<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            @canany(['user.edit', 'user.delete'])
                <th>Actions</th>
            @endcanany
        </tr>
    </thead>
    <tbody>
        @forelse ($users as $user)
            <tr data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                data-role="{{ $user->roles->pluck('name')->first() }}">
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                @canany(['user.edit', 'user.delete'])
                    <td class="text-nowrap">
                        @can('user.edit')
                            <button type="button" class="btn btn-warning btn-sm edit-user" data-id="{{ $user->id }}">
                                Edit
                            </button>
                        @endcan
                        @can('user.delete')
                            @unless ($user->hasRole('super-admin'))
                            <button type="button" class="btn btn-danger btn-sm delete-user" data-id="{{ $user->id }}">
                                Delete
                            </button>
                            @endunless
                        @endcan
                    </td>
                @endcanany
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">No admin users found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
