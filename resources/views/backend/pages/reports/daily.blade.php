@extends('backend.layouts.master')

@section('title', 'Daily Warehouse Report')

@section('admin-content')
<div class="container-fluid my-4">
    <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
        <div>
            <h3 class="mb-1 text-primary font-weight-bold">Daily Warehouse Report</h3>
            <p class="mb-0 text-muted">{{ $warehouseName }} · Submitted shop orders and current stock availability</p>
        </div>
        <form method="GET" action="{{ route('admin.reports.daily') }}" class="form-inline mt-3 mt-md-0">
            <label for="reportDate" class="mr-2">Order date</label>
            <input type="date" id="reportDate" name="date" class="form-control mr-2" value="{{ $date }}" required>
            <button class="btn btn-primary" type="submit">View report</button>
        </form>
    </div>

    <div class="d-flex flex-wrap mb-3">
        @can('report.pdf')
            <a class="btn btn-outline-danger mr-2 mb-2" href="{{ route('admin.reports.pdf', ['date' => $date]) }}"><i class="fa fa-file-pdf-o"></i> Download PDF</a>
        @endcan
        @can('report.excel')
            <a class="btn btn-outline-success mb-2" href="{{ route('admin.reports.excel', ['date' => $date]) }}"><i class="fa fa-file-excel-o"></i> Download Excel CSV</a>
        @endcan
    </div>

    <div class="row">
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body"><small class="text-muted">Submitted orders</small><h3 class="mt-2 mb-0">{{ number_format($orderCount) }}</h3><small class="text-muted">{{ number_format($shopCount) }} unique shops</small></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body"><small class="text-muted">Ordered line items</small><h3 class="mt-2 mb-0">{{ number_format($itemCount) }}</h3><small class="text-muted">Across submitted orders</small></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body"><small class="text-muted">Estimated order value</small><h3 class="mt-2 mb-0">{{ $currency }} {{ number_format($totalValue, 2) }}</h3><small class="text-muted">Saved order line prices</small></div></div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3 mb-3">
            <div class="card shadow-sm h-100"><div class="card-body"><small class="text-muted">Shortage items</small><h3 class="mt-2 mb-0 {{ $shortageCount ? 'text-danger' : 'text-success' }}">{{ number_format($shortageCount) }}</h3><small class="text-muted">Demand above available stock</small></div></div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <span>Fruit demand &amp; warehouse availability</span>
            <span>{{ $date }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Fruit item</th><th>Demand</th><th>WH stock available</th><th>Shortage / excess</th><th>Price / kg</th><th>Price / caja</th><th>Order value</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        @php
                            $difference = $item['available'] - $item['demand'];
                        @endphp
                        <tr class="{{ $difference < 0 ? 'table-warning' : '' }}">
                            <td><strong>{{ $item['name'] }}</strong><small class="d-block text-muted">{{ $item['code'] }}</small></td>
                            <td>{{ number_format($item['demand_kg'], 3) }} kg<br><small class="text-muted">{{ number_format($item['demand_boxes'], 3) }} caja</small></td>
                            <td>{{ number_format($item['available'], 3) }} {{ $item['unit'] }}</td>
                            <td class="{{ $difference < 0 ? 'text-danger font-weight-bold' : 'text-success' }}">{{ $difference > 0 ? '+' : '' }}{{ number_format($difference, 3) }} {{ $item['unit'] }}</td>
                            <td>{{ $item['price_per_kg'] === null ? '—' : $currency . ' ' . number_format($item['price_per_kg'], 2) }}</td>
                            <td>{{ $item['price_per_box'] === null ? '—' : $currency . ' ' . number_format($item['price_per_box'], 2) }}</td>
                            <td>{{ $currency }} {{ number_format($item['value'], 2) }}</td>
                            <td>
                                @if ($difference < 0)
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.inventory.index', ['fruit_id' => $item['fruit_id'] ?? null, 'movement' => 'purchase']) }}">Receive stock</a>
                                @else
                                    <span class="text-success">Sufficient</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4">No submitted orders found for {{ $date }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted small">Demand is shown in both kilograms and ordered Caja. Stock and shortage are shown in the fruit's default Caja equivalent where available. Prices are effective on the selected date; order value uses each submitted line's saved price.</div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Submitted shop orders</div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light"><tr><th>Order</th><th>Shop</th><th>Submitted</th><th>Items</th><th>Status</th><th>Value</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>{{ $order->shop->name ?? 'Unknown shop' }}<small class="d-block text-muted">{{ $order->shop->code ?? '' }}</small></td>
                            <td>{{ $order->submitted_at?->format('H:i') ?? '—' }}</td>
                            <td>{{ $order->items->count() }}</td>
                            <td>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</td>
                            <td>{{ $currency }} {{ number_format($order->report_value, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4">No submitted shop orders for this date.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
