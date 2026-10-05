<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Shop order #{{ $order->id }}</title>
    <style>
        body { color: #203326; font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { margin: 0 0 6px; color: #1e7a38; font-size: 23px; }
        p { margin: 4px 0; }
        .header { margin-bottom: 22px; padding-bottom: 14px; border-bottom: 2px solid #2f9e4f; }
        .details { margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 9px 8px; border-bottom: 1px solid #dbe8d5; text-align: left; }
        th { background: #f1f8ef; color: #46634d; font-size: 10px; text-transform: uppercase; }
        .number { text-align: right; }
        .notes { margin-top: 18px; padding: 10px; background: #f4faf1; }
        .muted { color: #5f7565; }
    </style>
</head>
<body>
    <div class="header">
        <h1>FreshLink — Shop Order</h1>
        <p class="muted">Order #{{ $order->id }} · {{ $order->order_date->format('d M Y') }}</p>
    </div>
    <div class="details">
        <p><strong>Shop:</strong> {{ $shop->name }} ({{ $shop->code }})</p>
        <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
        <p><strong>Submitted:</strong> {{ $order->submitted_at?->format('d M Y, H:i') ?? '—' }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Fruit</th>
                <th class="number">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                    <td class="number">{{ (int) $item->quantity }} Cajas</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($order->notes)
        <div class="notes"><strong>Note for the warehouse:</strong><br>{{ $order->notes }}</div>
    @endif
</body>
</html>
