@if ($paginator->hasPages())
    <div class="table-pagination">
        <div class="table-pagination-summary">
            Showing <strong>{{ $paginator->firstItem() }}</strong>
            to <strong>{{ $paginator->lastItem() }}</strong>
            of <strong>{{ $paginator->total() }}</strong> results
        </div>

        <nav aria-label="Pagination">
            <ul class="pagination table-pagination-controls">
                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link"><span aria-hidden="true">&lsaquo;</span> Previous</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                            <span aria-hidden="true">&lsaquo;</span> Previous
                        </a>
                    </li>
                @endif

                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">
                            Next <span aria-hidden="true">&rsaquo;</span>
                        </a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">Next <span aria-hidden="true">&rsaquo;</span></span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif
