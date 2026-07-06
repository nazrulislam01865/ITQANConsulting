@extends('frontend.layouts.app')

@section('content')
<main id="home" class="page active">
  <section class="hero home-hero">
    @include('frontend.partials.hero-space')
    <div class="container hero-grid">
      <div class="hero-copy reveal">
        <div class="label">{{ $page['hero']['label'] }}</div>
        <h1>{!! $page['hero']['title'] !!}</h1>
        <p>{{ $page['hero']['description'] }}</p>
        <div class="short-lines" aria-label="Hero message">
          @foreach($page['hero']['chips'] as $chip)<span>{{ $chip }}</span>@endforeach
        </div>
        <div class="button-row">
          @foreach($page['hero']['buttons'] as $button)
            @include('frontend.partials.button', ['button' => $button, 'class' => $button['class'] ?? ''])
          @endforeach
        </div>
        <div class="social-row with-label">
          <span>{{ $page['hero']['social_label'] }}</span>
          <div class="social-row">@include('frontend.partials.social-links')</div>
        </div>
      </div>
    </div>
    <section class="code-ticker-wrap" aria-label="Code style message slider">
      <div class="code-ticker">
        <div class="code-track">
          @foreach(array_merge($page['hero']['ticker'], $page['hero']['ticker']) as $item)
            <span class="code-item">{!! $item !!}</span>
          @endforeach
        </div>
      </div>
    </section>
  </section>

  <section class="section founder-message-section">
    <div class="container founder-message-card reveal">
      <div class="founder-message-copy">
        <div class="label">{{ $page['founder']['label'] }}</div>
        <h2 class="founder-message-title">{{ $page['founder']['title'] }}</h2>
        <div class="founder-message-body">
          @foreach($page['founder']['paragraphs'] as $paragraph)
            <p>{{ $paragraph }}</p>
          @endforeach
        </div>
        <div class="founder-message-signature">
          @if(! empty($page['founder']['image_url']))
            <img src="{{ $page['founder']['image_url'] }}" alt="{{ $page['founder']['name'] }}" loading="lazy">
          @else
            <span class="founder-message-avatar-placeholder" aria-hidden="true">IC</span>
          @endif
          <div>
            <strong>{{ $page['founder']['name'] }}</strong>
            <span>{{ $page['founder']['role'] }}</span>
          </div>
        </div>
      </div>
      <div class="founder-message-art" aria-hidden="true">
        <span class="founder-dot-grid"></span>
        <span class="founder-outline founder-outline-one"></span>
        <span class="founder-outline founder-outline-two"></span>
        <span class="founder-outline founder-outline-three"></span>
      </div>
    </div>
  </section>

  <section class="section light">
    <div class="container">
      @include('frontend.partials.section-head', ['label' => $page['who']['label'], 'title' => $page['who']['title'], 'lead' => $page['who']['lead']])
      <div class="card-grid three">
        @foreach($page['who']['cards'] as $card)
          <article class="card reveal"><span class="num">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $card['title'] }}</h3><p>{{ $card['text'] }}</p></article>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section soft-white">
    <div class="container">
      @include('frontend.partials.section-head', ['label' => $page['problems']['label'], 'title' => $page['problems']['title'], 'lead' => $page['problems']['lead']])
      <div class="problem-strip" aria-label="Problem response cards">
        @foreach($page['problems']['items'] as $item)
          <div class="problem-card reveal"><div><b>Problem</b><p>{{ $item['problem'] }}</p></div><div class="response"><b>ITQAN Response</b><p>{{ $item['response'] }}</p></div></div>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section" id="home-services-preview">
    <div class="container">
      @include('frontend.partials.section-head', ['label' => $page['services_preview']['label'], 'title' => $page['services_preview']['title'], 'lead' => $page['services_preview']['lead']])
      <div class="card-grid">
        @foreach($page['services_preview']['items'] as $item)
          <article class="glass-card reveal"><div class="icon-circle">{{ $item['num'] }}</div><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
        @endforeach
      </div>
      <div class="button-row" style="margin-top:26px">@include('frontend.partials.button', ['button' => $page['services_preview']['button'], 'class' => 'primary'])</div>
    </div>
  </section>

  <section class="section dark-panel working-section">
    <div class="container">
      <div class="working-head reveal">
        <div><div class="label">{{ $page['working']['label'] }}</div><h2 class="headline">{!! $page['working']['title'] !!}</h2></div>
        <p class="working-intro">{{ $page['working']['intro'] }}</p>
      </div>
      <div class="working-board reveal">
        <div class="working-cards">
          @foreach($page['working']['items'] as $item)
            <article class="working-card"><span class="working-no">{{ $item['num'] }}</span><h3>{{ $item['title'] }}</h3><p>{{ $item['text'] }}</p></article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="section light">
    <div class="container">
      <div class="section-head reveal"><div><div class="label">{{ $page['testimonials']['label'] }}</div><h2 class="headline">{{ $page['testimonials']['title'] }}</h2></div></div>
      <div class="testimonial-slider reveal" aria-label="Client words sliding testimonials">
        <div class="testimonial-track">
          @foreach(array_merge($collections['testimonials'], $collections['testimonials']) as $testimonial)
            <article class="card testimonial-card"><div><div class="quote-mark">“</div><h3>{{ $testimonial['title'] }}</h3><p>{{ $testimonial['text'] }}</p></div><p class="testimonial-author"><strong>{{ $testimonial['author'] }}</strong>{{ $testimonial['role'] }}</p></article>
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <section class="section soft-white">
    <div class="container">
      @include('frontend.partials.section-head', ['label' => $page['works_preview']['label'], 'title' => $page['works_preview']['title'], 'sectionButton' => array_merge($page['works_preview']['button'], ['class' => 'dark'])])
      <div class="card-grid two">
        @foreach(($collections['home_featured_works'] ?? array_slice($collections['works'] ?? [], 0, 4)) as $work)
          @include('frontend.partials.work-card', ['work' => $work, 'pill' => $work['preview_pill'] ?? $work['pill'] ?? '', 'description' => $work['preview_description'] ?? $work['description'] ?? ''])
        @endforeach
      </div>
    </div>
  </section>

  @include('frontend.partials.cta', ['cta' => $page['cta']])
</main>
@endsection
