<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SystemSetting;
use App\Support\OrderCajasSummaryBuilder;
use App\Support\WarehousePdfItemBuilder;
use App\Support\WarehouseWeightAllocator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $defaultDate = $this->defaultOrderDate();
        $query = Order::query()
            ->with(['shop', 'items.fruit', 'items.unit', 'items.boxConfiguration'])
            ->whereNotNull('submitted_at')
            ->withCount('items');

        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:submitted,prepared,ready,sent'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        if (! empty($filters['date'])) {
            $query->whereDate('order_date', $filters['date']);
        } else {
            $query->whereDate('order_date', $defaultDate);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('shop', function ($shop) use ($search) {
                $shop->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderByDesc('submitted_at')->orderByDesc('id')->paginate(20)->withQueryString();
        $filters = array_filter($filters, fn ($value) => $value !== null);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('backend.pages.orders.partials.table', compact('orders'))->render(),
            ]);
        }

        return view('backend.pages.orders.index', compact('orders', 'filters', 'defaultDate'));
    }

    private function defaultOrderDate(): string
    {
        $days = (int) (SystemSetting::query()->where('key', 'order_day_offset')->value('value') ?: 1);
        $timezone = SystemSetting::query()->where('key', 'timezone')->value('value') ?: config('app.timezone');

        return CarbonImmutable::today($timezone)->addDays(max(1, $days))->toDateString();
    }

    public function warehouseSheet(Request $request)
    {
        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? $this->defaultOrderDate();

        return view('backend.pages.orders.warehouse-sheet', $this->warehouseSheetData($date));
    }

    private function warehouseSheetData(string $date): array
    {
        $orders = Order::query()
            ->with([
                'shop',
                'items.boxConfiguration',
                'items.fruit.boxConfigurations' => fn ($query) => $query->where('status', true)->orderByDesc('is_default'),
                'items.fruit.prices' => fn ($query) => $query->where('is_active', true)
                    ->whereDate('effective_from', '<=', $date)
                    ->where(fn ($price) => $price->whereNull('effective_to')->orWhereDate('effective_to', '>=', $date))
                    ->with('unit')
                    ->orderByDesc('effective_from'),
            ])
            ->whereDate('order_date', $date)
            ->where('status', 'submitted')
            ->whereNotNull('submitted_at')
            ->get();

        $items = $orders
            ->flatMap(fn (Order $order) => $order->items->map(fn ($item) => [
                'item' => $item,
                'order' => $order,
            ]))
            ->groupBy(fn ($row) => $row['item']->fruit_id)
            ->map(function ($rows): array {
                $firstItem = $rows->first()['item'];
                $allWeightsSaved = $rows->every(fn ($row) => $row['item']->warehouse_weight_kg !== null);

                return [
                    'fruit_id' => (int) $firstItem->fruit_id,
                    'name' => $firstItem->fruit->display_name ?: $firstItem->fruit->name,
                    'quantity' => $rows->sum(fn ($row) => (float) $row['item']->quantity),
                    'shop_count' => $rows->pluck('order.shop_id')->unique()->count(),
                    'weight' => $allWeightsSaved
                        ? $rows->sum(fn ($row) => (float) $row['item']->warehouse_weight_kg)
                        : null,
                    'complete' => $allWeightsSaved,
                ];
            })
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values();

        return [
            'date' => $date,
            'orders' => $orders,
            'items' => $items,
            'allWeightsSaved' => $items->isNotEmpty() && $items->every(fn ($item) => $item['complete']),
        ];
    }

    public function saveWarehouseWeights(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'weights' => ['required', 'array', 'min:1'],
            'weights.*' => ['required', 'numeric', 'decimal:0,3', 'gt:0', 'max:999999.999'],
        ]);

        DB::transaction(function () use ($data): void {
            $orders = Order::query()
                ->with('items')
                ->whereDate('order_date', $data['date'])
                ->where('status', 'submitted')
                ->whereNotNull('submitted_at')
                ->lockForUpdate()
                ->get();

            $itemsByFruit = $orders->flatMap->items->groupBy('fruit_id');
            $expectedFruitIds = $itemsByFruit->keys()->map(fn ($id) => (string) $id)->sort()->values();
            $receivedFruitIds = collect(array_keys($data['weights']))->map(fn ($id) => (string) $id)->sort()->values();

            if ($expectedFruitIds->isEmpty() || $expectedFruitIds->all() !== $receivedFruitIds->all()) {
                throw ValidationException::withMessages([
                    'weights' => 'The submitted fruit list changed. Refresh the warehouse sheet and try again.',
                ]);
            }

            foreach ($itemsByFruit as $fruitId => $fruitItems) {
                $totalGrams = (int) round((float) $data['weights'][$fruitId] * 1000);
                $totalTareKg = $fruitItems->sum(fn ($item) => (float) $item->quantity * (float) $item->unit_tare_kg);
                if ($totalGrams / 1000 <= $totalTareKg) {
                    throw ValidationException::withMessages([
                        'weights' => 'The measured total weight must be greater than the combined tare weight.',
                    ]);
                }

                try {
                    $allocatedGrams = (new WarehouseWeightAllocator)->distribute($fruitItems, $totalGrams);
                } catch (\InvalidArgumentException $exception) {
                    throw ValidationException::withMessages([
                        'weights' => 'The total weight must provide at least 0.001 kg for each shop order item.',
                    ]);
                }

                foreach ($fruitItems as $item) {
                    $item->warehouse_weight_kg = number_format($allocatedGrams[$item->id] / 1000, 3, '.', '');
                    $item->save();
                }
            }
        });

        return redirect()
            ->route('admin.orders.warehouse-sheet', ['date' => $data['date']])
            ->with('success', 'Consolidated warehouse weights saved and distributed across shop orders by Caja quantity.');
    }

    public function warehousePdf(Request $request)
    {
        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? $this->defaultOrderDate();

        $sheet = $this->warehouseSheetData($date);
        $sheet['pdfItems'] = (new WarehousePdfItemBuilder)->build($sheet['orders']);
        $sheet['warehouseName'] = SystemSetting::query()->where('key', 'warehouse_name')->value('value') ?: 'Warehouse';

        return Pdf::loadView('backend.pages.orders.warehouse-pdf', $sheet)
            ->setPaper('a4')
            ->download("warehouse-picking-sheet-{$date}.pdf");
    }

    public function fruitCajasPdf(Request $request, OrderCajasSummaryBuilder $summaryBuilder)
    {
        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? $this->defaultOrderDate();

        $shopCount = Order::query()
            ->whereDate('order_date', $date)
            ->whereNotNull('submitted_at')
            ->distinct('shop_id')
            ->count('shop_id');

        return Pdf::loadView('backend.pages.orders.fruit-cajas-pdf', [
            'date' => $date,
            'shopCount' => $shopCount,
            'items' => $summaryBuilder->build($date),
        ])
            ->setPaper('a4', 'portrait')
            ->download("fruit-cajas-{$date}.pdf");
    }

    public function fruitCajasExcel(Request $request, OrderCajasSummaryBuilder $summaryBuilder)
    {
        $date = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? $this->defaultOrderDate();
        $items = $summaryBuilder->build($date);

        return response()->streamDownload(function () use ($items): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Unable to create the Fruit Cajas Excel export.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Fruit', 'Cajas']);
            foreach ($items as $item) {
                fputcsv($output, [
                    $item->name,
                    rtrim(rtrim(number_format((float) $item->cajas, 3, '.', ''), '0'), '.'),
                ]);
            }

            fclose($output);
        }, "fruit-cajas-{$date}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function show(int $id)
    {
        $order = Order::query()
            ->with(['shop', 'items.fruit', 'items.unit', 'items.boxConfiguration'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'html' => view('backend.pages.orders.partials.details', compact('order'))->render(),
        ]);
    }

    public function updateWeights(Request $request, int $id)
    {
        $weights = $request->validate([
            'weights' => ['sometimes', 'array'],
            'weights.*' => ['required', 'numeric', 'gt:0', 'max:999999.999'],
        ])['weights'] ?? [];

        DB::transaction(function () use ($id, $weights): void {
            $order = Order::query()->with('items')->lockForUpdate()->findOrFail($id);
            if ($order->status !== 'submitted') {
                throw ValidationException::withMessages([
                    'weights' => 'Warehouse weights can only be changed before the order is marked prepared.',
                ]);
            }

            $expectedIds = $order->items->pluck('id')->map(fn ($itemId) => (int) $itemId)->sort()->values();
            $receivedIds = collect(array_keys($weights))->map(fn ($itemId) => (int) $itemId)->sort()->values();
            if ($expectedIds->all() !== $receivedIds->all()) {
                throw ValidationException::withMessages([
                    'weights' => 'Submit a scale weight for every item in this order.',
                ]);
            }

            foreach ($order->items as $item) {
                $item->warehouse_weight_kg = $weights[$item->id];
                $item->save();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Scale weights saved.',
        ]);
    }

    public function deliveryPdf(int $id)
    {
        $order = Order::query()
            ->with(['shop', 'items.fruit'])
            ->whereNotNull('submitted_at')
            ->findOrFail($id);

        abort_if($order->items->isEmpty(), 422, 'This order has no fruit items to deliver.');

        $warehouseName = SystemSetting::query()->where('key', 'warehouse_name')->value('value') ?: 'Warehouse';

        return Pdf::loadView('backend.pages.orders.delivery-pdf', compact('order', 'warehouseName'))
            ->setPaper('a4')
            ->download("delivery-order-{$order->id}.pdf");
    }

    public function finalPdf(int $id)
    {
        $order = Order::query()
            ->with([
                'shop',
                'items.unit',
                'items.boxConfiguration',
                'items.fruit.boxConfigurations',
            ])
            ->whereNotNull('submitted_at')
            ->findOrFail($id);
        $orderDate = $order->order_date->toDateString();
        $order->load([
            'items.fruit.prices' => fn ($query) => $query
                ->where('is_active', true)
                ->whereDate('effective_from', '<=', $orderDate)
                ->where(fn ($price) => $price->whereNull('effective_to')->orWhereDate('effective_to', '>=', $orderDate))
                ->with('unit')
                ->orderByDesc('effective_from'),
        ]);

        abort_if(
            $order->items->isEmpty() || $order->items->contains(fn (OrderItem $item) => $item->warehouse_weight_kg === null),
            422,
            'Enter and save a scale weight for every item before downloading the final PDF.'
        );

        $pdfItems = (new WarehousePdfItemBuilder)->build(collect([$order]));
        abort_if(
            $pdfItems->contains(fn ($item) => $item['total'] === null),
            422,
            'A valid per-kilogram price is required for every item before downloading the final PDF.'
        );

        $shop = $order->shop;
        $currency = SystemSetting::query()->where('key', 'currency')->value('value') ?: 'EUR';

        return Pdf::loadView('test2', compact('shop', 'order', 'currency', 'pdfItems'))
            ->setPaper('a4', 'landscape')
            ->download("warehouse-order-{$order->id}.pdf");
    }

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:prepared,ready,sent'],
        ]);

        $requiredPermission = match ($data['status']) {
            'prepared' => 'order.prepare',
            'ready' => 'order.ready',
            'sent' => 'order.send',
        };
        abort_unless($request->user()->can($requiredPermission), 403);

        DB::transaction(function () use ($id, $data): void {
            $order = Order::query()->with('items')->lockForUpdate()->findOrFail($id);
            $allowedNext = [
                'submitted' => 'prepared',
                'prepared' => 'ready',
                'ready' => 'sent',
            ];

            if (($allowedNext[$order->status] ?? null) !== $data['status']) {
                throw ValidationException::withMessages([
                    'status' => 'This order can no longer move to the selected status. Refresh the order list and try again.',
                ]);
            }

            if ($data['status'] === 'prepared') {
                if ($order->items->isEmpty() || $order->items->contains(
                    fn (OrderItem $item) => $item->warehouse_weight_kg === null
                )) {
                    throw ValidationException::withMessages([
                        'weights' => 'Enter and save a scale weight for every item before marking this order prepared.',
                    ]);
                }

                $this->assertOrderStockAvailable($order);
            }

            if ($data['status'] === 'sent') {
                $this->issueOrderStock($order);
                $order->sent_at = now();
            } elseif ($data['status'] === 'prepared') {
                $order->prepared_at = now();
            } elseif ($data['status'] === 'ready') {
                $order->ready_at = now();
            }

            $order->status = $data['status'];
            $order->save();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Order status updated successfully.',
        ]);
    }

    private function issueOrderStock(Order $order): void
    {
        if ($order->items->isEmpty()) {
            throw ValidationException::withMessages([
                'stock' => 'An order without items cannot be marked delivered.',
            ]);
        }

        foreach ($order->items as $item) {
            $remainingKg = (float) ($item->warehouse_weight_kg ?? $item->converted_kg ?? $item->quantity);
            if ($remainingKg <= 0) {
                throw ValidationException::withMessages([
                    'stock' => 'Order item quantity must be greater than zero before it can be sent.',
                ]);
            }

            $stocks = InventoryStock::query()
                ->where('fruit_id', $item->fruit_id)
                ->where('status', 'active')
                ->whereRaw('(quantity_kg - reserved_kg) > 0')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $availableKg = $stocks->sum(fn ($stock) => max(0, (float) $stock->quantity_kg - (float) $stock->reserved_kg));
            if ($availableKg + 0.0005 < $remainingKg) {
                $fruitName = $item->fruit->display_name ?: $item->fruit->name;
                throw ValidationException::withMessages([
                    'stock' => "Insufficient stock for {$fruitName}; this order was not sent.",
                ]);
            }

            foreach ($stocks as $stock) {
                if ($remainingKg <= 0.0005) {
                    break;
                }

                $availableForStockKg = max(0, (float) $stock->quantity_kg - (float) $stock->reserved_kg);
                if ($availableForStockKg <= 0) {
                    continue;
                }

                $issuedKg = min($remainingKg, $availableForStockKg);
                $kgPerNativeUnit = (float) $stock->quantity > 0
                    ? (float) $stock->quantity_kg / (float) $stock->quantity
                    : 0;
                if ($kgPerNativeUnit <= 0) {
                    continue;
                }

                $issuedQuantity = round($issuedKg / $kgPerNativeUnit, 3);
                $actualIssuedKg = round($issuedQuantity * $kgPerNativeUnit, 3);
                if (
                    $issuedQuantity <= 0
                    || $actualIssuedKg > $remainingKg + 0.0005
                    || $actualIssuedKg > $availableForStockKg + 0.0005
                ) {
                    continue;
                }

                $stock->quantity = max(0, (float) $stock->quantity - $issuedQuantity);
                $stock->quantity_kg = max(0, (float) $stock->quantity_kg - $actualIssuedKg);
                $stock->save();

                InventoryTransaction::create([
                    'fruit_id' => $stock->fruit_id,
                    'unit_id' => $stock->unit_id,
                    'box_configuration_id' => $stock->box_configuration_id,
                    'type' => 'issue',
                    'quantity' => $issuedQuantity,
                    'quantity_kg' => $actualIssuedKg,
                    'unit_price' => $item->unit_price,
                    'total_price' => null,
                    'reference_order_id' => $order->id,
                    'created_by' => auth()->id(),
                    'notes' => "Issued for shop order #{$order->id}.",
                ]);

                $remainingKg = round($remainingKg - $actualIssuedKg, 3);
            }

            if ($remainingKg > 0.0005) {
                throw ValidationException::withMessages([
                    'stock' => 'The order could not be fully allocated from warehouse stock.',
                ]);
            }
        }
    }

    private function assertOrderStockAvailable(Order $order): void
    {
        if ($order->items->isEmpty()) {
            throw ValidationException::withMessages([
                'stock' => 'An order without items cannot be marked prepared.',
            ]);
        }

        $demandByFruit = $order->items
            ->groupBy('fruit_id')
            ->map(fn ($items) => $items->sum(fn ($item) => (float) ($item->warehouse_weight_kg ?? $item->converted_kg ?? $item->quantity)));

        foreach ($demandByFruit as $fruitId => $demandKg) {
            $stocks = InventoryStock::query()
                ->where('fruit_id', $fruitId)
                ->where('status', 'active')
                ->whereRaw('(quantity_kg - reserved_kg) > 0')
                ->lockForUpdate()
                ->get();

            $availableKg = $stocks->sum(fn ($stock) => max(0, (float) $stock->quantity_kg - (float) $stock->reserved_kg));
            if ($availableKg + 0.0005 < $demandKg) {
                $fruit = $order->items->firstWhere('fruit_id', $fruitId)->fruit;
                $fruitName = $fruit->display_name ?: $fruit->name;
                throw ValidationException::withMessages([
                    'stock' => "Insufficient stock for {$fruitName}; this order cannot be prepared.",
                ]);
            }
        }
    }
}
