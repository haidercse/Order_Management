@extends('backend.layouts.master')

@section('title', 'All Shops Warehouse Sheet')

@section('admin-content')
<div class="container-fluid my-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1 text-primary font-weight-bold">All Shops Warehouse Sheet</h3>
            <p class="text-muted mb-0">{{ $orders->count() }} submitted shops · {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}</p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('admin.orders.index', ['date' => $date]) }}" class="btn btn-outline-secondary">Back to orders</a>
            <a href="{{ route('admin.orders.warehouse-pdf', ['date' => $date]) }}" class="btn btn-outline-primary">Download / Print PDF</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.orders.warehouse-sheet') }}" class="card card-body mb-3">
        <div class="form-row align-items-end">
            <div class="col-sm-4 col-md-3">
                <label for="warehouseDate">Order date</label>
                <input type="date" id="warehouseDate" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="col-auto mt-2 mt-sm-0"><button class="btn btn-secondary" type="submit">Show date</button></div>
        </div>
    </form>

    @if ($items->isEmpty())
        <div class="alert alert-info">No submitted orders with fruit items were found for this date.</div>
    @else
        <div class="alert alert-info">
            Enter the total measured warehouse kilograms for each fruit once. On save, each shop's weight is distributed according to its Caja quantity; the saved weights are then used by each shop order and warehouse stock calculations.
        </div>

        <form method="POST" action="{{ route('admin.orders.warehouse-weights.save') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            @if ($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Fruit</th>
                                <th class="text-right">Total Cajas</th>
                                <th class="text-right">Shops</th>
                                <th class="text-right">Total warehouse weight (kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td class="text-right">{{ number_format($item['quantity'], 0) }}</td>
                                    <td class="text-right">{{ $item['shop_count'] }}</td>
                                    <td>
                                        <input
                                            class="form-control text-right"
                                            type="number"
                                            name="weights[{{ $item['fruit_id'] }}]"
                                            min="0.001"
                                            max="999999.999"
                                            step="0.001"
                                            value="{{ $item['weight'] !== null ? number_format($item['weight'], 3, '.', '') : old('weights.' . $item['fruit_id']) }}"
                                            required
                                            aria-label="Total warehouse weight in kilograms for {{ $item['name'] }}"
                                        >
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
                    <span class="text-muted">Weights are split to 0.001 kg precision so the shop allocations add up to the entered total.</span>
                    <button class="btn btn-success mt-2 mt-md-0" type="submit">Save all weights for {{ $orders->count() }} shops</button>
                </div>
            </div>
        </form>
    @endif
</div>
@endsection
