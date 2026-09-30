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
                                <th>Parent</th>
                                <th>Route</th>
                                <th>Order</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($menus->whereNull('parent_id') as $parent)
                                {{-- Parent row --}}
                                <tr class="parent-row" data-parent="{{ $parent->id }}">
                                    <td class="group-cell">{{ $parent->group->name ?? '-' }}</td>
                                    <td class="title-cell fw-bold">
                                        ▶ {{ $parent->title }}
                                    </td>
                                    <td>-</td>
                                    <td>{{ $parent->route }}</td>
                                    <td>{{ $parent->order }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-menu" data-id="{{ $parent->id }}"
                                            data-title="{{ $parent->title }}" data-route="{{ $parent->route }}"
                                            data-order="{{ $parent->order }}" data-group="{{ $parent->group_id }}"
                                            data-parent="">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger delete-menu" data-id="{{ $parent->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                {{-- Child rows --}}
                                @foreach ($menus->where('parent_id', $parent->id) as $menu)
                                    <tr class="child-row child-of-{{ $parent->id }}">
                                        <td class="group-cell">{{ $parent->group->name ?? '-' }}</td>
                                        <td class="title-cell">&nbsp;&nbsp;— {{ $menu->title }}</td>
                                        <td class="parent-cell badge badge-pill badge-info">{{ $parent->title }}</td>
                                        <td>{{ $menu->route }}</td>
                                        <td>{{ $menu->order }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-warning edit-menu" data-id="{{ $menu->id }}"
                                                data-title="{{ $menu->title }}" data-route="{{ $menu->route }}"
                                                data-order="{{ $menu->order }}" data-group="{{ $menu->group_id }}"
                                                data-parent="{{ $menu->parent_id }}">
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
                            <label class="form-label fw-semibold">Order</label>
                            <input type="number" class="form-control" id="order" name="order" required>
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
                $('#alertBox').html(`
            <div class="alert ${alertClass} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
                setTimeout(() => $('.alert').alert('close'), 4000);
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
                $('#menuModal').modal('show');
            });

            $('#menuForm').submit(function(e) {
                e.preventDefault();
                let id = $('#menu_id').val();
                let url = id ? `/admin/menus/update/${id}` : "{{ route('admin.menus.store.ajax') }}";

                $.post(url, $(this).serialize(), function() {
                    $('#menuModal').modal('hide');
                    reloadTable();
                    showAlert('success', 'Menu saved successfully!');
                });
            });

            $(document).on('click', '.edit-menu', function() {
                $('#menu_id').val($(this).data('id'));
                $('#title').val($(this).data('title'));
                $('#route').val($(this).data('route'));
                $('#order').val($(this).data('order'));
                $('#group_id').val($(this).data('group'));
                $('#parent_id').val($(this).data('parent'));
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
                    }
                });
            });

            function reloadTable() {
                $.get("{{ route('admin.menus.index') }}", function(data) {
                    $('#menuTableWrapper').html($(data).find('#menuTableWrapper').html());
                });
            }

        });
    </script>
@endpush
