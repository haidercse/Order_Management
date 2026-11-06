<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Warehouse order - {{ $shop->name }} - {{ $order->order_date->format('d/m/Y') }}</title>
    <style>
        @page { margin: 16mm 13mm; }
        body { color: #172033; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; }
        .header { margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #26364a; text-align: center; }
        .header h2 { margin: 0 0 9px; font-size: 17px; letter-spacing: .5px; }
        .header .meta { color: #526075; font-size: 10px; }
        .header .meta span { display: inline-block; margin: 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th, td { padding: 8px 7px; border: 1px solid #bcc6d2; text-align: center; }
        th { background: #eaf0f7; color: #26364a; font-size: 9px; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        td.name { text-align: left; font-weight: 600; }
        td.number, th.number { text-align: right; white-space: nowrap; }
        tfoot th { background: #eaf0f7; font-size: 11px; }
        .note { margin-top: 11px; color: #526075; font-size: 9px; }
        .grand-total { font-size: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BISMILLAHIR RAHMANIR RAHEEM</h2>
        <div class="meta">
            <span><strong>SHOP:</strong> {{ $shop->name }} ({{ $shop->code }})</span>
            <span><strong>DATE:</strong> {{ $order->order_date->format('d/m/Y') }}</span>
            <span><strong>ORDER:</strong> #{{ $order->id }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAME</th>
                <th class="number">KILO / BOX</th>
                <th class="number">T KILO</th>
                <th class="number">TARA / BOX</th>
                <th class="number">NETTO</th>
                <th class="number">PRICE / KG</th>
                <th class="number">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pdfItems as $item)
                <tr>
                    <td>{{ rtrim(rtrim(number_format($item['quantity'], 3, '.', ''), '0'), '.') }}</td>
                    <td class="name">{{ $item['name'] }}</td>
                    <td class="number">{{ $item['box_weight'] !== null ? number_format($item['box_weight'], 3) : '—' }}</td>
                    <td class="number">{{ $item['total_weight'] !== null ? number_format($item['total_weight'], 3) : '—' }}</td>
                    <td class="number">{{ $item['tare'] > 0 ? number_format($item['tare'], 3) : '—' }}</td>
                    <td class="number">{{ $item['net_weight'] !== null ? number_format($item['net_weight'], 3) : '—' }}</td>
                    <td class="number">{{ $item['price'] !== null ? number_format($item['price'], 2) : '—' }}</td>
                    <td class="number">{{ number_format($item['total'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" class="number">GRAND TOTAL ({{ $currency }})</th>
                <th class="number grand-total">
                    {{ $currency === 'EUR' ? '€' : $currency }}
                    {{ number_format(round($pdfItems->sum('total'), 0, PHP_ROUND_HALF_UP), 0) }}
                </th>
            </tr>
        </tfoot>
    </table>
    <p class="note">NO is the number of boxes. For fixed-weight boxes, T KILO = NO × KILO; otherwise T KILO is the saved scale weight. NETTO = T KILO − (NO × TARA). TOTAL is rounded to two decimals; the grand total is rounded to the nearest whole currency unit.</p>
</body>
</html>
