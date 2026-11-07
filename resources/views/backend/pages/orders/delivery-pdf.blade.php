<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Delivery order - {{ $order->shop->name }} - {{ $order->order_date->format('d/m/Y') }}</title>
    <style>
        @page { margin: 18mm 15mm; }
        body { color: #172033; font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
        .header { margin-bottom: 22px; padding-bottom: 14px; border-bottom: 2px solid #26364a; }
        .header h1 { margin: 0 0 5px; font-size: 19px; text-align: center; }
        .warehouse { margin-bottom: 15px; color: #526075; text-align: center; }
        .shop-details { margin-bottom: 14px; }
        .shop-details div { margin: 3px 0; }
        .meta { margin-top: 12px; color: #526075; }
        table { width: 100%; border-collapse: collapse; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th, td { padding: 9px 8px; border: 1px solid #bcc6d2; }
        th { background: #eaf0f7; color: #26364a; text-align: left; }
        th.number, td.number { width: 25%; text-align: right; }
        tbody tr:nth-child(even) { background: #f8fafc; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DELIVERY NOTE</h1>
        <div class="warehouse">{{ $warehouseName }}</div>
        <div class="shop-details">
            <div><strong>SHOP:</strong> {{ $order->shop->name }} ({{ $order->shop->code }})</div>
            @if ($order->shop->manager_name)
                <div><strong>CONTACT:</strong> {{ $order->shop->manager_name }}</div>
            @endif
            @if ($order->shop->address || $order->shop->city)
                <div><strong>ADDRESS:</strong> {{ collect([$order->shop->address, $order->shop->city])->filter()->implode(', ') }}</div>
            @endif
            @if ($order->shop->phone)
                <div><strong>PHONE:</strong> {{ $order->shop->phone }}</div>
            @endif
            @if ($order->shop->email)
                <div><strong>EMAIL:</strong> {{ $order->shop->email }}</div>
            @endif
        </div>
        <div class="meta">
            <strong>DELIVERY DATE:</strong> {{ $order->order_date->format('d/m/Y') }}
            &nbsp; | &nbsp;
            <strong>ORDER:</strong> #{{ $order->id }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fruit</th>
                <th class="number">Cajas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                    <td class="number">{{ number_format((float) $item->quantity, 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
