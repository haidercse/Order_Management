<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Fruit</th>
                <th>Configuration</th>
                <th>Code</th>
                <th>Weight (kg)</th>
                <th>Default</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($boxes as $box)
                <tr>
                    <td>{{ $boxes->firstItem() + $loop->index }}</td>
                    <td>{{ $box->fruit->display_name ?: $box->fruit->name }}</td>
                    <td>{{ $box->name }}</td>
                    <td><strong>{{ $box->code }}</strong></td>
                    <td>{{ number_format((float) $box->weight_kg, 3) }}</td>
                    <td><span class="badge badge-{{ $box->is_default ? 'primary' : 'light' }}">{{ $box->is_default ? 'Default' : '—' }}</span></td>
                    <td><span class="badge badge-{{ $box->status ? 'success' : 'secondary' }}">{{ $box->status ? 'Active' : 'Inactive' }}</span></td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-sm btn-primary edit-box" data-id="{{ $box->id }}" title="Edit"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-danger delete-box" data-id="{{ $box->id }}" title="Delete"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">No box configurations found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $boxes->links() }}
