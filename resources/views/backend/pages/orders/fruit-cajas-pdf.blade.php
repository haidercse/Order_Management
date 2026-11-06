<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fruit Cajas - {{ $date }}</title>
    <style>
        @page { margin: 12mm 14mm; }
        body {
            color: #172033;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
        }
        .header {
            margin-bottom: 12px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 17pt;
        }
        .header p {
            margin: 0;
            color: #526075;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th, td {
            padding: 5px 9px;
            border: 1px solid #aeb8c6;
        }
        th {
            background: #eaf0f7;
            font-size: 9pt;
            text-align: left;
        }
        th:last-child, td:last-child {
            width: 24%;
            text-align: right;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .total td {
            background: #eaf0f7;
            font-weight: bold;
        }
        .empty {
            color: #526075;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>Fruit Purchase List</h1>
        <p>Order date: {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }} · Shops: {{ $shopCount }}</p>
    </header>

    <table>
        <thead>
            <tr>
                <th>Fruit</th>
                <th>Cajas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ rtrim(rtrim(number_format((float) $item->cajas, 3, '.', ''), '0'), '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="empty">No Caja orders were submitted for this date.</td>
                </tr>
            @endforelse
            @if ($items->isNotEmpty())
                <tr class="total">
                    <td>Total Cajas</td>
                    <td>{{ rtrim(rtrim(number_format((float) $items->sum('cajas'), 3, '.', ''), '0'), '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
