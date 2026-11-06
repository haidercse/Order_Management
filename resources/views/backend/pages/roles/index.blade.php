@extends('backend.layouts.master')

@section('title', 'Manage Roles')

@section('admin-content')
    <div class="container-fluid my-4 role-management">

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
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Add Role</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
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
                        <div class="mb-4">
                            <label class="fw-semibold">Permissions</label>
                            <div class="permission-toolbar d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="selectAll">
                                    <label class="custom-control-label font-weight-bold" for="selectAll">Select all permissions</label>
                                </div>
                                <small class="text-muted"><span id="selectedPermissionCount">0</span> selected</small>
                            </div>

                            <div class="row permission-grid">
                                @foreach ($permissions as $group => $perms)
                                    @php($groupId = 'permission-group-' . \Illuminate\Support\Str::slug($group))
                                    <div class="col-12 col-sm-6 col-xl-3 mb-3">
                                        <section class="permission-group-card">
                                            <div class="permission-group-heading">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input group-check"
                                                        id="{{ $groupId }}" data-group="{{ $group }}">
                                                    <label class="custom-control-label font-weight-bold" for="{{ $groupId }}">
                                                        {{ $group }}
                                                    </label>
                                                </div>
                                                <button type="button" class="permission-collapse-toggle"
                                                    data-toggle="collapse" data-target="#{{ $groupId }}-items"
                                                    aria-expanded="true" aria-controls="{{ $groupId }}-items"
                                                    aria-label="Toggle {{ $group }} permissions">
                                                    <i class="fa fa-chevron-up" aria-hidden="true"></i>
                                                </button>
                                            </div>

                                            <div class="collapse show permission-group-options" id="{{ $groupId }}-items">
                                                @foreach ($perms as $perm)
                                                    @php($permissionId = $groupId . '-permission-' . $perm->id)
                                                    <div class="custom-control custom-checkbox permission-option">
                                                        <input type="checkbox" class="custom-control-input perm-checkbox"
                                                            id="{{ $permissionId }}" name="permissions[]"
                                                            value="{{ $perm->name }}" data-group="{{ $group }}">
                                                        <label class="custom-control-label" for="{{ $permissionId }}">
                                                            {{ $perm->name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </section>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <button class="btn btn-success w-100" id="saveBtn">Save</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .role-management .permission-toolbar {
            padding: 12px 14px;
            border: 1px solid #e6ebf1;
            border-radius: 8px;
            background: #f8fafc;
        }

        .role-management .permission-grid {
            max-height: 55vh;
            overflow-y: auto;
            padding: 4px 8px 0;
        }

        .role-management .permission-group-card {
            height: 100%;
            overflow: hidden;
            border: 1px solid #e1e7ef;
            border-radius: 9px;
            background: #fff;
            box-shadow: 0 2px 7px rgba(25, 42, 70, .04);
        }

        .role-management .permission-group-heading {
            display: flex;
            min-height: 48px;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 10px 12px;
            border-bottom: 1px solid #e9edf3;
            background: #f5f7fb;
        }

        .role-management .permission-group-heading label {
            margin-bottom: 0;
        }

        .role-management .permission-collapse-toggle {
            padding: 4px 6px;
            border: 0;
            background: transparent;
            color: #64748b;
        }

        .role-management .permission-collapse-toggle[aria-expanded="false"] .fa {
            transform: rotate(180deg);
        }

        .role-management .permission-group-options {
            padding: 10px 12px;
        }

        .role-management .permission-option {
            min-height: 28px;
            overflow-wrap: anywhere;
        }

        .role-management .permission-option label {
            color: #475569;
            font-size: 13px;
        }

        #roleModal .modal-body {
            max-height: calc(100vh - 150px);
            overflow-y: auto;
        }

        @media (max-width: 575.98px) {
            .role-management .permission-grid {
                max-height: 50vh;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function() {

            function showAlert(type, message) {
                const alert = $('<div>', {
                    class: `alert ${type === 'success' ? 'alert-success' : 'alert-danger'} alert-dismissible fade show`,
                    role: 'alert'
                }).text(message);
                alert.append($('<button>', {
                    type: 'button',
                    class: 'close',
                    'data-dismiss': 'alert',
                    'aria-label': 'Close'
                }).append('<span aria-hidden="true">&times;</span>'));
                $('#alertBox').empty().append(alert);
            }

            function updatePermissionSelection() {
                $('.permission-group-card').each(function() {
                    const checkboxes = $(this).find('.perm-checkbox');
                    const checkedCount = checkboxes.filter(':checked').length;
                    const groupCheckbox = $(this).find('.group-check');

                    groupCheckbox
                        .prop('checked', checkboxes.length > 0 && checkedCount === checkboxes.length)
                        .prop('indeterminate', checkedCount > 0 && checkedCount < checkboxes.length);
                });

                const allPermissions = $('.perm-checkbox');
                const selectedCount = allPermissions.filter(':checked').length;
                $('#selectAll')
                    .prop('checked', allPermissions.length > 0 && selectedCount === allPermissions.length)
                    .prop('indeterminate', selectedCount > 0 && selectedCount < allPermissions.length);
                $('#selectedPermissionCount').text(selectedCount);
            }

            $('#addRoleBtn').click(function() {
                $('#modalTitle').text('Add Role');
                $('#roleForm')[0].reset();
                $('#role_id').val('');
                $('.perm-checkbox, .group-check, #selectAll')
                    .prop('checked', false)
                    .prop('indeterminate', false);
                updatePermissionSelection();
                $('#roleModal').modal('show');
            });

            $('#selectAll').on('change', function() {
                $('.perm-checkbox').prop('checked', $(this).is(':checked'));
                updatePermissionSelection();
            });

            $('.group-check').on('change', function() {
                $(this).closest('.permission-group-card')
                    .find('.perm-checkbox')
                    .prop('checked', $(this).is(':checked'));
                updatePermissionSelection();
            });

            $('.perm-checkbox').on('change', updatePermissionSelection);

            $('.permission-collapse-toggle').on('click', function() {
                const toggle = $(this);
                const target = $(toggle.data('target'));
                target.one('hidden.bs.collapse shown.bs.collapse', function(event) {
                    if (event.target !== target[0]) return;
                    const isExpanded = target.hasClass('show');
                    toggle.attr('aria-expanded', isExpanded ? 'true' : 'false');
                });
            });

            $(document).on('click', '.edit-role', function() {
                const id = $(this).data('id');

                $.get(`/admin/roles/${id}/permissions`)
                    .done(function(res) {
                        $('#modalTitle').text('Edit Role');
                        $('#role_id').val(res.role.id);
                        $('#role_name').val(res.role.name);
                        $('.perm-checkbox, .group-check, #selectAll')
                            .prop('checked', false)
                            .prop('indeterminate', false);

                        const assigned = new Set(res.assigned);
                        $('.perm-checkbox').each(function() {
                            $(this).prop('checked', assigned.has(this.value));
                        });

                        updatePermissionSelection();
                        $('#roleModal').modal('show');
                    })
                    .fail(function(xhr) {
                        showAlert('error', xhr.responseJSON?.message || 'Unable to load role permissions.');
                    });
            });

            $('#roleForm').submit(function(e) {
                e.preventDefault();

                const id = $('#role_id').val();
                const url = id ? `/admin/roles/update/${id}` : "{{ route('admin.roles.store.ajax') }}";

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        name: $('#role_name').val(),
                        permissions: $('.perm-checkbox:checked').map(function() {
                            return this.value;
                        }).get(),
                        _token: "{{ csrf_token() }}"
                    }
                })
                    .done(function(res) {
                        $('#roleModal').modal('hide');
                        showAlert('success', res.message);
                        reloadTable();
                    })
                    .fail(function(xhr) {
                        const errors = xhr.responseJSON?.errors;
                        showAlert('error', errors
                            ? Object.values(errors).flat().join(' ')
                            : (xhr.responseJSON?.message || 'Unable to save role.'));
                    });
            });

            $(document).on('click', '.delete-role', function() {
                if (!confirm('Delete this role?')) return;

                const id = $(this).data('id');

                $.ajax({
                    url: `/admin/roles/delete/${id}`,
                    method: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                })
                    .done(function(res) {
                        $(`#role-${id}`).remove();
                        showAlert('success', res.message);
                    })
                    .fail(function(xhr) {
                        showAlert('error', xhr.responseJSON?.message || 'Unable to delete role.');
                    });
            });

            function reloadTable() {
                $.get("{{ route('admin.roles.index') }}")
                    .done(function(data) {
                        $('#roleTableWrapper').html($(data).find('#roleTableWrapper').html());
                    })
                    .fail(function(xhr) {
                        showAlert('error', xhr.responseJSON?.message || 'Unable to refresh roles.');
                    });
            }

            updatePermissionSelection();
        });
    </script>
@endpush
