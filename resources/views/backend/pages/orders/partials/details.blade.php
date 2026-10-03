<div class="mb-3">
    <h5 class="mb-1">{{ $order->shop->name ?? 'Unknown shop' }}</h5>
    <div class="text-muted">Order #{{ $order->id }} · {{ $order->shop->code ?? '' }} · {{ $order->order_date->format('Y-m-d') }}</div>
    <div class="text-muted">Status: {{ ucfirst($order->status) }} · Submitted: {{ $order->submitted_at?->format('Y-m-d H:i') ?? '—' }}</div>
</div>
<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="thead-light"><tr><th>Fruit</th><th>Quantity</th><th>Unit / box</th><th>Line total</th><th>Notes</th></tr></thead>
        <tbody>
            @forelse ($order->items as $item)
                <tr>
                    <td>{{ $item->fruit->display_name ?: $item->fruit->name }}</td>
                    <td>{{ number_format((float) $item->quantity, 3) }}</td>
                    <td>
                        {{ $item->unit->symbol ?? $item->unit->name }}
                        @if ($item->boxConfiguration)<small class="d-block text-muted">{{ $item->boxConfiguration->name }}</small>@endif
                    </td>
                    <td>{{ $item->line_total !== null ? number_format((float) $item->line_total, 2) : '—' }}</td>
                    <td>{{ $item->notes ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">This order has no items.</td></tr>
            @endforelse
        </tbody>
        @if ($order->items->isNotEmpty())
            <tfoot><tr><th colspan="3" class="text-right">Order total</th><th colspan="2">{{ number_format($order->items->sum(fn ($item) => (float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price))), 2) }}</th></tr></tfoot>
        @endif
    </table>
</div>
