<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Daily Warehouse Report - {{ $date }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { color: #202b3c; font-family: DejaVu Sans, sans-serif; font-size: 9px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        h2 { font-size: 13px; margin: 18px 0 8px; }
        .muted { color: #64748b; }
        .metrics { margin: 14px 0; width: 100%; }
        .metrics td { background: #f1f5f9; border: 1px solid #dce3ec; padding: 9px; width: 25%; }
        .metrics strong { display: block; font-size: 15px; margin-top: 4px; }
        table.data { border-collapse: collapse; width: 100%; }
        .data th, .data td { border: 1px solid #dce3ec; padding: 6px; text-align: left; }
        .data th { background: #eaf0f7; }
        .right { text-align: right !important; }
        .short { color: #b42318; font-weight: bold; }
        .sufficient { color: #167044; }
        .footer { color: #64748b; font-size: 8px; margin-top: 8px; }
    </style>
</head>
<body>
    <h1>Daily Warehouse Report</h1>
    <div class="muted">{{ $warehouseName }} · Order date: {{ $date }} · Generated: {{ now()->format('Y-m-d H:i') }}</div>

    <table class="metrics">
        <tr>
            <td>Submitted orders<strong>{{ number_format($orderCount) }}</strong></td>
            <td>Unique shops<strong>{{ number_format($shopCount) }}</strong></td>
            <td>Estimated value<strong>{{ $currency }} {{ number_format($totalValue, 2) }}</strong></td>
            <td>Shortage items<strong>{{ number_format($shortageCount) }}</strong></td>
        </tr>
    </table>

    <h2>Fruit demand and current stock availability</h2>
    <table class="data">
        <thead><tr><th>Fruit item</th><th>Demand KG</th><th>Demand Caja</th><th>Available</th><th>Shortage / excess</th><th>Price / KG</th><th>Price / Caja</th><th>Order value</th></tr></thead>
        <tbody>
            @forelse ($items as $item)
                @php
                    $difference = $item['available'] - $item['demand'];
                @endphp
                <tr>
                    <td>{{ $item['name'] }} ({{ $item['code'] }})</td>
                    <td>{{ number_format($item['demand_kg'], 3) }} kg</td>
                    <td>{{ number_format($item['demand_boxes'], 3) }} caja</td>
                    <td>{{ number_format($item['available'], 3) }} {{ $item['unit'] }}</td>
                    <td class="{{ $difference < 0 ? 'short' : 'sufficient' }}">{{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 3) }} {{ $item['unit'] }}</td>
                    <td>{{ $item['price_per_kg'] === null ? '—' : $currency . ' ' . number_format($item['price_per_kg'], 2) }}</td>
                    <td>{{ $item['price_per_box'] === null ? '—' : $currency . ' ' . number_format($item['price_per_box'], 2) }}</td>
                    <td>{{ $currency }} {{ number_format($item['value'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No submitted order demand for this date.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">Demand is shown in kilograms and ordered Caja. Stock and shortage use the default caja equivalent where available. Prices are effective for the selected order date; order value uses the submitted price snapshots.</div>

    <h2>Submitted shop orders</h2>
    <table class="data">
        <thead><tr><th>Order</th><th>Shop</th><th>Submitted</th><th>Items</th><th>Status</th><th>Value</th></tr></thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->shop->name ?? 'Unknown shop' }} ({{ $order->shop->code ?? '' }})</td>
                    <td>{{ $order->submitted_at?->format('H:i') ?? '—' }}</td>
                    <td>{{ $order->items->count() }}</td>
                    <td>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</td>
                    <td>{{ $currency }} {{ number_format($order->report_value, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6">No submitted shop orders for this date.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
