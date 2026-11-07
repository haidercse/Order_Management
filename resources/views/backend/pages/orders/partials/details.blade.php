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
    <h6 class="mb-0">Order details and scale weights</h6>
    <div class="d-flex align-items-center">
        @if ($order->items->isNotEmpty())
            <a class="btn btn-sm btn-outline-primary mr-2" href="{{ route('admin.orders.delivery-pdf', $order->id) }}" target="_blank" rel="noopener">Delivery PDF</a>
        @endif
        @if ($weightsComplete)
            <a class="btn btn-sm btn-primary" href="{{ route('admin.orders.final-pdf', $order->id) }}" target="_blank" rel="noopener">Generate PDF</a>
        @else
            <span class="text-muted">Save all scale weights to generate the PDF.</span>
        @endif
    </div>
</div>

<form id="orderWeightsForm" action="{{ route('admin.orders.weights', $order->id) }}">
    @csrf
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr><th>Fruit</th><th>Cajas</th><th>Scale weight (kg)</th></tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                        <td>{{ number_format((float) $item->quantity, 0) }}</td>
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
                                aria-label="Scale weight in kilograms for {{ $item->fruit->display_name ?: $item->fruit->name }}"
                                @disabled(!$canEditWeights)
                            >
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center">This order has no items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($canEditWeights && $order->items->isNotEmpty())
        <button class="btn btn-sm btn-success" type="submit">Save scale weights</button>
    @elseif (!$canEditWeights)
        <p class="text-muted mb-0">Scale weights cannot be changed after an order is marked prepared.</p>
    @endif
</form>
