<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Fruit</th>
                <th>Unit / configuration</th>
                <th>On hand</th>
                <th>Reserved</th>
                <th>Available</th>
                <th>Availability</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stocks as $stock)
                @php
                    $available = max(0, (float) $stock->quantity - (float) $stock->reserved_quantity);
                    $unitName = $stock->unit->symbol ?? $stock->unit->name ?? '';
                @endphp
                <tr>
                    <td>{{ $stocks->firstItem() + $loop->index }}</td>
                    <td>
                        <strong>{{ $stock->fruit->display_name ?: $stock->fruit->name }}</strong>
                        <small class="d-block text-muted">{{ $stock->fruit->code }}</small>
                    </td>
                    <td>
                        {{ $unitName }}
                        @if ($stock->boxConfiguration)
                            <small class="d-block text-muted">{{ $stock->boxConfiguration->name }} ({{ number_format((float) $stock->boxConfiguration->weight_kg, 3) }} kg)</small>
                        @endif
                    </td>
                    <td>{{ number_format((float) $stock->quantity, 3) }} {{ $unitName }}</td>
                    <td>{{ number_format((float) $stock->reserved_quantity, 3) }} {{ $unitName }}</td>
                    <td class="font-weight-bold">{{ number_format($available, 3) }} {{ $unitName }}</td>
                    <td>{{ number_format((float) $stock->quantity_kg, 3) }} kg</td>
                    <td><span class="badge badge-{{ $stock->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($stock->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">No inventory stock found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $stocks->links() }}
