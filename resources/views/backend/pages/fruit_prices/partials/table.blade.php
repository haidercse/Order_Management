<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Fruit</th>
                <th>Unit / box</th>
                <th>Price</th>
                <th>Valid from</th>
                <th>Valid to</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prices as $price)
                <tr>
                    <td>{{ $prices->firstItem() + $loop->index }}</td>
                    <td>
                        {{ $price->fruit->display_name ?: $price->fruit->name }}
                        <small class="d-block text-muted">{{ $price->fruit->code }}</small>
                    </td>
                    <td>
                        {{ $price->unit->name ?? '—' }}
                        @if ($price->boxConfiguration)
                            <small class="d-block text-muted">{{ $price->boxConfiguration->name }} ({{ number_format((float) $price->boxConfiguration->weight_kg, 3) }} kg)</small>
                        @endif
                    </td>
                    <td><strong>{{ number_format((float) $price->price, 2) }} {{ $price->currency }}</strong></td>
                    <td>{{ $price->effective_from->format('Y-m-d') }}</td>
                    <td>{{ $price->effective_to?->format('Y-m-d') ?? 'Open ended' }}</td>
                    <td><span class="badge badge-{{ $price->is_active ? 'success' : 'secondary' }}">{{ $price->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-primary edit-price" data-id="{{ $price->id }}" title="Edit"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-price" data-id="{{ $price->id }}" title="Delete"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">No fruit prices found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $prices->links() }}
