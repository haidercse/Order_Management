<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pedido - {{ $shop->name }} - {{ $order->order_date->format('d/m/Y') }}</title>
    <style>
        @page { size: A4 landscape; margin: 12mm; }
        * { box-sizing: border-box; }
        body { margin: 0; color: #111; font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #333; font-size: 11px; font-weight: bold; }
        .shop { text-align: center; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th { height: 20px; padding: 3px; border: 1px solid #555; background: #e8e8e8; font-size: 7px; }
        td { height: 18px; padding: 2px 3px; border: 1px solid #777; vertical-align: middle; }
        .weight, .quantity { width: 5%; text-align: center; }
        .name { width: 15%; overflow: hidden; text-align: left; white-space: nowrap; }
        .weight { background: #fff; }
        .quantity { font-weight: bold; }
        th.weight, th.quantity { width: 5%; }
        th.name { width: 15%; }
        .muted { color: #666; font-weight: normal; }
    </style>
</head>
<body>
    <div class="header">
        <span>FRESH LINK — PICKING SHEET</span>
        <span class="shop">{{ $shop->name }} ({{ $shop->code }})</span>
        <span>DATE: {{ $order->order_date->format('d/m/Y') }} · ORDER #{{ $order->id }}</span>
    </div>

    <table>
        <thead>
            <tr>
                @for ($column = 0; $column < 4; $column++)
                    <th class="weight">WEIGHT KG</th><th class="name">PRODUCT</th><th class="quantity">CAJAS</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($fruitRows as $row)
                <tr>
                    @foreach ($row as $fruit)
                        @if ($fruit)
                            @php($item = $itemsByFruit->get($fruit->id))
                            <td class="weight"></td>
                            <td class="name">{{ $fruit->display_name ?: $fruit->name }}</td>
                            <td class="quantity">{{ $item ? number_format((float) $item->quantity, 0) : '' }}</td>
                        @else
                            <td class="weight"></td><td class="name"></td><td class="quantity"></td>
                        @endif
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="12" class="muted">No fruits are configured.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
