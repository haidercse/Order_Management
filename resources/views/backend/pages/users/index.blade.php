@extends('backend.layouts.master')

@section('title', 'User Management')

@section('admin-content')
    <div class="container-fluid my-4">
        <h3 class="mb-4 text-primary fw-bold">User Management</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">Add User</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" name="name" type="text" class="form-control"
                                value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control"
                                value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" name="password" type="password" class="form-control" minlength="8"
                                required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->name }}" @selected(old('role') === $role->name)>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Add User</button>
                </form>
                <small class="text-muted d-block mt-2">
                    Shop manager accounts are created from Shop Management so they can be linked to a shop.
                </small>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Users</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->pluck('name')->join(', ') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
