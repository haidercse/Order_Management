@extends('backend.layouts.master')

@section('title', 'Warehouse Inventory')

@section('admin-content')
<div class="container-fluid my-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div><h3 class="mb-1 text-primary font-weight-bold">Warehouse Inventory</h3><p class="text-muted mb-0">Record stock receipts, issues and adjustments. All movements are kept in the inventory ledger.</p></div>
        <button type="button" class="btn btn-success mt-2 mt-md-0" id="addMovementBtn"><i class="fa fa-plus"></i> Record Movement</button>
    </div>
    <div id="inventoryAlert"></div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0"><input type="search" id="stockSearch" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search fruit or code"></div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select id="stockFruitFilter" class="form-control">
                        <option value="">All fruits</option>
                        @foreach ($fruits as $fruit)<option value="{{ $fruit->id }}" @selected(($filters['fruit_id'] ?? '') == $fruit->id)>{{ $fruit->display_name ?: $fruit->name }}</option>@endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select id="stockStatusFilter" class="form-control">
                        <option value="">All statuses</option>
                        <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                        <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-1"><button type="button" class="btn btn-secondary btn-block" id="resetStockFilter">Reset</button></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">Stock on hand</div>
        <div class="card-body" id="stockTableWrapper">@include('backend.pages.inventory.partials.stocks', ['stocks' => $stocks])</div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Recent inventory movements</span>
            <span class="text-muted small">{{ $movements->count() }} latest</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light"><tr><th>Date</th><th>Fruit</th><th>Movement</th><th>Quantity</th><th>Reference</th><th>Recorded by</th><th>Notes</th></tr></thead>
                <tbody>
                    @forelse ($movements as $movement)
                        <tr>
                            <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $movement->fruit->display_name ?: $movement->fruit->name }}</td>
                            <td><span class="badge badge-{{ in_array($movement->type, ['purchase', 'return'], true) ? 'success' : 'info' }}">{{ ucfirst($movement->type) }}</span></td>
                            <td>{{ number_format((float) $movement->quantity, 3) }} {{ $movement->unit->symbol ?? $movement->unit->name }} <small class="text-muted">({{ number_format((float) $movement->quantity_kg, 3) }} kg)</small></td>
                            <td>
                                @if ($movement->referenceOrder)
                                    <a href="{{ route('admin.orders.index', ['search' => $movement->referenceOrder->shop->code ?? '', 'date' => $movement->referenceOrder->order_date->toDateString()]) }}">Order #{{ $movement->reference_order_id }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $movement->creator->name ?? 'System' }}</td>
                            <td>{{ $movement->notes ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No inventory movements recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="movementModal" tabindex="-1" role="dialog" aria-labelledby="movementModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="movementModalTitle">Record Inventory Movement</h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <form id="movementForm">
                @csrf
                <div class="form-group">
                    <label for="movement_fruit_id">Fruit</label>
                    <select class="form-control" id="movement_fruit_id" name="fruit_id" required>
                        <option value="">Select fruit</option>
                        @foreach ($fruits as $fruit)
                            @php
                                $defaultBox = $fruit->boxConfigurations->firstWhere('is_default', true);
                            @endphp
                            <option value="{{ $fruit->id }}" data-default-unit="{{ $fruit->default_unit_id }}" data-default-box="{{ $defaultBox?->id }}">{{ $fruit->display_name ?: $fruit->name }} ({{ $fruit->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="movement_type">Movement type</label>
                        <select class="form-control" id="movement_type" name="type" required>
                            <option value="purchase">Purchase / receive stock</option>
                            <option value="return">Return to warehouse</option>
                            <option value="issue">Issue stock</option>
                            <option value="damage">Damage / write-off</option>
                            <option value="adjustment">Stock adjustment</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="movementDirectionGroup">
                        <label for="movement_direction">Adjustment direction</label>
                        <select class="form-control" id="movement_direction" name="direction"><option value="in">Increase</option><option value="out">Decrease</option></select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="movement_unit_id">Unit</label>
                        <select class="form-control" id="movement_unit_id" name="unit_id" required>
                            <option value="">Select unit</option>
                            @foreach ($units as $unit)<option value="{{ $unit->id }}" data-code="{{ $unit->code }}">{{ $unit->name }} ({{ $unit->symbol }})</option>@endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="movementBoxGroup">
                        <label for="movement_box_configuration_id">Box configuration</label>
                        <select class="form-control" id="movement_box_configuration_id" name="box_configuration_id">
                            <option value="">Select box configuration</option>
                                            @foreach ($fruits as $fruit)
                                                @foreach ($fruit->boxConfigurations as $box)<option value="{{ $box->id }}" data-fruit-id="{{ $fruit->id }}">{{ $box->name }} — {{ $fruit->display_name ?: $fruit->name }} ({{ number_format((float) $box->weight_kg, 3) }} kg)</option>@endforeach
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6"><label for="movement_quantity">Quantity</label><input type="number" class="form-control" id="movement_quantity" name="quantity" min="0.001" step="0.001" required></div>
                    <div class="form-group col-md-6" id="movementPriceGroup"><label for="movement_unit_price">Purchase unit price (optional)</label><input type="number" class="form-control" id="movement_unit_price" name="unit_price" min="0" step="0.01"></div>
                </div>
                <div class="form-group"><label for="movement_notes">Notes</label><textarea class="form-control" id="movement_notes" name="notes" rows="2" maxlength="2000"></textarea></div>
                <button type="submit" class="btn btn-success btn-block" id="saveMovementBtn">Save Movement</button>
            </form>
        </div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const indexUrl = @json(route('admin.inventory.index'));
    const storeUrl = @json(route('admin.inventory.movements.store'));
    const params = new URLSearchParams(window.location.search);
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });

    function alertInventory(type, message) {
        $('<div>', { class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show` }).text(message)
            .append($('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert' }).append('<span>&times;</span>')).appendTo('#inventoryAlert');
    }
    function requestError(xhr, fallback) {
        const errors = xhr.responseJSON && xhr.responseJSON.errors;
        alertInventory('error', errors ? Object.values(errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
    }
    function loadStocks(url = indexUrl) {
        $('#stockTableWrapper').css('opacity', .5);
        $.get(url, { search: $('#stockSearch').val(), fruit_id: $('#stockFruitFilter').val(), status: $('#stockStatusFilter').val() })
            .done(response => $('#stockTableWrapper').html(response.html))
            .fail(xhr => requestError(xhr, 'Unable to load inventory.'))
            .always(() => $('#stockTableWrapper').css('opacity', 1));
    }
    function syncMovementOptions() {
        const fruitId = $('#movement_fruit_id').val();
        const isBox = $('#movement_unit_id option:selected').data('code') === 'BOX';
        $('#movementBoxGroup').toggle(isBox);
        $('#movement_box_configuration_id').prop('required', isBox);
        $('#movement_box_configuration_id option').each(function () {
            if (!this.value) return;
            const belongs = String($(this).data('fruit-id')) === String(fruitId);
            $(this).prop('disabled', !belongs).toggle(belongs);
        });
        $('#movementDirectionGroup').toggle($('#movement_type').val() === 'adjustment');
        $('#movementPriceGroup').toggle(['purchase', 'return'].includes($('#movement_type').val()));
    }
    if (params.get('saved') === '1') alertInventory('success', 'Inventory movement recorded successfully.');
    $(document).on('click', '#stockTableWrapper .pagination a', function (event) { event.preventDefault(); loadStocks($(this).attr('href')); });
    let searchTimer;
    $('#stockSearch').on('input', function () { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadStocks(), 300); });
    $('#stockFruitFilter, #stockStatusFilter').on('change', () => loadStocks());
    $('#resetStockFilter').on('click', function () { $('#stockSearch, #stockFruitFilter, #stockStatusFilter').val(''); loadStocks(); });
    $('#movement_fruit_id, #movement_unit_id, #movement_type').on('change', syncMovementOptions);
    $('#addMovementBtn').on('click', function () {
        $('#movementForm')[0].reset();
        const fruitId = params.get('fruit_id') || $('#stockFruitFilter').val();
        const fruitOption = $('#movement_fruit_id option').filter(function () { return String(this.value) === String(fruitId); });
        $('#movement_fruit_id').val(fruitId || '');
        const isPurchase = params.get('movement') === 'purchase';
        const boxUnit = $('#movement_unit_id option').filter(function () { return $(this).data('code') === 'BOX'; }).first();
        const unitId = isPurchase && fruitOption.data('default-box') && boxUnit.length
            ? boxUnit.val()
            : (fruitOption.data('default-unit') || '');
        $('#movement_unit_id').val(unitId);
        if (isPurchase) $('#movement_box_configuration_id').val(fruitOption.data('default-box') || '');
        if (params.has('movement')) $('#movement_type').val(params.get('movement'));
        syncMovementOptions();
        $('#movementModal').modal('show');
    });
    $('#movementForm').on('submit', function (event) {
        event.preventDefault();
        const button = $('#saveMovementBtn').prop('disabled', true).text('Saving...');
        $.ajax({ url: storeUrl, method: 'POST', data: $(this).serialize() })
            .done(() => {
                $('#movementModal').modal('hide');
                const query = new URLSearchParams();
                if ($('#stockFruitFilter').val()) query.set('fruit_id', $('#stockFruitFilter').val());
                query.set('saved', '1');
                window.location.href = indexUrl + '?' + query.toString();
            })
            .fail(xhr => requestError(xhr, 'Unable to save inventory movement.'))
            .always(() => button.prop('disabled', false).text('Save Movement'));
    });
    syncMovementOptions();
    if (params.has('movement')) $('#addMovementBtn').trigger('click');
});
</script>
@endpush
