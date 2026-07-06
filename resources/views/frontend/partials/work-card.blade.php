@php
  $categories = isset($work['categories']) ? implode(' ', $work['categories']) : '';
  $buttonLabel = $buttonText ?? ($work['button_text'] ?? 'View Case Study');
  $buttonUrl = $work['button_url'] ?? null;
  $buttonRoute = $work['button_route'] ?? null;
  $buttonHref = '#';
  $workImageUrl = $work['image_url'] ?? null;

  if (!empty($buttonUrl)) {
      $buttonHref = $buttonUrl;
  } elseif (!empty($buttonRoute) && \Illuminate\Support\Facades\Route::has($buttonRoute)) {
      $buttonHref = route($buttonRoute);
  }
@endphp

<article class="work-card reveal" @if($categories) data-cats="{{ $categories }}" @endif>
  <div class="work-visual {{ $workImageUrl ? 'has-image' : '' }}">
    @if($workImageUrl)
      <img src="{{ $workImageUrl }}" alt="{{ $work['title'] ?? 'Work image' }}">
    @endif
  </div>
  <div class="work-copy">
    <span class="pill">{{ $pill ?? ($work['pill'] ?? '') }}</span>
    <h3>{{ $work['title'] ?? '' }}</h3>
    <p>{{ $description ?? ($work['description'] ?? '') }}</p>
    @isset($showTags)
      @if($showTags && !empty($work['tags']))
        <div class="meta">
          @foreach($work['tags'] as $tag)<span>{{ $tag }}</span>@endforeach
        </div>
      @endif
    @endisset
    <a class="btn ghost-light small" href="{{ $buttonHref }}">{{ $buttonLabel }}</a>
  </div>
</article>
