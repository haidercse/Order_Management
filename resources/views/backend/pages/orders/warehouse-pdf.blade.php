<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Warehouse order - {{ $date }}</title>
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
        thead {
            display: table-header-group;
        }
        th, td {
            border: 1px solid #333;
            padding: 6px 5px;
            text-align: center;
            font-size: 11px;
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
        .note {
            margin-top: 8px;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BISMILLAHIR RAHMANIR RAHEEM</h2>
        <h3>WAREHOUSE : {{ $warehouseName }}</h3>
        <h3>FECHA : {{ \Illuminate\Support\Carbon::parse($date)->format('d/m/Y') }}</h3>
        <h3>SHOPS : {{ $orders->count() }}</h3>
    </div>

    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>NAME</th>
                <th>KILO</th>
                <th>T KILO</th>
                <th>TARA</th>
                <th>NETTO</th>
                <th>PRECIO</th>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pdfItems as $item)
                <tr>
                    <td>{{ number_format($item['quantity'], 0) }}</td>
                    <td class="name">
                        {{ $item['name'] }}
                        @if ($item['box_name'])
                            <small>({{ $item['box_name'] }})</small>
                        @endif
                    </td>
                    <td class="number">{{ $item['box_weight'] !== null ? number_format($item['box_weight'], 3) : '' }}</td>
                    <td class="number">{{ $item['total_weight'] !== null ? number_format($item['total_weight'], 3) : '' }}</td>
                    <td class="number">{{ $item['total_tare'] !== null ? number_format($item['total_tare'], 3) : '' }}</td>
                    <td class="number">{{ $item['net_weight'] !== null ? number_format($item['net_weight'], 3) : '' }}</td>
                    <td class="number">{{ $item['price'] !== null ? number_format($item['price'], 2) : '' }}</td>
                    <td class="number">{{ $item['total'] !== null ? number_format($item['total'], 2) : '' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No submitted orders with fruit items were found for this date.</td></tr>
            @endforelse
            @if ($pdfItems->isNotEmpty())
                <tr class="total-row">
                    <td colspan="7" class="number">TOTAL</td>
                    <td class="number">
                        {{ $pdfItems->every(fn ($item) => $item['total'] !== null) ? number_format($pdfItems->sum('total'), 2) : '' }}
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
    <p class="note">KILO and tare per box come from the box configuration. T KILO is the measured warehouse weight across submitted shops; TARA is the combined package tare and NETTO is T KILO minus TARA. PRECIO is the active price per kg for the order date.</p>
</body>
</html>
