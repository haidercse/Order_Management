@extends('backend.layouts.master')

@section('title', 'Fruit Prices')

@section('admin-content')
<div class="container-fluid my-4">
    <h3 class="mb-4 text-primary font-weight-bold">Fruit Prices</h3>
    <div id="priceAlert"></div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6"><h5 class="mb-0">Price List</h5></div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    <button type="button" class="btn btn-success" id="addPriceBtn"><i class="fa fa-plus"></i> Add Price</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0"><input type="search" id="priceSearch" class="form-control" placeholder="Search fruit, unit or currency"></div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select id="priceFruitFilter" class="form-control">
                        <option value="">All fruits</option>
                        @foreach ($fruits as $fruit)<option value="{{ $fruit->id }}">{{ $fruit->display_name ?: $fruit->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0"><select id="priceStatusFilter" class="form-control"><option value="">All statuses</option><option value="1">Active</option><option value="0">Inactive</option></select></div>
                <div class="col-md-1"><button type="button" class="btn btn-secondary btn-block" id="resetPriceFilter">Reset</button></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Price Records</div>
        <div class="card-body" id="priceTableWrapper">@include('backend.pages.fruit_prices.partials.table', ['prices' => $prices])</div>
    </div>
</div>

<div class="modal fade" id="priceModal" tabindex="-1" role="dialog" aria-labelledby="priceModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="priceModalTitle">Add Fruit Price</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="priceForm">
                @csrf
                <input type="hidden" id="price_id">
                <div class="form-group">
                    <label for="price_fruit_id">Fruit</label>
                    <select class="form-control" id="price_fruit_id" name="fruit_id" required>
                        <option value="">Select fruit</option>
                        @foreach ($fruits as $fruit)<option value="{{ $fruit->id }}" data-allow-kg="{{ $fruit->allow_kg ? 1 : 0 }}" data-allow-box="{{ $fruit->allow_box ? 1 : 0 }}">{{ $fruit->display_name ?: $fruit->name }} ({{ $fruit->code }})</option>@endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="price_unit_id">Unit</label>
                        <select class="form-control" id="price_unit_id" name="unit_id" required>
                            <option value="">Select unit</option>
                            @foreach ($units as $unit)<option value="{{ $unit->id }}" data-code="{{ $unit->code }}" data-weight="{{ $unit->is_weight_unit ? 1 : 0 }}">{{ $unit->name }} ({{ $unit->symbol }}){{ $unit->status ? '' : ' — inactive' }}</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="priceBoxGroup">
                        <label for="price_box_configuration_id">Box configuration</label>
                        <select class="form-control" id="price_box_configuration_id" name="box_configuration_id">
                            <option value="">Select box configuration</option>
                            @foreach ($fruits as $fruit)
                                @foreach ($fruit->boxConfigurations as $box)
                                    <option value="{{ $box->id }}" data-fruit-id="{{ $fruit->id }}">{{ $fruit->display_name ?: $fruit->name }} — {{ $box->name }} ({{ number_format((float) $box->weight_kg, 3) }} kg){{ $box->status ? '' : ' — inactive' }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4"><label for="price_amount">Price</label><input type="number" class="form-control" id="price_amount" name="price" min="0" step="0.01" required></div>
                    <div class="form-group col-md-4"><label for="price_currency">Currency</label><input type="text" class="form-control text-uppercase" id="price_currency" name="currency" value="EUR" minlength="3" maxlength="3" pattern="[A-Za-z]{3}" required></div>
                    <div class="form-group col-md-4"><label for="price_is_active">Status</label><select class="form-control" id="price_is_active" name="is_active" required><option value="1">Active</option><option value="0">Inactive</option></select></div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6"><label for="price_effective_from">Valid from</label><input type="date" class="form-control" id="price_effective_from" name="effective_from" required></div>
                    <div class="form-group col-md-6"><label for="price_effective_to">Valid to (optional)</label><input type="date" class="form-control" id="price_effective_to" name="effective_to"></div>
                </div>
                <button type="submit" class="btn btn-success btn-block" id="savePriceBtn">Save Price</button>
            </form>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const indexUrl = @json(route('admin.fruit-prices.index'));
    const storeUrl = @json(route('admin.fruit-prices.store'));
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });
    function alertPrice(type, message) {
        $('<div>', { class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show` }).text(message)
            .append($('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert' }).append('<span>&times;</span>')).appendTo('#priceAlert');
    }
    function requestError(xhr, fallback) {
        const errors = xhr.responseJSON && xhr.responseJSON.errors;
        alertPrice('error', errors ? Object.values(errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
    }
    function loadPrices(url = indexUrl) {
        $('#priceTableWrapper').css('opacity', .5);
        $.get(url, { search: $('#priceSearch').val(), fruit_id: $('#priceFruitFilter').val(), status: $('#priceStatusFilter').val() })
            .done(response => $('#priceTableWrapper').html(response.html))
            .fail(xhr => requestError(xhr, 'Unable to load fruit prices.'))
            .always(() => $('#priceTableWrapper').css('opacity', 1));
    }
    function syncBoxOptions(selectedId = '') {
        const fruitId = $('#price_fruit_id').val();
        const boxUnit = $('#price_unit_id option:selected').data('code') === 'BOX';
        $('#priceBoxGroup').toggle(boxUnit);
        $('#price_box_configuration_id').prop('required', boxUnit);
        $('#price_box_configuration_id option').each(function () {
            if (!this.value) return;
            const belongs = String($(this).data('fruit-id')) === String(fruitId);
            $(this).prop('disabled', !belongs).toggle(belongs);
        });
        $('#price_box_configuration_id').val(selectedId || '');
    }
    $(document).on('click', '#priceTableWrapper .pagination a', function (event) { event.preventDefault(); loadPrices($(this).attr('href')); });
    let searchTimer;
    $('#priceSearch').on('input', function () { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadPrices(), 300); });
    $('#priceFruitFilter, #priceStatusFilter').on('change', () => loadPrices());
    $('#resetPriceFilter').on('click', function () { $('#priceSearch, #priceFruitFilter, #priceStatusFilter').val(''); loadPrices(); });
    $('#price_fruit_id').on('change', () => syncBoxOptions());
    $('#price_unit_id').on('change', () => syncBoxOptions());
    $('#addPriceBtn').on('click', function () {
        $('#priceForm')[0].reset(); $('#price_id').val(''); $('#price_currency').val('EUR');
        $('#price_is_active').val('1'); $('#price_effective_from').val(new Date().toISOString().slice(0, 10));
        syncBoxOptions(); $('#priceModalTitle').text('Add Fruit Price'); $('#savePriceBtn').text('Save Price'); $('#priceModal').modal('show');
    });
    $('#priceForm').on('submit', function (event) {
        event.preventDefault();
        const id = $('#price_id').val(); const button = $('#savePriceBtn').prop('disabled', true).text('Saving...');
        $.ajax({ url: id ? `/admin/fruit-prices/update/${id}` : storeUrl, method: 'POST', data: $(this).serialize() })
            .done(response => { $('#priceModal').modal('hide'); alertPrice('success', response.message); loadPrices(); })
            .fail(xhr => requestError(xhr, 'Unable to save fruit price.'))
            .always(() => button.prop('disabled', false).text(id ? 'Update Price' : 'Save Price'));
    });
    $(document).on('click', '.edit-price', function () {
        $.get(`/admin/fruit-prices/edit/${$(this).data('id')}`)
            .done(({ data }) => {
                $('#price_id').val(data.id); $('#price_fruit_id').val(data.fruit_id); $('#price_unit_id').val(data.unit_id);
                syncBoxOptions(data.box_configuration_id || '');
                $('#price_amount').val(data.price); $('#price_currency').val(data.currency);
                $('#price_is_active').val(data.is_active ? '1' : '0');
                $('#price_effective_from').val(data.effective_from); $('#price_effective_to').val(data.effective_to || '');
                $('#priceModalTitle').text('Edit Fruit Price'); $('#savePriceBtn').text('Update Price'); $('#priceModal').modal('show');
            })
            .fail(xhr => requestError(xhr, 'Unable to load fruit price.'));
    });
    $(document).on('click', '.delete-price', function () {
        const id = $(this).data('id');
        if (!window.confirm('Delete this fruit price record?')) return;
        $.ajax({ url: `/admin/fruit-prices/delete/${id}`, method: 'DELETE' })
            .done(response => { alertPrice('success', response.message); loadPrices(); })
            .fail(xhr => requestError(xhr, 'Unable to delete fruit price.'));
    });
});
</script>
@endpush
