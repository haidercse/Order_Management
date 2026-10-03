<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Order</th>
                <th>Shop</th>
                <th>Order date</th>
                <th>Submitted</th>
                <th>Items</th>
                <th>Value</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                @php
                    $statusLabels = ['submitted' => 'Submitted', 'prepared' => 'Prepared', 'ready' => 'Ready', 'sent' => 'Delivered'];
                    $statusClass = ['submitted' => 'warning', 'prepared' => 'info', 'ready' => 'primary', 'sent' => 'success'][$order->status] ?? 'secondary';
                    $nextStatus = ['submitted' => 'prepared', 'prepared' => 'ready', 'ready' => 'sent'][$order->status] ?? null;
                    $nextLabel = ['prepared' => 'Mark prepared', 'ready' => 'Mark ready', 'sent' => 'Mark delivered'][$nextStatus] ?? null;
                    $orderValue = $order->items->sum(fn ($item) => (float) ($item->line_total ?? ((float) $item->quantity * (float) $item->unit_price)));
                @endphp
                <tr>
                    <td><strong>#{{ $order->id }}</strong></td>
                    <td>{{ $order->shop->name ?? 'Unknown shop' }}<small class="d-block text-muted">{{ $order->shop->code ?? '' }}</small></td>
                    <td>{{ $order->order_date->format('Y-m-d') }}</td>
                    <td>{{ $order->submitted_at?->format('H:i') ?? '—' }}</td>
                    <td>{{ $order->items_count }}</td>
                    <td>{{ number_format($orderValue, 2) }}</td>
                    <td><span class="badge badge-{{ $statusClass }}">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-outline-primary view-order" data-id="{{ $order->id }}">Details</button>
                        @if ($nextStatus)
                            <button type="button" class="btn btn-sm btn-success advance-order" data-id="{{ $order->id }}" data-status="{{ $nextStatus }}" data-label="{{ $nextLabel }}">{{ $nextLabel }}</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">No orders found for the selected filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $orders->links() }}
