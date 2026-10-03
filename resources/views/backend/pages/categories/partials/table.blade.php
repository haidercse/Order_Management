<div class="table-responsive">

    <table class="table table-bordered table-hover align-middle">

        <thead class="table-light">

            <tr>

                <th width="70">
                    #
                </th>

                <th>
                    Code
                </th>

                <th>
                    Category Name
                </th>

                <th>
                    Sort Order
                </th>

                <th>
                    Status
                </th>

                <th width="150">
                    Action
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse($categories as $category)

                <tr>

                    <td>
                        {{ $categories->firstItem() + $loop->index }}
                    </td>

                    <td>
                        <strong>
                            {{ $category->code }}
                        </strong>
                    </td>

                    <td>
                        {{ $category->name }}
                    </td>

                    <td>
                        {{ $category->sort_order }}
                    </td>

                    <td>

                        @if($category->status)

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>

                        <button
                            type="button"
                            class="btn btn-sm btn-primary edit-category"
                            data-id="{{ $category->id }}"
                        >
                            <i class="fa fa-edit"></i>
                        </button>


                        <button
                            type="button"
                            class="btn btn-sm btn-danger delete-category"
                            data-id="{{ $category->id }}"
                        >
                            <i class="fa fa-trash"></i>
                        </button>

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="6"
                        class="text-center py-4"
                    >
                        No categories found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


<div class="d-flex justify-content-end">

    {{ $categories->links() }}

</div>