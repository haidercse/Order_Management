@extends('backend.layouts.master')

@section('title', 'Manage Categories')

@section('admin-content')

<div class="container-fluid my-4">

    <h3 class="mb-4 text-primary fw-bold">
        Manage Categories
    </h3>

    {{-- Alert --}}
    <div id="alertBox"></div>

    {{-- Header --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-6">
                    <h5 class="mb-0">
                        Category List
                    </h5>
                </div>

                <div class="col-md-6 text-end">

                    <button
                        type="button"
                        class="btn btn-success"
                        id="addCategoryBtn"
                    >
                        <i class="fa fa-plus"></i>
                        Add New Category
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
                        id="categorySearch"
                        class="form-control"
                        placeholder="🔍 Search category..."
                    >

                </div>

                <div class="col-md-4">

                    <select
                        id="categoryStatus"
                        class="form-control"
                    >

                        <option value="">
                            -- All Status --
                        </option>

                        <option value="1">
                            Active
                        </option>

                        <option value="0">
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <button
                        type="button"
                        class="btn btn-secondary w-100"
                        id="resetCategoryFilter"
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
            Category Records
        </div>

        <div
            class="card-body"
            id="categoryTableWrapper"
        >

            @include(
                'backend.pages.categories.partials.table',
                ['categories' => $categories]
            )

        </div>

    </div>

</div>


{{-- Category Modal --}}
<div
    class="modal fade"
    id="categoryModal"
    tabindex="-1"
>

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header bg-success text-white">

                <h5
                    class="modal-title"
                    id="categoryModalTitle"
                >
                    Add Category
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <form id="categoryForm">

                    @csrf

                    <input
                        type="hidden"
                        id="category_id"
                    >


                    <div class="mb-3">

                        <label class="form-label">
                            Category Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Category Code
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="code"
                            class="form-control"
                            required
                        >

                    </div>


                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Sort Order
                            </label>

                            <input
                                type="number"
                                name="sort_order"
                                id="sort_order"
                                class="form-control"
                                min="0"
                                value="0"
                                required
                            >

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

                                <option value="1">
                                    Active
                                </option>

                                <option value="0">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="btn btn-success w-100"
                        id="saveCategoryBtn"
                    >
                        Save Category
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

        setTimeout(function () {

            $('#alertBox .alert').fadeOut(
                300,
                function () {
                    $(this).remove();
                }
            );

        }, 4000);
    }


    /*
    |--------------------------------------------------------------------------
    | Load Categories
    |--------------------------------------------------------------------------
    */

    function loadCategories(
        url = "{{ route('admin.categories.index') }}"
    )
    {

        let search = $('#categorySearch').val();
        let status = $('#categoryStatus').val();

        $.ajax({

            url: url,

            type: 'GET',

            data: {
                search: search,
                status: status
            },

            beforeSend: function () {

                $('#categoryTableWrapper').css(
                    'opacity',
                    '0.5'
                );

            },

            success: function (response) {

                $('#categoryTableWrapper').html(
                    response.html
                );

            },

            error: function (xhr) {

                console.error(xhr.responseText);

                showAlert(
                    'error',
                    'Something went wrong while loading categories.'
                );

            },

            complete: function () {

                $('#categoryTableWrapper').css(
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
        '#categoryTableWrapper .pagination a',
        function (e) {

            e.preventDefault();

            let url = $(this).attr('href');

            loadCategories(url);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    let searchTimer;

    $('#categorySearch').on('keyup', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {

            loadCategories();

        }, 300);

    });


    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    $('#categoryStatus').on('change', function () {

        loadCategories();

    });


    /*
    |--------------------------------------------------------------------------
    | Reset Filter
    |--------------------------------------------------------------------------
    */

    $('#resetCategoryFilter').on('click', function () {

        $('#categorySearch').val('');

        $('#categoryStatus').val('');

        loadCategories();

    });


    /*
    |--------------------------------------------------------------------------
    | Add Category
    |--------------------------------------------------------------------------
    */

    $('#addCategoryBtn').on('click', function () {

        $('#categoryForm')[0].reset();

        $('#category_id').val('');

        $('#sort_order').val(0);

        $('#status').val(1);

        $('#categoryModalTitle').text(
            'Add Category'
        );

        $('#saveCategoryBtn').text(
            'Save Category'
        );

        $('#categoryModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Save / Update Category
    |--------------------------------------------------------------------------
    */

    $('#categoryForm').on('submit', function (e) {

        e.preventDefault();

        let id = $('#category_id').val();

        let url = id

            ? `/admin/categories/update/${id}`

            : "{{ route('admin.categories.store') }}";

        let form = $(this);

        let button = $('#saveCategoryBtn');

        button.prop('disabled', true);

        button.text('Saving...');


        $.ajax({

            url: url,

            type: 'POST',

            data: form.serialize(),

            success: function (response) {

                $('#categoryModal').modal('hide');

                showAlert(
                    'success',
                    response.message
                );

                loadCategories();

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

                    showAlert(
                        'error',
                        message
                    );

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
                        ? 'Update Category'
                        : 'Save Category'
                );

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Edit Category
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.edit-category',
        function () {

            let id = $(this).data('id');

            $.ajax({

                url: `/admin/categories/edit/${id}`,

                type: 'GET',

                success: function (response) {

                    let category = response.data;


                    $('#category_id').val(
                        category.id
                    );

                    $('#name').val(
                        category.name
                    );

                    $('#code').val(
                        category.code
                    );

                    $('#sort_order').val(
                        category.sort_order
                    );

                    $('#status').val(
                        category.status ? 1 : 0
                    );


                    $('#categoryModalTitle').text(
                        'Edit Category'
                    );

                    $('#saveCategoryBtn').text(
                        'Update Category'
                    );


                    $('#categoryModal').modal(
                        'show'
                    );

                },

                error: function () {

                    showAlert(
                        'error',
                        'Unable to load category data.'
                    );

                }

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Delete Category
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.delete-category',
        function () {

            if (
                !confirm(
                    'Are you sure you want to delete this category?'
                )
            ) {

                return;

            }


            let id = $(this).data('id');


            $.ajax({

                url: `/admin/categories/delete/${id}`,

                type: 'DELETE',

                success: function (response) {

                    showAlert(
                        'success',
                        response.message
                    );

                    loadCategories();

                },

                error: function (xhr) {

                    if (xhr.status === 422) {

                        showAlert(
                            'error',
                            xhr.responseJSON.message
                                ?? 'Unable to delete category.'
                        );

                    } else {

                        showAlert(
                            'error',
                            'Unable to delete category.'
                        );

                    }

                }

            });

        }
    );

});

</script>

@endpush