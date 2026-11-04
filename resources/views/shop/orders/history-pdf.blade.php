<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order history — {{ $shop->name }}</title>
    <style>
        @page { margin: 18mm 14mm; }
        body { color: #203326; font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { margin: 0 0 5px; color: #1e7a38; font-size: 20px; }
        p { margin: 4px 0; }
        .header { margin-bottom: 18px; padding-bottom: 10px; border-bottom: 2px solid #2f9e4f; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 7px 8px; border-bottom: 1px solid #dbe8d5; text-align: left; vertical-align: top; }
        th { background: #f1f8ef; color: #46634d; font-size: 9px; text-transform: uppercase; }
        .number { width: 12%; text-align: right; white-space: nowrap; }
        .date { width: 18%; white-space: nowrap; }
        .order { width: 12%; white-space: nowrap; }
        .status { width: 16%; }
        .muted { color: #5f7565; }
        .empty { padding: 20px 8px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Shop Order History</h1>
        <p><strong>Shop:</strong> {{ $shop->name }} ({{ $shop->code }})</p>
        <p><strong>Dates:</strong> {{ $from->format('d M Y') }} – {{ $to->format('d M Y') }}</p>
    </div>
    <table>
        <thead>
            <tr><th class="date">Date</th><th class="order">Order</th><th class="status">Status</th><th>Fruit</th><th class="number">Cajas</th></tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $order->order_date->format('d M Y') }}</td>
                        <td>#{{ $order->id }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                        <td class="number">{{ (int) $item->quantity }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td class="empty muted" colspan="5">No submitted orders found for these dates.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
