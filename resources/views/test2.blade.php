<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Warehouse order - {{ $shop->name }} - {{ $order->order_date->format('d/m/Y') }}</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { color: #202020; font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; }
        .header { margin-bottom: 16px; text-align: center; }
        .header h2 { margin: 0 0 5px; font-size: 16px; }
        .header p { margin: 3px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 5px; border: 1px solid #444; text-align: center; }
        th { background: #e8e8e8; }
        td.name { text-align: left; }
        td.number, th.number { text-align: right; }
        tfoot th { background: #f0f0f0; }
        .note { margin-top: 12px; color: #555; font-size: 9px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>BISMILLAHIR RAHMANIR RAHEEM</h2>
        <p><strong>TIENDA:</strong> {{ $shop->name }} ({{ $shop->code }})</p>
        <p><strong>FECHA:</strong> {{ $order->order_date->format('d/m/Y') }} · <strong>ORDER:</strong> #{{ $order->id }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAME</th>
                <th class="number">KILO / CAJA</th>
                <th class="number">T KILO</th>
                <th class="number">TARA</th>
                <th class="number">NETTO</th>
                <th class="number">PRECIO</th>
                <th class="number">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ number_format((float) $item->quantity, 0) }}</td>
                    <td class="name">{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                    <td class="number">{{ $item->unit_weight_kg !== null ? number_format((float) $item->unit_weight_kg, 3) : '—' }}</td>
                    <td class="number">{{ number_format((float) $item->warehouse_weight_kg, 3) }}</td>
                    <td class="number">—</td>
                    <td class="number">—</td>
                    <td class="number">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="number">{{ number_format((float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price)), 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="7" class="number">TOTAL ({{ $currency }})</th>
                <th class="number">{{ number_format($order->items->sum(fn ($item) => (float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price))), 2) }}</th>
            </tr>
        </tfoot>
    </table>
    <p class="note">T KILO shows the actual total weight entered by the warehouse. TARA and NETTO are not recorded in the order system.</p>
</body>
</html>
