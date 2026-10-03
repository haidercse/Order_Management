<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Fruit;
use App\Models\Order;
use App\Models\SystemSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $date = $this->reportDate($request);

        return view('backend.pages.reports.daily', $this->reportData($date));
    }

    public function pdf(Request $request)
    {
        $data = $this->reportData($this->reportDate($request));

        return Pdf::loadView('backend.pages.reports.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->download('daily-warehouse-report-' . $data['date'] . '.pdf');
    }

    public function excel(Request $request)
    {
        $data = $this->reportData($this->reportDate($request));
        $filename = 'daily-warehouse-report-' . $data['date'] . '.csv';

        return response()->streamDownload(function () use ($data): void {
            $output = fopen('php://output', 'w');
            if ($output === false) {
                throw new \RuntimeException('Unable to open report output stream.');
            }

            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Daily Warehouse Report', $data['date']]);
            fputcsv($output, ['Warehouse', $data['warehouseName']]);
            fputcsv($output, ['Currency', $data['currency']]);
            fputcsv($output, []);
            fputcsv($output, ['Fruit demand and available stock']);
            fputcsv($output, ['Fruit', 'Code', 'Demand', 'Unit', 'WH available', 'Shortage / excess', 'Estimated value', 'Currency']);

            foreach ($data['items'] as $item) {
                fputcsv($output, [
                    $this->safeCsvText($item['name']),
                    $this->safeCsvText($item['code']),
                    number_format($item['demand'], 3, '.', ''),
                    $this->safeCsvText($item['unit']),
                    number_format($item['available'], 3, '.', ''),
                    number_format($item['available'] - $item['demand'], 3, '.', ''),
                    number_format($item['value'], 2, '.', ''),
                    $this->safeCsvText($data['currency']),
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['Shop orders']);
            fputcsv($output, ['Order ID', 'Shop', 'Shop code', 'Submitted at', 'Status', 'Items', 'Order value', 'Currency']);
            foreach ($data['orders'] as $order) {
                fputcsv($output, [
                    $order->id,
                    $this->safeCsvText($order->shop->name ?? 'Unknown shop'),
                    $this->safeCsvText($order->shop->code ?? ''),
                    $order->submitted_at?->format('Y-m-d H:i:s') ?? '',
                    $this->safeCsvText($data['statusLabels'][$order->status] ?? ucfirst($order->status)),
                    $order->items->count(),
                    number_format($order->report_value, 2, '.', ''),
                    $this->safeCsvText($data['currency']),
                ]);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function reportDate(Request $request): string
    {
        return $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
        ])['date'] ?? today()->toDateString();
    }

    private function reportData(string $date): array
    {
        $demandRows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereDate('orders.order_date', $date)
            ->whereNotNull('orders.submitted_at')
            ->select('order_items.fruit_id')
            ->selectRaw('SUM(COALESCE(order_items.converted_kg, order_items.quantity)) AS demand_kg')
            ->selectRaw('SUM(COALESCE(order_items.line_total, order_items.quantity * order_items.unit_price, 0)) AS value')
            ->groupBy('order_items.fruit_id')
            ->get()
            ->keyBy('fruit_id');

        $stockByFruit = DB::table('inventory_stocks')
            ->where('status', 'active')
            ->select('fruit_id')
            ->selectRaw('SUM(quantity_kg - reserved_kg) AS available_kg')
            ->groupBy('fruit_id')
            ->pluck('available_kg', 'fruit_id');

        $fruits = Fruit::query()
            ->with(['defaultUnit', 'boxConfigurations' => fn ($query) => $query->where('is_default', true)])
            ->whereIn('id', $demandRows->keys())
            ->get()
            ->keyBy('id');

        $items = $demandRows->map(function ($row) use ($fruits, $stockByFruit): ?array {
            $fruit = $fruits->get($row->fruit_id);
            if (!$fruit) {
                return null;
            }

            $box = $fruit->boxConfigurations->first();
            $boxWeight = $box ? (float) $box->weight_kg : 0;
            $divisor = $boxWeight > 0 ? $boxWeight : 1;
            $demandKg = (float) $row->demand_kg;
            $availableKg = max(0, (float) ($stockByFruit[$fruit->id] ?? 0));

            return [
                'fruit_id' => $fruit->id,
                'name' => $fruit->display_name ?: $fruit->name,
                'code' => $fruit->code,
                'demand' => $demandKg / $divisor,
                'available' => $availableKg / $divisor,
                'unit' => $boxWeight > 0 ? 'caja' : ($fruit->defaultUnit->symbol ?? 'kg'),
                'value' => (float) $row->value,
            ];
        })->filter()->sortBy('name')->values();

        $orders = Order::query()
            ->with(['shop', 'items'])
            ->whereDate('order_date', $date)
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at')
            ->get();

        $orders->each(function (Order $order): void {
            $order->report_value = $order->items->sum(
                fn ($item) => (float) ($item->line_total ?? ((float) $item->quantity * (float) ($item->unit_price ?? 0)))
            );
        });

        $statusLabels = [
            'submitted' => 'Submitted',
            'prepared' => 'Prepared',
            'ready' => 'Ready',
            'sent' => 'Delivered',
        ];

        return [
            'date' => $date,
            'warehouseName' => SystemSetting::query()->where('key', 'warehouse_name')->value('value') ?: 'Warehouse',
            'currency' => SystemSetting::query()->where('key', 'currency')->value('value') ?: 'EUR',
            'items' => $items,
            'orders' => $orders,
            'statusLabels' => $statusLabels,
            'orderCount' => $orders->count(),
            'shopCount' => $orders->pluck('shop_id')->unique()->count(),
            'itemCount' => $orders->sum(fn ($order) => $order->items->count()),
            'totalValue' => $items->sum('value'),
            'shortageCount' => $items->filter(fn ($item) => $item['demand'] > $item['available'])->count(),
            'demandTotal' => $items->sum('demand'),
        ];
    }

    private function safeCsvText(?string $value): string
    {
        $value = $value ?? '';

        return preg_match('/^[\s\x00-\x1F]*[=+\-@]/u', $value) ? "'" . $value : $value;
    }
}
