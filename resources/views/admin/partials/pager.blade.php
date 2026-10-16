<div class="list-foot">
    <p>
        @if ($paginator->total() === 0)
            Showing 0 results
        @else
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        @endif
    </p>
    {{ $paginator->links('pagination.admin') }}
</div>
