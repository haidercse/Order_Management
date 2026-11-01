@php
    $weightsComplete = $order->items->isNotEmpty()
        && $order->items->every(fn ($item) => $item->warehouse_weight_kg !== null);
    $canEditWeights = $order->status === 'submitted';
@endphp

<div class="mb-3">
    <h5 class="mb-1">{{ $order->shop->name ?? 'Unknown shop' }}</h5>
    <div class="text-muted">Order #{{ $order->id }} · {{ $order->shop->code ?? '' }} · {{ $order->order_date->format('Y-m-d') }}</div>
    <div class="text-muted">Status: {{ ucfirst($order->status) }} · Submitted: {{ $order->submitted_at?->format('Y-m-d H:i') ?? '—' }}</div>
</div>

<div class="d-flex align-items-center justify-content-between mb-2">
    <h6 class="mb-0">Order items and warehouse weights</h6>
    @if ($weightsComplete)
        <a class="btn btn-sm btn-primary" href="{{ route('admin.orders.final-pdf', $order->id) }}" target="_blank" rel="noopener">Download final PDF</a>
    @else
        <button class="btn btn-sm btn-secondary" type="button" disabled title="Enter and save every warehouse weight first">Final PDF requires all weights</button>
    @endif
</div>

<form id="orderWeightsForm" action="{{ route('admin.orders.weights', $order->id) }}">
    @csrf
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr><th>Fruit</th><th>Cajas</th><th>Box</th><th>Estimated kg</th><th>Warehouse total weight (kg)</th></tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                        <td>{{ number_format((float) $item->quantity, 0) }}</td>
                        <td>{{ $item->boxConfiguration->name ?? $item->unit->symbol ?? $item->unit->name }}</td>
                        <td>{{ $item->converted_kg !== null ? number_format((float) $item->converted_kg, 3) : '—' }}</td>
                        <td>
                            <input
                                class="form-control form-control-sm"
                                type="number"
                                name="weights[{{ $item->id }}]"
                                min="0.001"
                                max="999999.999"
                                step="0.001"
                                value="{{ $item->warehouse_weight_kg }}"
                                required
                                aria-label="Warehouse weight in kilograms for {{ $item->fruit->display_name ?: $item->fruit->name }}"
                                @disabled(!$canEditWeights)
                            >
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">This order has no items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($canEditWeights && $order->items->isNotEmpty())
        <button class="btn btn-sm btn-success" type="submit">Save warehouse weights</button>
    @elseif (!$canEditWeights)
        <p class="text-muted mb-0">Weights cannot be changed after an order is marked prepared.</p>
    @endif
</form>

<div class="mt-3 text-muted">Order total: {{ number_format($order->items->sum(fn ($item) => (float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price))), 2) }}</div>
