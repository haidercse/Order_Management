@extends('backend.layouts.master')

@section('title', 'Manage Shops')

@section('admin-content')

<div class="container-fluid my-4">

    <h3 class="mb-4 text-primary fw-bold">
        Manage Shops
    </h3>


    {{-- Alert --}}

    <div id="alertBox"></div>


    {{-- Header --}}

    <div class="card shadow-sm mb-3">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-6">

                    <h5 class="mb-0">
                        Shop List
                    </h5>

                </div>

                <div class="col-md-6 text-end">

                    <button
                        type="button"
                        class="btn btn-success"
                        id="addShopBtn"
                    >
                        <i class="fa fa-plus"></i>
                        Add New Shop
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- Search / Filter --}}

    <div class="card shadow-sm mb-3">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <input
                        type="text"
                        id="shopSearch"
                        class="form-control"
                        placeholder="🔍 Search shop..."
                    >

                </div>


                <div class="col-md-4">

                    <select
                        id="shopStatus"
                        class="form-control"
                    >

                        <option value="">
                            -- All Status --
                        </option>

                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
                            Inactive
                        </option>

                    </select>

                </div>


                <div class="col-md-2">

                    <button
                        type="button"
                        class="btn btn-secondary w-100"
                        id="resetShopFilter"
                    >
                        Reset
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- Table --}}

    <div class="card shadow-sm">

        <div class="card-header bg-primary text-white">
            Shop Records
        </div>

        <div
            class="card-body"
            id="shopTableWrapper"
        >

            @include(
                'backend.pages.shops.partials.table',
                ['shops' => $shops]
            )

        </div>

    </div>

</div>


{{-- Shop Modal --}}

<div
    class="modal fade"
    id="shopModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5
                    class="modal-title"
                    id="shopModalTitle"
                >
                    Add Shop
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <form id="shopForm">

                    @csrf

                    <input
                        type="hidden"
                        id="shop_id"
                    >


                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Shop Code
                            </label>

                            <input
                                type="text"
                                name="code"
                                id="code"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Shop Name
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                required
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Manager Name
                            </label>

                            <input
                                type="text"
                                name="manager_name"
                                id="manager_name"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Phone
                            </label>

                            <input
                                type="text"
                                name="phone"
                                id="phone"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Shop Contact Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager Login Email</label>
                            <input type="email" name="manager_login_email" id="manager_login_email" class="form-control" required autocomplete="off">
                            <small class="text-muted">This is the manager's username; it can differ from the shop contact email.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Manager Password <span id="managerPasswordHint">(optional; defaults to 12345678 for a new manager)</span></label>
                            <input type="password" name="manager_password" id="manager_password" class="form-control" minlength="8" autocomplete="new-password">
                            <small class="text-muted">Leave blank when editing to keep the current password. If entering a password, confirm it below.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm Manager Password</label>
                            <input type="password" name="manager_password_confirmation" id="manager_password_confirmation" class="form-control" minlength="8" autocomplete="new-password">
                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                City
                            </label>

                            <input
                                type="text"
                                name="city"
                                id="city"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Address
                            </label>

                            <textarea
                                name="address"
                                id="address"
                                class="form-control"
                                rows="2"
                            ></textarea>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-control"
                                required
                            >

                                <option value="active">
                                    Active
                                </option>

                                <option value="inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                        <div class="col-md-12 mb-3">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                id="notes"
                                class="form-control"
                                rows="3"
                            ></textarea>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-success w-100"
                        id="saveShopBtn"
                    >
                        Save Shop
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection
@push('scripts')

<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | CSRF
    |--------------------------------------------------------------------------
    */

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Alert
    |--------------------------------------------------------------------------
    */

    function showAlert(type, message)
    {
        let alertClass =
            type === 'success'
                ? 'alert-success'
                : 'alert-danger';

        $('#alertBox').html(`
            <div class="alert ${alertClass} alert-dismissible fade show">

                ${message}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>

            </div>
        `);

        if (type !== 'success' || (!message.includes('Temporary password:') && !message.includes('Default password:'))) {
            setTimeout(function () {

                $('#alertBox .alert').fadeOut(
                    300,
                    function () {
                        $(this).remove();
                    }
                );

            }, 4000);
        }
    }

    function escapeHtml(value)
    {
        return $('<div>').text(value ?? '').html();
    }


    /*
    |--------------------------------------------------------------------------
    | Load Shops
    |--------------------------------------------------------------------------
    */

    function loadShops(url = "{{ route('admin.shops.index') }}")
    {

        let search = $('#shopSearch').val();
        let status = $('#shopStatus').val();


        $.ajax({

            url: url,

            type: 'GET',

            data: {
                search: search,
                status: status
            },

            beforeSend: function () {

                $('#shopTableWrapper').css(
                    'opacity',
                    '0.5'
                );

            },

            success: function (response) {

                $('#shopTableWrapper').html(
                    response.html
                );

            },

            error: function (xhr) {

                console.error(xhr.responseText);

                showAlert(
                    'error',
                    'Something went wrong while loading shops.'
                );

            },

            complete: function () {

                $('#shopTableWrapper').css(
                    'opacity',
                    '1'
                );

            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | AJAX Pagination
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '#shopTableWrapper .pagination a',
        function (e) {

            e.preventDefault();

            let url = $(this).attr('href');

            loadShops(url);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    let searchTimer;

    $('#shopSearch').on('keyup', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {

            loadShops();

        }, 300);

    });


    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    $('#shopStatus').on('change', function () {

        loadShops();

    });


    /*
    |--------------------------------------------------------------------------
    | Reset Filter
    |--------------------------------------------------------------------------
    */

    $('#resetShopFilter').on('click', function () {

        $('#shopSearch').val('');

        $('#shopStatus').val('');

        loadShops();

    });


    /*
    |--------------------------------------------------------------------------
    | Add Shop
    |--------------------------------------------------------------------------
    */

    $('#addShopBtn').on('click', function () {

        $('#shopForm')[0].reset();

        $('#shop_id').val('');
        $('#manager_password').prop('required', false);
        $('#manager_password_confirmation').prop('required', false);
        $('#managerPasswordHint').text('(optional; defaults to 12345678 for a new manager)');

        $('#shopModalTitle').text(
            'Add Shop'
        );

        $('#saveShopBtn').text(
            'Save Shop'
        );

        $('#shopModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Save / Update Shop
    |--------------------------------------------------------------------------
    */

    $('#shopForm').on('submit', function (e) {

        e.preventDefault();


        let id = $('#shop_id').val();


        let url = id

            ? `/admin/shops/update/${id}`

            : "{{ route('admin.shops.store') }}";


        let form = $(this);

        let button = $('#saveShopBtn');


        button.prop('disabled', true);

        button.text('Saving...');


        $.ajax({

            url: url,

            type: 'POST',

            data: form.serialize(),

            success: function (response) {

                $('#shopModal').modal('hide');

                const credentials = response.credentials
                    ? `<br><strong>Share once with the manager:</strong><br>Email: ${escapeHtml(response.credentials.email)}<br>Temporary password: <code>${escapeHtml(response.credentials.password)}</code>`
                    : '';
                showAlert('success', escapeHtml(response.message) + credentials);

                loadShops();

            },

            error: function (xhr) {

                if (xhr.status === 422) {

                    let errors =
                        xhr.responseJSON.errors;

                    let message = '';

                    $.each(
                        errors,
                        function (key, value) {

                            message +=
                                value[0] + '<br>';

                        }
                    );

                    showAlert('error', escapeHtml(message));

                } else {

                    showAlert(
                        'error',
                        'Something went wrong.'
                    );

                }

            },

            complete: function () {

                button.prop(
                    'disabled',
                    false
                );

                button.text(
                    id
                        ? 'Update Shop'
                        : 'Save Shop'
                );

            }

        });

        $('#manager_password').on('input', function () {
            const hasPassword = $(this).val().length > 0;
            $('#manager_password_confirmation').prop('required', hasPassword);
        });

    });


    /*
    |--------------------------------------------------------------------------
    | Edit Shop
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.edit-shop',
        function () {

            let id = $(this).data('id');


            $.ajax({

                url: `/admin/shops/edit/${id}`,

                type: 'GET',

                success: function (response) {

                    let shop = response.data;


                    $('#shop_id').val(
                        shop.id
                    );

                    $('#code').val(
                        shop.code
                    );

                    $('#name').val(
                        shop.name
                    );

                    $('#manager_name').val(
                        shop.manager_name
                    );

                    $('#phone').val(
                        shop.phone
                    );

                    $('#email').val(
                        shop.email
                    );

                    $('#manager_login_email').val(shop.manager_login_email || '');
                    $('#manager_password').val('');
                    $('#manager_password_confirmation').val('');
                    $('#manager_password').prop('required', false);
                    $('#managerPasswordHint').text(shop.manager_login_email
                        ? '(optional; blank keeps the current password)'
                        : '(optional; defaults to 12345678 when creating the manager login)');
                    $('#manager_password_confirmation').prop('required', false);

                    $('#address').val(
                        shop.address
                    );

                    $('#city').val(
                        shop.city
                    );

                    $('#status').val(
                        shop.status
                    );

                    $('#notes').val(
                        shop.notes
                    );


                    $('#shopModalTitle').text(
                        'Edit Shop'
                    );
                    $('#saveShopBtn').text(
                        'Update Shop'
                    );

                    $('#shopModal').modal(
                        'show'
                    );

                },

                error: function () {

                    showAlert(
                        'error',
                        'Unable to load shop data.'
                    );

                }

            });

        }
    );

    $(document).on('click', '.reset-manager-password', function () {
        const id = $(this).data('id');
        if (!confirm('Reset this shop manager password to 12345678? Their next login will require changing it.')) {
            return;
        }

        $.ajax({
            url: `/admin/shops/${id}/reset-manager-password`,
            type: 'POST',
            success: function (response) {
                const credentials = `<br><strong>Share once with the manager:</strong><br>Email: ${escapeHtml(response.credentials.email)}<br>Default password: <code>${escapeHtml(response.credentials.password)}</code>`;
                showAlert('success', escapeHtml(response.message) + credentials);
            },
            error: function () {
                showAlert('error', 'Unable to reset the manager password.');
            }
        });
    });


    /*
    |--------------------------------------------------------------------------
    | Delete Shop
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.delete-shop',
        function () {

            if (
                !confirm(
                    'Are you sure you want to delete this shop?'
                )
            ) {
                return;
            }


            let id = $(this).data('id');


            $.ajax({

                url: `/admin/shops/delete/${id}`,

                type: 'DELETE',

                success: function (response) {

                    showAlert(
                        'success',
                        response.message
                    );

                    loadShops();

                },

                error: function () {

                    showAlert(
                        'error',
                        'Unable to delete shop.'
                    );

                }

            });

        }
    );

});

</script>

@endpush