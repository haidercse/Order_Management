<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Fruit</th>
                <th>Category</th>
                <th>Default unit</th>
                <th>Order units</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($fruits as $fruit)
                <tr>
                    <td>{{ $fruits->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $fruit->code }}</strong></td>
                    <td>
                        {{ $fruit->display_name ?: $fruit->name }}
                        @if ($fruit->display_name && $fruit->display_name !== $fruit->name)
                            <small class="d-block text-muted">{{ $fruit->name }}</small>
                        @endif
                    </td>
                    <td>{{ $fruit->category->name ?? '—' }}</td>
                    <td>{{ $fruit->defaultUnit->symbol ?? $fruit->defaultUnit->name ?? '—' }}</td>
                    <td>
                        @if ($fruit->allow_kg)<span class="badge badge-info">KG</span>@endif
                        @if ($fruit->allow_box)<span class="badge badge-primary">BOX</span>@endif
                    </td>
                    <td>
                        <span class="badge badge-{{ $fruit->status ? 'success' : 'secondary' }}">
                            {{ $fruit->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-primary edit-fruit" data-id="{{ $fruit->id }}" title="Edit"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-fruit" data-id="{{ $fruit->id }}" title="Delete"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">No fruits found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $fruits->links() }}
