@extends('frontend.layouts.app')

@push('head')
<script>window.ITQAN_CATALOG_PAGES = @json($collections['catalog_pages']);</script>
@endpush

@section('content')
<main id="catalog" class="page active">
  @include('frontend.partials.page-hero', ['hero' => $page['hero']])

  <section class="section" id="catalog-viewer">
    <div class="container">
      <div class="catalog-stage reveal" id="catalogStage">
        <div class="catalog-top">
          <div><div class="eyebrow">{{ $page['viewer']['eyebrow'] ?? 'Catalog Viewer' }}</div><div class="catalog-title" id="catalogTitle">{{ $page['viewer']['title'] ?? 'ITQAN Service Profile' }}</div></div>
          <div class="catalog-actions"><button class="btn small" id="thumbToggle" type="button">Thumbnails</button><button class="btn small" id="fullBtn" type="button">Fullscreen</button><button class="btn small" id="muteBtn" type="button">Mute</button><a class="btn small" href="{{ route('catalog.download') }}">Download PDF</a></div>
        </div>
        <div class="book-wrap" id="bookWrap">
          <button class="catalog-control prev" id="prevPage" type="button" aria-label="Previous page">‹</button>
          <div class="book" id="book">
            <article class="page-sheet left" id="leftPage"></article>
            <article class="page-sheet right" id="rightPage"></article>
          </div>
          <button class="catalog-control next" id="nextPage" type="button" aria-label="Next page">›</button>
        </div>
        <div class="page-indicator" id="pageIndicator">Page 1 of {{ count($collections['catalog_pages']) }}</div>
        <div class="thumbs" id="thumbs" aria-label="Catalog thumbnails"></div>
      </div>
    </div>
  </section>
</main>
@endsection
