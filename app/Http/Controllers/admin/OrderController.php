<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryStock;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\SystemSetting;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with(['shop', 'items.fruit', 'items.unit', 'items.boxConfiguration'])
            ->whereNotNull('submitted_at')
            ->withCount('items');

        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:submitted,prepared,ready,sent'],
            'search' => ['nullable', 'string', 'max:150'],
        ]);

        if (!empty($filters['date'])) {
            $query->whereDate('order_date', $filters['date']);
        } else {
            $query->whereDate('order_date', $this->defaultOrderDate());
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
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

        return view('backend.pages.orders.index', compact('orders', 'filters'));
    }

    private function defaultOrderDate(): string
    {
        $days = (int) (SystemSetting::query()->where('key', 'order_day_offset')->value('value') ?: 1);
        $timezone = SystemSetting::query()->where('key', 'timezone')->value('value') ?: config('app.timezone');

        return CarbonImmutable::today($timezone)->addDays(max(1, $days))->toDateString();
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

    public function updateStatus(Request $request, int $id)
    {
        $data = $request->validate([
            'status' => ['required', 'in:prepared,ready,sent'],
        ]);

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
            $remainingKg = (float) ($item->converted_kg ?? $item->quantity);
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
            ->map(fn ($items) => $items->sum(fn ($item) => (float) ($item->converted_kg ?? $item->quantity)));

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
