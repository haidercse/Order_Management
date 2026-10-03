@extends('backend.layouts.master')

@section('title', 'Manage Fruits')

@section('admin-content')
<div class="container-fluid my-4">
    <h3 class="mb-4 text-primary font-weight-bold">Manage Fruits</h3>
    <div id="fruitAlert"></div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6"><h5 class="mb-0">Fruit List</h5></div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <button type="button" class="btn btn-success" id="addFruitBtn"><i class="fa fa-plus"></i> Add Fruit</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-2 mb-md-0"><input type="search" id="fruitSearch" class="form-control" placeholder="Search by fruit, code or category"></div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select id="fruitCategoryFilter" class="form-control">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select id="fruitStatusFilter" class="form-control">
                        <option value="">All statuses</option><option value="1">Active</option><option value="0">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="button" class="btn btn-secondary btn-block" id="resetFruitFilter">Reset</button></div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Fruit Records</div>
        <div class="card-body" id="fruitTableWrapper">
            @include('backend.pages.fruits.partials.table', ['fruits' => $fruits])
        </div>
    </div>
</div>

<div class="modal fade" id="fruitModal" tabindex="-1" role="dialog" aria-labelledby="fruitModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="fruitModalTitle">Add Fruit</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="fruitForm">
                    @csrf
                    <input type="hidden" id="fruit_id">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="fruit_name">Fruit name</label>
                            <input type="text" class="form-control" id="fruit_name" name="name" required maxlength="255">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="fruit_display_name">Display name (optional)</label>
                            <input type="text" class="form-control" id="fruit_display_name" name="display_name" maxlength="255">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="fruit_code">Code</label>
                            <input type="text" class="form-control" id="fruit_code" name="code" required maxlength="80">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fruit_category_id">Category</label>
                            <select class="form-control" id="fruit_category_id" name="category_id" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fruit_default_unit_id">Default unit</label>
                            <select class="form-control" id="fruit_default_unit_id" name="default_unit_id" required>
                                <option value="">Select unit</option>
                                @foreach ($units as $unit)<option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }}){{ $unit->status ? '' : ' — inactive' }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="fruit_sort_order">Sort order</label>
                            <input type="number" class="form-control" id="fruit_sort_order" name="sort_order" min="0" value="0" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fruit_status">Status</label>
                            <select class="form-control" id="fruit_status" name="status" required><option value="1">Active</option><option value="0">Inactive</option></select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Allowed order units</label>
                            <div class="pt-2">
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="fruit_allow_kg" name="allow_kg" value="1">
                                    <label class="custom-control-label" for="fruit_allow_kg">Kilograms</label>
                                </div>
                                <div class="custom-control custom-checkbox custom-control-inline">
                                    <input type="checkbox" class="custom-control-input" id="fruit_allow_box" name="allow_box" value="1">
                                    <label class="custom-control-label" for="fruit_allow_box">Boxes</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="fruit_notes">Notes</label>
                        <textarea class="form-control" id="fruit_notes" name="notes" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block" id="saveFruitBtn">Save Fruit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const indexUrl = @json(route('admin.fruits.index'));
    const storeUrl = @json(route('admin.fruits.store'));
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });

    function alertFruit(type, message) {
        $('<div>', { class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`, role: 'alert' })
            .text(message)
            .append($('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert', 'aria-label': 'Close' }).append('<span aria-hidden="true">&times;</span>'))
            .appendTo('#fruitAlert');
    }
    function requestError(xhr, fallback) {
        const errors = xhr.responseJSON && xhr.responseJSON.errors;
        alertFruit('error', errors ? Object.values(errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
    }
    function loadFruits(url = indexUrl) {
        $('#fruitTableWrapper').css('opacity', .5);
        $.get(url, { search: $('#fruitSearch').val(), category_id: $('#fruitCategoryFilter').val(), status: $('#fruitStatusFilter').val() })
            .done(response => $('#fruitTableWrapper').html(response.html))
            .fail(xhr => requestError(xhr, 'Unable to load fruit records.'))
            .always(() => $('#fruitTableWrapper').css('opacity', 1));
    }
    $(document).on('click', '#fruitTableWrapper .pagination a', function (event) { event.preventDefault(); loadFruits($(this).attr('href')); });
    let searchTimer;
    $('#fruitSearch').on('input', function () { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadFruits(), 300); });
    $('#fruitCategoryFilter, #fruitStatusFilter').on('change', () => loadFruits());
    $('#resetFruitFilter').on('click', function () { $('#fruitSearch, #fruitCategoryFilter, #fruitStatusFilter').val(''); loadFruits(); });

    $('#addFruitBtn').on('click', function () {
        $('#fruitForm')[0].reset();
        $('#fruit_id').val('');
        $('#fruit_sort_order').val(0);
        $('#fruit_status').val('1');
        $('#fruit_allow_kg, #fruit_allow_box').prop('checked', true);
        $('#fruitModalTitle').text('Add Fruit');
        $('#saveFruitBtn').text('Save Fruit');
        $('#fruitModal').modal('show');
    });
    $('#fruitForm').on('submit', function (event) {
        event.preventDefault();
        const id = $('#fruit_id').val();
        const button = $('#saveFruitBtn').prop('disabled', true).text('Saving...');
        const data = $(this).serializeArray().filter(field => !['allow_kg', 'allow_box'].includes(field.name) || field.value === '1');
        data.push({ name: 'allow_kg', value: $('#fruit_allow_kg').is(':checked') ? '1' : '0' });
        data.push({ name: 'allow_box', value: $('#fruit_allow_box').is(':checked') ? '1' : '0' });
        $.ajax({ url: id ? `/admin/fruits/update/${id}` : storeUrl, method: 'POST', data })
            .done(response => { $('#fruitModal').modal('hide'); alertFruit('success', response.message); loadFruits(); })
            .fail(xhr => requestError(xhr, 'Unable to save fruit.'))
            .always(() => button.prop('disabled', false).text(id ? 'Update Fruit' : 'Save Fruit'));
    });
    $(document).on('click', '.edit-fruit', function () {
        $.get(`/admin/fruits/edit/${$(this).data('id')}`)
            .done(({ data }) => {
                $('#fruit_id').val(data.id);
                $('#fruit_name').val(data.name);
                $('#fruit_display_name').val(data.display_name);
                $('#fruit_code').val(data.code);
                $('#fruit_category_id').val(data.category_id);
                $('#fruit_default_unit_id').val(data.default_unit_id);
                $('#fruit_sort_order').val(data.sort_order);
                $('#fruit_status').val(data.status ? '1' : '0');
                $('#fruit_allow_kg').prop('checked', !!data.allow_kg);
                $('#fruit_allow_box').prop('checked', !!data.allow_box);
                $('#fruit_notes').val(data.notes);
                $('#fruitModalTitle').text('Edit Fruit');
                $('#saveFruitBtn').text('Update Fruit');
                $('#fruitModal').modal('show');
            })
            .fail(xhr => requestError(xhr, 'Unable to load fruit.'));
    });
    $(document).on('click', '.delete-fruit', function () {
        const id = $(this).data('id');
        if (!window.confirm('Delete this fruit? Fruits with order or warehouse history cannot be deleted.')) return;
        $.ajax({ url: `/admin/fruits/delete/${id}`, method: 'DELETE' })
            .done(response => { alertFruit('success', response.message); loadFruits(); })
            .fail(xhr => requestError(xhr, 'Unable to delete fruit.'));
    });
});
</script>
@endpush
