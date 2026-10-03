<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Shop Name</th>
                <th>Manager</th>
                <th>Phone</th>
                <th>City</th>
                <th>Status</th>
                <th width="150">Action</th>
            </tr>

        </thead>

        <tbody>

            @forelse ($shops as $shop)

                <tr>

                    <td>
                        {{ $shops->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>{{ $shop->code }}</strong>
                    </td>

                    <td>
                        {{ $shop->name }}
                    </td>

                    <td>
                        {{ $shop->manager_name ?? '-' }}
                    </td>

                    <td>
                        {{ $shop->phone ?? '-' }}
                    </td>

                    <td>
                        {{ $shop->city ?? '-' }}
                    </td>

                    <td>

                        @if ($shop->status === 'active')

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn btn-sm btn-warning edit-shop"
                            data-id="{{ $shop->id }}"
                        >
                            <i class="fa fa-edit"></i>
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger delete-shop"
                            data-id="{{ $shop->id }}"
                        >
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="text-center py-4">

                        No shops found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{ $shops->links() }}