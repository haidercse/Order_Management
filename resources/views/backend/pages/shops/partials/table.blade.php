<div class="table-responsive">
    <style>
        .shop-actions-heading { min-width: 135px; }
        .shop-actions { white-space: nowrap; }
        .shop-action-buttons { display: inline-flex; align-items: center; justify-content: flex-start; gap: 6px; white-space: nowrap; }
        .shop-action-buttons .btn { display: inline-flex; width: 32px; height: 32px; align-items: center; justify-content: center; padding: 0; }
    </style>

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-dark">

            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Shop Name</th>
                <th>Manager</th>
                <th>Manager Login Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Status</th>
                <th class="shop-actions-heading">Action</th>
            </tr>

        </thead>

        <tbody>

            @forelse ($shops as $shop)

                <tr>

                    <td class="shop-actions">
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
                        {{ $shop->users->first()?->email ?? 'No login account' }}
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

                        <div class="shop-action-buttons">

                        @if ($shop->users->first())
                            <button
                                type="button"
                                class="btn btn-sm btn-info reset-manager-password"
                                data-id="{{ $shop->id }}"
                                title="Issue a temporary password"
                            >
                                <i class="fa fa-key"></i>
                            </button>
                        @endif

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

                        </div>
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9" class="text-center py-4">

                        No shops found.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{ $shops->links() }}