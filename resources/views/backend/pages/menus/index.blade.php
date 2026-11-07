@extends('backend.layouts.master')

@section('title', 'Manage Menus')

@section('admin-content')
    <div class="container my-4">

        <h3 class="mb-4 text-primary fw-bold text-center">Menu List</h3>

        {{-- Alerts --}}
        <div id="alertBox" class="mt-2"></div>

        {{-- Add Button --}}
        <div class="mb-3 text-end">
            <button class="btn btn-success" id="addMenuBtn">Add New Menu</button>
        </div>

        {{-- Filters --}}
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="liveSearch" class="form-control" placeholder="🔍 Search menu...">
            </div>

            <div class="col-md-4">
                <select id="filterGroup" class="form-control">
                    <option value="">-- Filter by Group --</option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->name }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <select id="filterParent" class="form-control">
                    <option value="">-- Filter by Parent --</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->title }}">{{ $parent->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Menu Table --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Menu Records</div>
            <div class="card-body" id="menuTableWrapper">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Title</th>
                                <th>Icon</th>
                                <th>Parent</th>
                                <th>Route</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($menus->whereNull('parent_id') as $parent)
                                {{-- Parent row --}}
                                <tr class="parent-row" id="menu-{{ $parent->id }}" data-parent="{{ $parent->id }}">
                                    <td class="group-cell">{{ $parent->group->name ?? '-' }}</td>
                                    <td class="title-cell fw-bold">
                                        ▶ {{ $parent->title }}
                                    </td>
                                    <td>
                                        @if ($parent->icon)
                                            <i class="{{ $parent->icon }}" aria-hidden="true"></i>
                                        @endif
                                        {{ $parent->icon }}
                                    </td>
                                    <td>-</td>
                                    <td>{{ $parent->route }}</td>
                                    <td>{{ $parent->order }}</td>
                                    <td>{{ $parent->status ? 'Active' : 'Inactive' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-menu" data-id="{{ $parent->id }}"
                                            data-title="{{ $parent->title }}" data-route="{{ $parent->route }}"
                                            data-order="{{ $parent->order }}" data-group="{{ $parent->group_id }}"
                                            data-parent="" data-icon="{{ $parent->icon }}"
                                            data-permission="{{ $parent->permission }}"
                                            data-status="{{ $parent->status ? '1' : '0' }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger delete-menu" data-id="{{ $parent->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Child rows --}}
                                @foreach ($menus->where('parent_id', $parent->id) as $menu)
                                    <tr class="child-row child-of-{{ $parent->id }}" id="menu-{{ $menu->id }}">
                                        <td class="group-cell">{{ $parent->group->name ?? '-' }}</td>
                                        <td class="title-cell">&nbsp;&nbsp;— {{ $menu->title }}</td>
                                        <td>
                                            @if ($menu->icon)
                                                <i class="{{ $menu->icon }}" aria-hidden="true"></i>
                                            @endif
                                            {{ $menu->icon }}
                                        </td>
                                        <td class="parent-cell badge badge-pill badge-info">{{ $parent->title }}</td>
                                        <td>{{ $menu->route }}</td>
                                        <td>{{ $menu->order }}</td>
                                        <td>{{ $menu->status ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-menu" data-id="{{ $menu->id }}"
                                                data-title="{{ $menu->title }}" data-route="{{ $menu->route }}"
                                                data-order="{{ $menu->order }}" data-group="{{ $menu->group_id }}"
                                                data-parent="{{ $menu->parent_id }}" data-icon="{{ $menu->icon }}"
                                                data-permission="{{ $menu->permission }}"
                                                data-status="{{ $menu->status ? '1' : '0' }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <button class="btn btn-sm btn-danger delete-menu"
                                                data-id="{{ $menu->id }}">
                                                <i class="fa fa-trash"></i>
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
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="menuModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Add Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="menuForm">
                        @csrf
                        <input type="hidden" id="menu_id" name="id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Group</label>
                            <select name="group_id" id="group_id" class="form-control">
                                <option value="">-- Select Group --</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Parent Menu (Optional)</label>
                            <select name="parent_id" id="parent_id" class="form-control">
                                <option value="">-- No Parent (Main Menu) --</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Menu Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Route Name</label>
                            <input type="text" class="form-control" id="route" name="route">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="icon">Icon class</label>
                            <input type="text" class="form-control" id="icon" name="icon"
                                list="menuIconSuggestions" placeholder="e.g. ti-dashboard or fa fa-users"
                                autocomplete="off">
                            <datalist id="menuIconSuggestions">
                                @foreach (['ti-dashboard', 'ti-package', 'fa fa-shopping-cart', 'ti-apple', 'fa fa-file-text-o', 'ti-lock', 'fa fa-users', 'ti-menu', 'ti-key'] as $iconSuggestion)
                                    <option value="{{ $iconSuggestion }}">
                                @endforeach
                            </datalist>
                            <small class="form-text text-muted">Type an icon class or choose a suggestion from the menu seeder. <span id="iconPreview" class="ml-2"></span></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Permission</label>
                            <input type="text" class="form-control" id="permission" name="permission"
                                placeholder="e.g. inventory.view">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order</label>
                            <input type="number" class="form-control" id="order" name="order" min="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="saveBtn">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            /* ================= ALERT ================= */
            function showAlert(type, message) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alert = $('<div>', {
                    class: `alert ${alertClass} alert-dismissible fade show`,
                    role: 'alert'
                }).text(message);
                alert.append($('<button>', {
                    type: 'button',
                    class: 'btn-close',
                    'data-bs-dismiss': 'alert',
                    'aria-label': 'Close'
                }));
                $('#alertBox').empty().append(alert);
                setTimeout(() => $('.alert').alert('close'), 4000);
            }

            function responseError(xhr) {
                const errors = xhr.responseJSON?.errors;
                if (errors) return Object.values(errors).flat().join(' ');
                return xhr.responseJSON?.message || 'Something went wrong!';
            }

            function updateParentGroupField() {
                const hasParent = Boolean($('#parent_id').val());
                $('#group_id').prop('disabled', hasParent).prop('required', !hasParent);
            }

            function updateIconPreview() {
                const icon = $('#icon').val().trim();
                const preview = $('#iconPreview').empty();
                if (icon) {
                    preview.append($('<i>', {
                        class: icon,
                        'aria-hidden': 'true'
                    })).append(document.createTextNode(` ${icon}`));
                }
            }

            /* ================= COLLAPSE ================= */
            $('.parent-row .title-cell').on('click', function() {
                let parentId = $(this).closest('.parent-row').data('parent');
                $('.child-of-' + parentId).toggle();
            });

            /* ================= LIVE SEARCH ================= */
            $('#liveSearch').on('keyup', function() {
                let val = $(this).val().toLowerCase();
                $('tbody tr').hide();

                $('.parent-row').each(function() {
                    let parent = $(this);
                    let parentId = parent.data('parent');
                    let match = parent.text().toLowerCase().includes(val);

                    $('.child-of-' + parentId).each(function() {
                        if ($(this).text().toLowerCase().includes(val)) {
                            $(this).show();
                            match = true;
                        }
                    });

                    if (match) parent.show();
                });

                if (val === '') $('tbody tr').show();
            });

            /* ================= FILTER GROUP ================= */
            $('#filterGroup').on('change', function() {
                let g = $(this).val();
                $('tbody tr').hide();

                if (!g) return $('tbody tr').show();

                $('.parent-row').each(function() {
                    let p = $(this);
                    let pid = p.data('parent');
                    if (p.find('.group-cell').text().trim() === g) {
                        p.show();
                        $('.child-of-' + pid).show();
                    }
                });
            });

            /* ================= FILTER PARENT ================= */
            $('#filterParent').on('change', function() {
                let pName = $(this).val();
                $('tbody tr').hide();

                if (!pName) return $('tbody tr').show();

                $('.parent-row').each(function() {
                    let p = $(this);
                    let pid = p.data('parent');
                    if (p.find('.title-cell').text().includes(pName)) {
                        p.show();
                        $('.child-of-' + pid).show();
                    }
                });
            });

            /* ================= CRUD (UNCHANGED) ================= */
            $('#addMenuBtn').click(function() {
                $('#menuForm')[0].reset();
                $('#menu_id').val('');
                $('#status').val('1');
                updateParentGroupField();
                updateIconPreview();
                $('#modalTitle').text('Add Menu');
                $('#menuModal').modal('show');
            });

            $('#parent_id').on('change', updateParentGroupField);
            $('#icon').on('input', updateIconPreview);

            $('#menuForm').submit(function(e) {
                e.preventDefault();
                let id = $('#menu_id').val();
                let url = id ? `/admin/menus/update/${id}` : "{{ route('admin.menus.store.ajax') }}";

                $('#saveBtn').prop('disabled', true);
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function() {
                        $('#menuModal').modal('hide');
                        reloadTable();
                        showAlert('success', 'Menu saved successfully!');
                    },
                    error: function(xhr) {
                        showAlert('error', responseError(xhr));
                    },
                    complete: function() {
                        $('#saveBtn').prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.edit-menu', function() {
                $('#modalTitle').text('Edit Menu');
                $('#menu_id').val($(this).data('id'));
                $('#title').val($(this).data('title'));
                $('#route').val($(this).data('route'));
                $('#icon').val($(this).data('icon') || '');
                $('#permission').val($(this).data('permission') || '');
                $('#order').val($(this).data('order'));
                $('#group_id').val($(this).data('group'));
                $('#parent_id').val($(this).data('parent'));
                $('#status').val(String($(this).data('status')));
                updateParentGroupField();
                updateIconPreview();
                $('#menuModal').modal('show');
            });

            $(document).on('click', '.delete-menu', function() {
                if (!confirm('Are you sure?')) return;
                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/menus/delete/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        $('#menu-' + id).remove();
                        showAlert('success', 'Menu deleted!');
                    },
                    error: function(xhr) {
                        showAlert('error', responseError(xhr));
                    }
                });
            });

            function reloadTable() {
                $.get("{{ route('admin.menus.index') }}", function(data) {
                    const refreshedPage = $(data);
                    $('#menuTableWrapper').html(refreshedPage.find('#menuTableWrapper').html());
                    $('#group_id').html(refreshedPage.find('#group_id').html());
                    $('#parent_id').html(refreshedPage.find('#parent_id').html());
                    $('#filterGroup').html(refreshedPage.find('#filterGroup').html());
                    $('#filterParent').html(refreshedPage.find('#filterParent').html());
                    updateParentGroupField();
                }).fail(function(xhr) {
                    showAlert('error', responseError(xhr));
                });
            }

        });
    </script>
@endpush
