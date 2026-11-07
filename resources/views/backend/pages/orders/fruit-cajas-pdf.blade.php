<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fruit Cajas - {{ $date }}</title>
    <style>
        @page { margin: 9mm 10mm; }
        body {
            color: #172033;
            font-family: DejaVu Sans, sans-serif;
            font-size: 7.5pt;
        }
        .header {
            margin-bottom: 7px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 2px;
            font-size: 13pt;
        }
        .header p {
            margin: 0;
            color: #526075;
            font-size: 7.5pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        th, td {
            padding: 3px 5px;
            border: 1px solid #aeb8c6;
        }
        th {
            background: #eaf0f7;
            font-size: 7pt;
            text-align: center;
        }
        td {
            width: 25%;
            overflow-wrap: anywhere;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        .total td {
            background: #eaf0f7;
            font-weight: bold;
            text-align: right;
        }
        .empty {
            color: #526075;
            text-align: center;
        }
        .cajas {
            white-space: nowrap;
            font-weight: bold;
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
                @for ($column = 0; $column < 4; $column++)
                    <th>Fruit / Cajas</th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @forelse ($items->chunk(4) as $row)
                <tr>
                    @foreach ($row as $item)
                        <td>
                            {{ $item->name }}
                            <span class="cajas">- {{ rtrim(rtrim(number_format((float) $item->cajas, 3, '.', ''), '0'), '.') }} cajas</span>
                        </td>
                    @endforeach
                    @for ($column = $row->count(); $column < 4; $column++)
                        <td></td>
                    @endfor
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="empty">No Caja orders were submitted for this date.</td>
                </tr>
            @endforelse
            @if ($items->isNotEmpty())
                <tr class="total">
                    <td colspan="4">Total Cajas: {{ rtrim(rtrim(number_format((float) $items->sum('cajas'), 3, '.', ''), '0'), '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
