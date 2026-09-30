@extends('backend.layouts.master')

@section('title', 'Manage Permissions')

@section('admin-content')
<div class="container my-4">

    <h3 class="mb-4 text-primary fw-bold text-center">Permission Management</h3>

    <div id="alertBox"></div>

    {{-- Top Bar --}}
    <div class="d-flex justify-content-between mb-3">

        {{-- Group Filter --}}
        <div class="w-25">
            <select id="groupFilter" class="form-control">
                <option value="">All Groups</option>
                @foreach ($groups as $group)
                    <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Add Button --}}
        <button class="btn btn-success" id="addPermissionBtn">Add New Permission</button>
    </div>

    {{-- Permission Table --}}
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Permissions</div>
        <div class="card-body" id="permissionTableWrapper">

            <table class="table table-bordered text-center" id="permissionTable">
                <thead>
                    <tr>
                        <th>Permission Name</th>
                        <th>Group</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $group => $perms)
                        @foreach ($perms as $perm)
                            <tr id="permission-{{ $perm->id }}">
                                <td>{{ $perm->name }}</td>
                                <td>{{ $perm->group_name ?: '-' }}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-permission"
                                        data-id="{{ $perm->id }}"
                                        data-name="{{ $perm->name }}"
                                        data-group="{{ $perm->group_name }}">
                                        Edit
                                    </button>

                                    <button class="btn btn-danger btn-sm delete-permission"
                                        data-id="{{ $perm->id }}">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>

{{-- Modal --}}
<div class="modal fade" id="permissionModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTitle">Add Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="permissionForm">
                    @csrf
                    <input type="hidden" id="permission_id">

                    {{-- MULTIPLE PERMISSION INPUT --}}
                    <label class="fw-semibold">Permission Names</label>
                    <div id="permissionInputs">

                        <div class="input-group mb-2 permission-row">
                            <input type="text" class="form-control perm-input" placeholder="Permission name" required>
                            <button type="button" class="btn btn-success add-row">+</button>
                        </div>

                    </div>

                    {{-- GROUP DROPDOWN --}}
                    <div class="mb-3 mt-3">
                        <label class="fw-semibold">Permission Group</label>

                        <select id="group_name_select" class="form-control">
                            <option value="">-- Select Group --</option>

                            @foreach ($groups as $group)
                                <option value="{{ $group }}">{{ ucfirst($group) }}</option>
                            @endforeach

                            <option value="__custom">+ Create New Group</option>
                        </select>
                    </div>

                    {{-- CUSTOM GROUP --}}
                    <div class="mb-3 d-none" id="customGroupWrapper">
                        <label class="fw-semibold">New Group Name</label>
                        <input type="text" id="custom_group_name" class="form-control" placeholder="e.g. invoice">
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

    // Initialize DataTable
    let table = $('#permissionTable').DataTable();

    // Group Filter
    $('#groupFilter').on('change', function() {
        let value = $(this).val();
        table.column(1).search(value).draw();
    });

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

    // ADD PERMISSION
    $('#addPermissionBtn').click(function() {
        $('#modalTitle').text("Add Permission");
        $('#permissionForm')[0].reset();
        $('#permission_id').val('');
        $('#permissionInputs').html(`
            <div class="input-group mb-2 permission-row">
                <input type="text" class="form-control perm-input" placeholder="Permission name" required>
                <button type="button" class="btn btn-success add-row">+</button>
            </div>
        `);
        $('#group_name_select').val('');
        $('#customGroupWrapper').addClass('d-none');
        $('#permissionModal').modal('show');
    });

    // ADD NEW INPUT ROW
    $(document).on('click', '.add-row', function() {
        $('#permissionInputs').append(`
            <div class="input-group mb-2 permission-row">
                <input type="text" class="form-control perm-input" placeholder="Permission name" required>
                <button type="button" class="btn btn-danger remove-row">X</button>
            </div>
        `);
    });

    // REMOVE INPUT ROW
    $(document).on('click', '.remove-row', function() {
        $(this).closest('.permission-row').remove();
    });

    // GROUP DROPDOWN LOGIC
    $('#group_name_select').on('change', function() {
        let value = $(this).val();
        if (value === '__custom') {
            $('#customGroupWrapper').removeClass('d-none');
        } else {
            $('#customGroupWrapper').addClass('d-none');
        }
    });

    // SAVE / UPDATE
    $('#permissionForm').submit(function(e) {
        e.preventDefault();

        let id = $('#permission_id').val();
        let url = id ? `/admin/permissions/update/${id}` : "{{ route('admin.permissions.store.ajax') }}";

        let groupName = $('#group_name_select').val();
        if (groupName === '__custom') {
            groupName = $('#custom_group_name').val();
        }

        let names = $('.perm-input').map(function() {
            return $(this).val();
        }).get();

        $.ajax({
            url: url,
            method: "POST",
            data: {
                names: names,
                name: names[0],
                group_name: groupName,
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#permissionModal').modal('hide');
                showAlert('success', res.message);
                reloadTable();
            },
            error: function(xhr) {
                let msg = "Something went wrong";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                showAlert('error', msg);
            }
        });
    });

    // EDIT PERMISSION
    $(document).on('click', '.edit-permission', function() {

        $('#modalTitle').text("Edit Permission");

        $('#permission_id').val($(this).data('id'));

        $('#permissionInputs').html(`
            <div class="input-group mb-2 permission-row">
                <input type="text" class="form-control perm-input" value="${$(this).data('name')}" required>
            </div>
        `);

        let group = $(this).data('group');

        if (group) {
            $('#group_name_select').val(group);
            $('#customGroupWrapper').addClass('d-none');
        } else {
            $('#group_name_select').val('');
            $('#customGroupWrapper').addClass('d-none');
        }

        $('#permissionModal').modal('show');
    });

    // DELETE PERMISSION
    $(document).on('click', '.delete-permission', function() {
        if (!confirm("Delete this permission?")) return;

        let id = $(this).data('id');

        $.ajax({
            url: `/admin/permissions/delete/${id}`,
            method: "DELETE",
            data: { _token: "{{ csrf_token() }}" },
            success: function(res) {
                $(`#permission-${id}`).remove();
                showAlert('success', res.message);
            }
        });
    });

    // RELOAD TABLE
    function reloadTable() {
        $.get("{{ route('admin.permissions.index') }}", function(data) {
            $('#permissionTableWrapper').html($(data).find('#permissionTableWrapper').html());

            // Reinitialize DataTable after reload
            table.destroy();
            table = $('#permissionTable').DataTable();
        });
    }

});
</script>
@endpush