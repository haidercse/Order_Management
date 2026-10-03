@extends('backend.layouts.master')

@section('title', 'Box Configuration')

@section('admin-content')
<div class="container-fluid my-4">
    <h3 class="mb-4 text-primary font-weight-bold">Box Configuration</h3>
    <div id="boxAlert"></div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6"><h5 class="mb-0">Box Configuration List</h5></div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <button type="button" class="btn btn-success" id="addBoxBtn"><i class="fa fa-plus"></i> Add Configuration</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0"><input type="search" id="boxSearch" class="form-control" placeholder="Search fruit, configuration or code"></div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select id="boxFruitFilter" class="form-control">
                        <option value="">All fruits</option>
                        @foreach ($fruits as $fruit)<option value="{{ $fruit->id }}">{{ $fruit->display_name ?: $fruit->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0"><select id="boxStatusFilter" class="form-control"><option value="">All statuses</option><option value="1">Active</option><option value="0">Inactive</option></select></div>
                <div class="col-md-1"><button type="button" class="btn btn-secondary btn-block" id="resetBoxFilter">Reset</button></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Box Configuration Records</div>
        <div class="card-body" id="boxTableWrapper">@include('backend.pages.boxes.partials.table', ['boxes' => $boxes])</div>
    </div>
</div>

<div class="modal fade" id="boxModal" tabindex="-1" role="dialog" aria-labelledby="boxModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="boxModalTitle">Add Box Configuration</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="boxForm">
                @csrf
                <input type="hidden" id="box_id">
                <div class="form-group">
                    <label for="box_fruit_id">Fruit</label>
                    <select class="form-control" id="box_fruit_id" name="fruit_id" required>
                        <option value="">Select fruit</option>
                        @foreach ($fruits as $fruit)<option value="{{ $fruit->id }}">{{ $fruit->display_name ?: $fruit->name }} ({{ $fruit->code }})</option>@endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-7"><label for="box_name">Configuration name</label><input type="text" class="form-control" id="box_name" name="name" maxlength="255" required></div>
                    <div class="form-group col-md-5"><label for="box_code">Code</label><input type="text" class="form-control" id="box_code" name="code" maxlength="50" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4"><label for="box_weight_kg">Weight (kg)</label><input type="number" class="form-control" id="box_weight_kg" name="weight_kg" min="0.001" step="0.001" required></div>
                    <div class="form-group col-md-4"><label for="box_is_default">Default configuration</label><select class="form-control" id="box_is_default" name="is_default" required><option value="0">No</option><option value="1">Yes</option></select></div>
                    <div class="form-group col-md-4"><label for="box_status">Status</label><select class="form-control" id="box_status" name="status" required><option value="1">Active</option><option value="0">Inactive</option></select></div>
                </div>
                <small class="form-text text-muted mb-3">Choosing this as the default will clear the default flag from the fruit's other box configurations.</small>
                <button type="submit" class="btn btn-success btn-block" id="saveBoxBtn">Save Configuration</button>
            </form>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const indexUrl = @json(route('admin.boxes.index'));
    const storeUrl = @json(route('admin.boxes.store'));
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });
    function alertBox(type, message) {
        $('<div>', { class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show` }).text(message)
            .append($('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert' }).append('<span>&times;</span>')).appendTo('#boxAlert');
    }
    function requestError(xhr, fallback) {
        const errors = xhr.responseJSON && xhr.responseJSON.errors;
        alertBox('error', errors ? Object.values(errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
    }
    function loadBoxes(url = indexUrl) {
        $('#boxTableWrapper').css('opacity', .5);
        $.get(url, { search: $('#boxSearch').val(), fruit_id: $('#boxFruitFilter').val(), status: $('#boxStatusFilter').val() })
            .done(response => $('#boxTableWrapper').html(response.html))
            .fail(xhr => requestError(xhr, 'Unable to load box configurations.'))
            .always(() => $('#boxTableWrapper').css('opacity', 1));
    }
    $(document).on('click', '#boxTableWrapper .pagination a', function (event) { event.preventDefault(); loadBoxes($(this).attr('href')); });
    let searchTimer;
    $('#boxSearch').on('input', function () { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadBoxes(), 300); });
    $('#boxFruitFilter, #boxStatusFilter').on('change', () => loadBoxes());
    $('#resetBoxFilter').on('click', function () { $('#boxSearch, #boxFruitFilter, #boxStatusFilter').val(''); loadBoxes(); });
    $('#addBoxBtn').on('click', function () {
        $('#boxForm')[0].reset(); $('#box_id').val(''); $('#box_is_default').val('0'); $('#box_status').val('1');
        $('#boxModalTitle').text('Add Box Configuration'); $('#saveBoxBtn').text('Save Configuration'); $('#boxModal').modal('show');
    });
    $('#boxForm').on('submit', function (event) {
        event.preventDefault();
        const id = $('#box_id').val(); const button = $('#saveBoxBtn').prop('disabled', true).text('Saving...');
        $.ajax({ url: id ? `/admin/boxes/update/${id}` : storeUrl, method: 'POST', data: $(this).serialize() })
            .done(response => { $('#boxModal').modal('hide'); alertBox('success', response.message); loadBoxes(); })
            .fail(xhr => requestError(xhr, 'Unable to save box configuration.'))
            .always(() => button.prop('disabled', false).text(id ? 'Update Configuration' : 'Save Configuration'));
    });
    $(document).on('click', '.edit-box', function () {
        $.get(`/admin/boxes/edit/${$(this).data('id')}`)
            .done(({ data }) => {
                $('#box_id').val(data.id); $('#box_fruit_id').val(data.fruit_id); $('#box_name').val(data.name);
                $('#box_code').val(data.code); $('#box_weight_kg').val(data.weight_kg);
                $('#box_is_default').val(data.is_default ? '1' : '0'); $('#box_status').val(data.status ? '1' : '0');
                $('#boxModalTitle').text('Edit Box Configuration'); $('#saveBoxBtn').text('Update Configuration'); $('#boxModal').modal('show');
            })
            .fail(xhr => requestError(xhr, 'Unable to load box configuration.'));
    });
    $(document).on('click', '.delete-box', function () {
        const id = $(this).data('id');
        if (!window.confirm('Delete this configuration? Configurations used by prices, orders or warehouse history cannot be deleted.')) return;
        $.ajax({ url: `/admin/boxes/delete/${id}`, method: 'DELETE' })
            .done(response => { alertBox('success', response.message); loadBoxes(); })
            .fail(xhr => requestError(xhr, 'Unable to delete box configuration.'));
    });
});
</script>
@endpush
