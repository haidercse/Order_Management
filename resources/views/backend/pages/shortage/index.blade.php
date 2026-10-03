@extends('backend.layouts.master')

@section('title', 'Warehouse Shortages')

@section('admin-content')
<div class="container-fluid my-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1 text-primary font-weight-bold">Shortage / Purchase Planning</h3>
            <p class="text-muted mb-0">Outstanding submitted orders compared with available warehouse stock for {{ $date }}.</p>
        </div>
        <div class="d-flex flex-wrap align-items-center mt-2 mt-md-0">
            <form method="GET" action="{{ route('admin.shortage.index') }}" class="form-inline mr-2">
                @if ($showAll)<input type="hidden" name="show_all" value="1">@endif
                <label for="shortageDate" class="mr-2">Delivery date</label>
                <input id="shortageDate" name="date" type="date" class="form-control mr-2" value="{{ $date }}">
                <button class="btn btn-primary" type="submit">View</button>
            </form>
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-outline-primary"><i class="fa fa-cubes"></i> Open inventory</a>
        </div>
    </div>
    <div class="alert {{ $shortageCount ? 'alert-warning' : 'alert-success' }}">
        <strong>{{ $shortageCount ? $shortageCount . ' fruit item(s) need purchasing.' : 'No shortages detected.' }}</strong>
        Demand is shown in the default caja equivalent when configured, otherwise in the fruit's default unit. Delivered orders are excluded.
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center">
            <span>Items with shortages: <strong>{{ $shortageCount }}</strong></span>
            <a href="{{ route('admin.shortage.index', ['date' => $date, 'show_all' => !$showAll]) }}" class="btn btn-sm btn-outline-secondary">
                {{ $showAll ? 'Show shortages only' : 'Show all demanded fruit' }}
            </a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Outstanding demand vs available stock</div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr><th>Fruit item</th><th>Total demand</th><th>WH stock available</th><th>Shortage / excess</th><th>Action required</th></tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="{{ $item['shortage'] > 0 ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $item['fruit']->display_name ?: $item['fruit']->name }}</strong>
                                <small class="d-block text-muted">{{ $item['fruit']->code }}</small>
                            </td>
                            <td>{{ number_format($item['demand'], 3) }} {{ $item['unit'] }}</td>
                            <td>{{ number_format($item['available'], 3) }} {{ $item['unit'] }}</td>
                            <td class="{{ $item['shortage'] > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">
                                @if ($item['shortage'] > 0)
                                    −{{ number_format($item['shortage'], 3) }} {{ $item['unit'] }}
                                @else
                                    +{{ number_format($item['available'] - $item['demand'], 3) }} {{ $item['unit'] }}
                                @endif
                            </td>
                            <td>
                                @if ($item['shortage'] > 0)
                                    <a class="btn btn-sm btn-warning" href="{{ route('admin.inventory.index', ['fruit_id' => $item['fruit']->id, 'movement' => 'purchase']) }}">
                                        <i class="fa fa-cart-plus"></i> Receive purchase
                                    </a>
                                @else
                                    <span class="text-success">Sufficient stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4">No outstanding shop order demand.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
