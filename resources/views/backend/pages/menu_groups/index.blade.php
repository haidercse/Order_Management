@extends('backend.layouts.master')

@section('title', 'Manage Menu Groups')

@section('admin-content')
    <div class="container my-4">

        <h3 class="mb-4 text-primary fw-bold text-center">Menu Group List</h3>

        {{-- Success & Error Alerts --}}
        <div id="alertBox" class="mt-2"></div>

        {{-- Add Button --}}
        <div class="mb-3 text-end">
            <button class="btn btn-success" id="addGroupBtn">Add New Group</button>
        </div>

        {{-- Menu Group List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">Menu Group Records</div>
            <div class="card-body" id="groupTableWrapper">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Order</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($groups as $group)
                                <tr id="group-{{ $group->id }}">
                                    <td>{{ $group->name }}</td>
                                    <td>{{ $group->order }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning edit-group" data-id="{{ $group->id }}"
                                            data-name="{{ $group->name }}" data-order="{{ $group->order }}">
                                            <i class="fa fa-edit"></i>
                                        </button>

                                        <button class="btn btn-sm btn-danger delete-group" data-id="{{ $group->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div class="modal fade" id="groupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalTitle">Add Menu Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="groupForm">
                        @csrf
                        <input type="hidden" id="group_id" name="id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Group Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
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

            function showAlert(type, message) {
                let alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                $('#alertBox').html(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);

                setTimeout(() => $('.alert').alert('close'), 4000);
            }

            // Add Button
            $('#addGroupBtn').click(function() {
                $('#modalTitle').text("Add Menu Group");
                $('#groupForm')[0].reset();
                $('#group_id').val('');

                $('#saveBtn').removeClass('btn-warning').addClass('btn-success').text("Save");
                $('#groupModal').modal('show');
            });

            // Save / Update
            $('#groupForm').submit(function(e) {
                e.preventDefault();

                let id = $('#group_id').val();
                let url = id ? `/admin/menu-groups/update/${id}` :
                    "{{ route('admin.menu-groups.store.ajax') }}";

                $.ajax({
                    url: url,
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#groupModal').modal('hide');
                        reloadTable();
                        showAlert('success', "Menu Group saved successfully!");
                    },
                    error: function() {
                        showAlert('error', "Something went wrong!");
                    }
                });
            });

            // Edit
            $(document).on('click', '.edit-group', function() {
                $('#modalTitle').text("Edit Menu Group");

                $('#group_id').val($(this).data('id'));
                $('#name').val($(this).data('name'));
                $('#order').val($(this).data('order'));

                $('#saveBtn').removeClass('btn-success').addClass('btn-warning').text("Update");

                $('#groupModal').modal('show');
            });

            // Delete
            $(document).on('click', '.delete-group', function() {
                if (!confirm("Are you sure to delete?")) return;

                let id = $(this).data('id');

                $.ajax({
                    url: `/admin/menu-groups/delete/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function() {
                        $(`#group-${id}`).remove();
                        showAlert('success', "Menu Group deleted successfully!");
                    },
                    error: function() {
                        showAlert('error', "Failed to delete!");
                    }
                });
            });

            // Reload Table
            function reloadTable() {
                $.get("{{ route('admin.menu-groups.index') }}", function(data) {
                    $('#groupTableWrapper').html($(data).find('#groupTableWrapper').html());
                });
            }

        });
    </script>
@endpush
