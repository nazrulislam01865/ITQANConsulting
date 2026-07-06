@extends('frontend.layouts.app')

@section('content')
<main id="about" class="page active">
  @include('frontend.partials.page-hero', ['hero' => $page['hero']])

  <section class="section light">
    <div class="container split top">
      <div class="reveal"><div class="label">{{ $page['story']['label'] }}</div><h2 class="headline">{{ $page['story']['title'] }}</h2></div>
      <div class="reveal story-copy">
        @foreach($page['story']['paragraphs'] as $paragraph)<p class="lead">{{ $paragraph }}</p>@endforeach
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="section-head reveal"><div><div class="label">{{ $page['beliefs']['label'] }}</div><h2 class="headline">{{ $page['beliefs']['title'] }}</h2></div></div>
      <div class="card-grid four">
        @foreach($page['beliefs']['items'] as $item)
          <article class="glass-card reveal"><div class="icon-circle">{{ $item['num'] }}</div><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section soft-white">
    <div class="container card-grid two">
      @foreach($page['mission_vision'] as $card)
        <article class="card reveal"><span class="num">{{ $card['num'] }}</span><h3>{{ $card['title'] }}</h3><p>{{ $card['text'] }}</p></article>
      @endforeach
    </div>
  </section>

  @include('frontend.partials.cta', ['cta' => $page['cta']])
</main>
@endsection
