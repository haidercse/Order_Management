@php
    $weightsComplete = $order->items->isNotEmpty()
        && $order->items->every(fn ($item) => (float) $item->unit_weight_kg > 0 || $item->warehouse_weight_kg !== null);
    $scaleItems = $order->items->filter(fn ($item) => (float) $item->unit_weight_kg <= 0);
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
        <button class="btn btn-sm btn-secondary" type="button" disabled title="Enter and save scale weights for variable-weight items first">Final PDF requires scale weights</button>
    @endif
</div>

<form id="orderWeightsForm" action="{{ route('admin.orders.weights', $order->id) }}">
    @csrf
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr><th>Fruit</th><th>NO (Cajas)</th><th>Box</th><th>KILO / box</th><th>T KILO</th><th>TARA / box</th><th>Scale weight (kg)</th></tr>
            </thead>
            <tbody>
                @forelse ($order->items as $item)
                    <tr>
                        <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                        <td>{{ number_format((float) $item->quantity, 0) }}</td>
                        <td>{{ $item->boxConfiguration->name ?? $item->unit->symbol ?? $item->unit->name }}</td>
                        <td>
                            {{ (float) $item->unit_weight_kg > 0 ? number_format((float) $item->unit_weight_kg, 3) : '—' }}
                        </td>
                        <td>
                            {{ (float) $item->unit_weight_kg > 0
                                ? number_format((float) $item->quantity * (float) $item->unit_weight_kg, 3)
                                : ($item->warehouse_weight_kg !== null ? number_format((float) $item->warehouse_weight_kg, 3) : '—') }}
                        </td>
                        <td>{{ (float) ($item->unit_tare_kg ?? 0) > 0 ? number_format((float) $item->unit_tare_kg, 3) : '—' }}</td>
                        <td>
                            @if ((float) $item->unit_weight_kg <= 0)
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
                            @else
                                <span class="text-muted">Fixed weight</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">This order has no items.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($canEditWeights && $scaleItems->isNotEmpty())
        <button class="btn btn-sm btn-success" type="submit">Save warehouse weights</button>
    @elseif ($canEditWeights && $order->items->isNotEmpty())
        <p class="text-muted mb-0">All items use fixed box weights; T KILO is calculated from Cajas × KILO.</p>
    @elseif (!$canEditWeights)
        <p class="text-muted mb-0">Scale weights cannot be changed after an order is marked prepared.</p>
    @endif
</form>

<div class="mt-3 text-muted">Fixed-weight items use Cajas × KILO. Variable-weight items use the saved scale weight.</div>
