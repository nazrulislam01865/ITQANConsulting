@if ($paginator->hasPages())
  <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">
    <div class="admin-pagination-summary">
      Showing
      <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
      to
      <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
      of
      <strong>{{ $paginator->total() }}</strong>
      results
    </div>

    <div class="admin-pagination-links">
      @if ($paginator->onFirstPage())
        <span class="admin-pagination-button is-disabled" aria-disabled="true">Previous</span>
      @else
        <a class="admin-pagination-button" href="{{ $paginator->previousPageUrl() }}" rel="prev">Previous</a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="admin-pagination-ellipsis" aria-hidden="true">{{ $element }}</span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="admin-pagination-page is-current" aria-current="page">{{ $page }}</span>
            @else
              <a class="admin-pagination-page" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a class="admin-pagination-button" href="{{ $paginator->nextPageUrl() }}" rel="next">Next</a>
      @else
        <span class="admin-pagination-button is-disabled" aria-disabled="true">Next</span>
      @endif
    </div>
  </nav>
@endif
