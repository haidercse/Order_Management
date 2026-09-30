@extends('backend.layouts.master')

@section('title', 'Manage Roles')

@section('admin-content')
    <div class="container my-4">

        <h3 class="mb-4 text-primary fw-bold text-center">Role Management</h3>

        {{-- Alerts --}}
        <div id="alertBox"></div>

        {{-- Add Button --}}
        <div class="text-end mb-3">
            <button class="btn btn-success" id="addRoleBtn">Add New Role</button>
        </div>

        {{-- Role Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Roles</div>
            <div class="card-body" id="roleTableWrapper">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Role Name</th>
                            <th>Permissions</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr id="role-{{ $role->id }}">
                                <td>{{ $role->name }}</td>
                                <td>{{ implode(', ', $role->permissions->pluck('name')->toArray()) }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-role" data-id="{{ $role->id }}">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm delete-role" data-id="{{ $role->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="roleModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Add Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="roleForm">
                        @csrf
                        <input type="hidden" id="role_id">

                        {{-- Role Name --}}
                        <div class="mb-3">
                            <label class="fw-semibold">Enter Role Name</label>
                            <input type="text" id="role_name" class="form-control" required>
                        </div>

                        {{-- Permissions --}}
                        <div class="mb-3">
                            <label class="fw-semibold">Permissions</label>

                            {{-- Select All --}}
                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="selectAll">
                                <label class="form-check-label fw-bold">Select All</label>
                            </div>

                            <hr>

                            {{-- Permission Groups --}}
                            @foreach ($permissions as $group => $perms)
                                <div class="border rounded p-2 mb-3">

                                    {{-- Parent Group --}}
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input group-check"
                                            data-group="{{ $group }}">
                                        <label class="form-check-label fw-bold">
                                            {{ ucfirst($group) }}
                                        </label>
                                    </div>

                                    {{-- Child Permissions --}}
                                    <div class="ms-4 mt-2">
                                        @foreach ($perms as $perm)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input perm-checkbox"
                                                    name="permissions[]" value="{{ $perm->name }}"
                                                    data-group="{{ $group }}">
                                                <label class="form-check-label">{{ $perm->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            @endforeach

                        </div>

                        <button class="btn btn-success w-100" id="saveBtn">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        $(document).ready(function() {

            // ALERT FUNCTION
            function showAlert(type, message) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                $('#alertBox').html(`
            <div class="alert ${alertClass} alert-dismissible fade show">
                ${message}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
            }

            // ADD ROLE
            $('#addRoleBtn').click(function() {
                $('#modalTitle').text("Add Role");
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('.perm-checkbox, .group-check, #selectAll').prop('checked', false);
                $('#roleModal').modal('show');
            });

            // SELECT ALL
            $('#selectAll').on('change', function() {
                $('.perm-checkbox, .group-check').prop('checked', $(this).is(':checked'));
            });

            // GROUP SELECT
            $('.group-check').on('change', function() {
                let group = $(this).data('group');
                $(`.perm-checkbox[data-group="${group}"]`).prop('checked', $(this).is(':checked'));
            });

            // EDIT ROLE
            $(document).on('click', '.edit-role', function() {

                let id = $(this).data('id');

                $.get(`/admin/roles/${id}/permissions`, function(res) {

                    $('#modalTitle').text("Edit Role");
                    $('#role_id').val(res.role.id);
                    $('#role_name').val(res.role.name);

                    // Reset all
                    $('.perm-checkbox, .group-check, #selectAll').prop('checked', false);

                    // Check assigned permissions
                    res.assigned.forEach(function(p) {
                        $(`.perm-checkbox[value="${p}"]`).prop('checked', true);
                    });

                    $('#roleModal').modal('show');
                });
            });

            // SAVE / UPDATE
            $('#roleForm').submit(function(e) {
                e.preventDefault();

                let id = $('#role_id').val();
                let url = id ? `/admin/roles/update/${id}` : "{{ route('admin.roles.store.ajax') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        name: $('#role_name').val(),
                        permissions: $('input[name="permissions[]"]:checked').map(function() {
                            return this.value;
                        }).get(),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $('#roleModal').modal('hide');
                        showAlert('success', res.message);
                        reloadTable();
                    }
                });
            });

            // DELETE ROLE
            $(document).on('click', '.delete-role', function() {
                if (!confirm("Delete this role?")) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/roles/delete/${id}`,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        $(`#role-${id}`).remove();
                        showAlert('success', res.message);
                    }
                });
            });

            // RELOAD TABLE
            function reloadTable() {
                $.get("{{ route('admin.roles.index') }}", function(data) {
                    $('#roleTableWrapper').html($(data).find('#roleTableWrapper').html());
                });
            }

        });
    </script>
@endpush
