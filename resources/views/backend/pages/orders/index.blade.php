@extends('backend.layouts.master')

@section('title', 'Shop Orders')

@section('admin-content')
<div class="container-fluid my-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div><h3 class="mb-1 text-primary font-weight-bold">Shop Orders</h3><p class="text-muted mb-0">Review submitted orders and advance warehouse fulfilment status.</p></div>
        <a href="{{ route('admin.shortage.index') }}" class="btn btn-outline-warning mt-2 mt-md-0"><i class="fa fa-exclamation-triangle"></i> Stock shortages</a>
    </div>
    <div id="ordersAlert"></div>
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 mb-2 mb-md-0"><input type="search" id="orderSearch" class="form-control" value="{{ $filters['search'] ?? '' }}" placeholder="Search shop name or code"></div>
                <div class="col-md-3 mb-2 mb-md-0"><input type="date" id="orderDateFilter" class="form-control" value="{{ $filters['date'] ?? today()->toDateString() }}"></div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select id="orderStatusFilter" class="form-control">
                        <option value="">All statuses</option>
                        @foreach (['submitted' => 'Submitted', 'prepared' => 'Prepared', 'ready' => 'Ready', 'sent' => 'Delivered'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"><button type="button" class="btn btn-secondary btn-block" id="resetOrderFilter">Reset</button></div>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">Orders</div>
        <div class="card-body" id="ordersTableWrapper">@include('backend.pages.orders.partials.table', ['orders' => $orders])</div>
    </div>
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title" id="orderDetailsTitle">Order Details</h5><button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body" id="orderDetailsContent"></div>
    </div></div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    const indexUrl = @json(route('admin.orders.index'));
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': @json(csrf_token()) } });
    function alertOrders(type, message) {
        $('<div>', { class: `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show` }).text(message)
            .append($('<button>', { type: 'button', class: 'close', 'data-dismiss': 'alert' }).append('<span>&times;</span>')).appendTo('#ordersAlert');
    }
    function requestError(xhr, fallback) {
        const errors = xhr.responseJSON && xhr.responseJSON.errors;
        alertOrders('error', errors ? Object.values(errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || fallback));
    }
    function loadOrders(url = indexUrl) {
        $('#ordersTableWrapper').css('opacity', .5);
        $.get(url, { search: $('#orderSearch').val(), date: $('#orderDateFilter').val(), status: $('#orderStatusFilter').val() })
            .done(response => $('#ordersTableWrapper').html(response.html))
            .fail(xhr => requestError(xhr, 'Unable to load orders.'))
            .always(() => $('#ordersTableWrapper').css('opacity', 1));
    }
    $(document).on('click', '#ordersTableWrapper .pagination a', function (event) { event.preventDefault(); loadOrders($(this).attr('href')); });
    let searchTimer;
    $('#orderSearch').on('input', function () { clearTimeout(searchTimer); searchTimer = setTimeout(() => loadOrders(), 300); });
    $('#orderDateFilter, #orderStatusFilter').on('change', () => loadOrders());
    $('#resetOrderFilter').on('click', function () {
        $('#orderSearch').val(''); $('#orderDateFilter').val(''); $('#orderStatusFilter').val(''); loadOrders();
    });
    $(document).on('click', '.view-order', function () {
        $.get(`/admin/orders/${$(this).data('id')}`)
            .done(response => { $('#orderDetailsContent').html(response.html); $('#orderDetailsModal').modal('show'); })
            .fail(xhr => requestError(xhr, 'Unable to load order details.'));
    });
    $(document).on('click', '.advance-order', function () {
        const button = $(this); const id = button.data('id'); const status = button.data('status');
        if (status === 'sent' && !window.confirm('Mark this order delivered and deduct its demand from warehouse stock?')) return;
        button.prop('disabled', true);
        $.ajax({ url: `/admin/orders/${id}/status`, method: 'POST', data: { status } })
            .done(response => { alertOrders('success', response.message); loadOrders(); })
            .fail(xhr => requestError(xhr, 'Unable to update order status.'))
            .always(() => button.prop('disabled', false));
    });
});
</script>
@endpush
