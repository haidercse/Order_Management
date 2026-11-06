@extends('backend.layouts.master')

@section('title', 'User Management')

@section('admin-content')
    <div class="container-fluid my-4">
        <h3 class="mb-4 text-primary fw-bold">User Management</h3>
        <div id="userAlert"></div>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <span>Admin Users</span>
                @can('user.create')
                    <button type="button" class="btn btn-light btn-sm" id="addUserBtn">Add User</button>
                @endcan
            </div>
            <div class="card-body table-responsive" id="userTableWrapper">
                @include('backend.pages.users.partials.table', ['users' => $users])
            </div>
        </div>
    </div>

    @canany(['user.create', 'user.edit'])
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalTitle"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="userModalTitle">Add User</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            @csrf
                            <input type="hidden" id="user_id">
                            <div class="form-group">
                                <label for="user_name">Name</label>
                                <input id="user_name" name="name" type="text" class="form-control" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label for="user_email">Email</label>
                                <input id="user_email" name="email" type="email" class="form-control" maxlength="255" required>
                            </div>
                            <div class="form-group">
                                <label for="user_password">Password <span id="passwordHint"></span></label>
                                <input id="user_password" name="password" type="password" class="form-control" minlength="8">
                            </div>
                            <div class="form-group">
                                <label for="user_role">Role</label>
                                <select id="user_role" name="role" class="form-control" required>
                                    <option value="">-- Select Role --</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-block" id="saveUserBtn">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endcanany
@endsection

@push('scripts')
    <script>
        $(function () {
            const indexUrl = @json(route('admin.users.index'));
            const storeUrl = @json(route('admin.users.store'));
            const updateUrl = @json(route('admin.users.update.ajax', ['id' => '__ID__']));
            const deleteUrl = @json(route('admin.users.destroy.ajax', ['id' => '__ID__']));
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });

            function showUserAlert(type, message) {
                $('<div>', {
                    class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`,
                    role: 'alert'
                })
                    .text(message)
                    .append($('<button>', {
                        type: 'button',
                        class: 'close',
                        'data-dismiss': 'alert',
                        'aria-label': 'Close'
                    }).append('<span aria-hidden="true">&times;</span>'))
                    .appendTo('#userAlert');
            }

            function requestError(xhr, fallback) {
                const errors = xhr.responseJSON && xhr.responseJSON.errors;
                showUserAlert('error', errors
                    ? Object.values(errors).flat().join(' ')
                    : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
            }

            function loadUsers() {
                $.get(indexUrl)
                    .done(response => $('#userTableWrapper').html(response.html))
                    .fail(xhr => requestError(xhr, 'Unable to load users.'));
            }

            $('#addUserBtn').on('click', function () {
                $('#userForm')[0].reset();
                $('#user_id').val('');
                $('#user_password').prop('required', true);
                $('#passwordHint').text('(required)');
                $('#userModalTitle').text('Add User');
                $('#saveUserBtn').text('Add User');
                $('#userModal').modal('show');
            });

            $('#userForm').on('submit', function (event) {
                event.preventDefault();
                const id = $('#user_id').val();
                $.ajax({
                    url: id ? updateUrl.replace('__ID__', id) : storeUrl,
                    method: 'POST',
                    data: $(this).serialize()
                })
                    .done(response => {
                        $('#userModal').modal('hide');
                        showUserAlert('success', response.message);
                        loadUsers();
                    })
                    .fail(xhr => requestError(xhr, 'Unable to save user.'));
            });

            $(document).on('click', '.edit-user', function () {
                const row = $(this).closest('tr');
                $('#userForm')[0].reset();
                $('#user_id').val($(this).data('id'));
                $('#user_name').val(row.data('name'));
                $('#user_email').val(row.data('email'));
                $('#user_role').val(row.data('role'));
                $('#user_password').prop('required', false);
                $('#passwordHint').text('(leave blank to keep current password)');
                $('#userModalTitle').text('Edit User');
                $('#saveUserBtn').text('Update User');
                $('#userModal').modal('show');
            });

            $(document).on('click', '.delete-user', function () {
                const id = $(this).data('id');
                if (!window.confirm('Delete this user?')) return;

                $.ajax({
                    url: deleteUrl.replace('__ID__', id),
                    method: 'DELETE'
                })
                    .done(response => {
                        showUserAlert('success', response.message);
                        loadUsers();
                    })
                    .fail(xhr => requestError(xhr, 'Unable to delete user.'));
            });
        });
    </script>
@endpush
