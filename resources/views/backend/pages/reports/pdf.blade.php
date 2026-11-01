<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Daily Warehouse Report - {{ $date }}</title>
    <style>
        @page { margin: 20px; }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 18px;
        }
        .header h3 {
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: center;
            font-size: 13px;
        }
        th {
            background-color: #e8e8e8;
            font-weight: bold;
        }
        td.name {
            text-align: left;
            padding-left: 10px;
        }
        td.number, th.number {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        .short {
            color: red;
            font-weight: bold;
        }
        .section-title {
            margin: 12px 0 6px;
            font-size: 14px;
        }
        .note {
            margin: 6px 0 12px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BISMILLAHIR RAHMANIR RAHEEM</h2>
        <h3>WAREHOUSE : {{ $warehouseName }}</h3>
        <h3>FECHA : {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <h3>ORDERS : {{ number_format($orderCount) }} · SHOPS : {{ number_format($shopCount) }} · SHORTAGES : {{ number_format($shortageCount) }}</h3>
    </div>

    <div class="section">
        <h3 class="section-title">Fruit demand and current stock availability</h3>
        <table>
            <thead><tr><th>Fruit item</th><th>Demand KG</th><th>Demand Caja</th><th>Available</th><th>Shortage / excess</th><th>Price / KG</th><th>Price / Caja</th><th>Order value</th></tr></thead>
            <tbody>
                @forelse ($items as $item)
                    @php
                        $difference = $item['available'] - $item['demand'];
                    @endphp
                    <tr>
                        <td class="name">{{ $item['name'] }} ({{ $item['code'] }})</td>
                        <td class="number">{{ number_format($item['demand_kg'], 3) }} kg</td>
                        <td class="number">{{ number_format($item['demand_boxes'], 3) }} caja</td>
                        <td class="number">{{ number_format($item['available'], 3) }} {{ $item['unit'] }}</td>
                        <td class="number {{ $difference < 0 ? 'short' : '' }}">{{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 3) }} {{ $item['unit'] }}</td>
                        <td class="number">{{ $item['price_per_kg'] === null ? '—' : $currency . ' ' . number_format($item['price_per_kg'], 2) }}</td>
                        <td class="number">{{ $item['price_per_box'] === null ? '—' : $currency . ' ' . number_format($item['price_per_box'], 2) }}</td>
                        <td class="number">{{ $currency }} {{ number_format($item['value'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">No submitted order demand for this date.</td></tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="7" class="number">TOTAL ({{ $currency }})</td>
                    <td class="number">{{ number_format($totalValue, 2) }}</td>
                </tr>
            </tbody>
        </table>
        <p class="note">Demand is shown in kilograms and ordered Caja. Stock and shortage use the default Caja equivalent where available. Prices are effective for the selected order date; order value uses submitted price snapshots.</p>
    </div>

    <div class="section">
        <h3 class="section-title">Submitted shop orders</h3>
        <table>
            <thead><tr><th>Order</th><th>Shop</th><th>Submitted</th><th>Items</th><th>Status</th><th class="number">Value</th></tr></thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td class="name">{{ $order->shop->name ?? 'Unknown shop' }} ({{ $order->shop->code ?? '' }})</td>
                        <td>{{ $order->submitted_at?->format('H:i') ?? '—' }}</td>
                        <td>{{ $order->items->count() }}</td>
                        <td>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</td>
                        <td class="number">{{ $currency }} {{ number_format($order->report_value, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No submitted shop orders for this date.</td></tr>
                @endforelse
                <tr class="total-row">
                    <td colspan="5" class="number">TOTAL ({{ $currency }})</td>
                    <td class="number">{{ number_format($orders->sum('report_value'), 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
