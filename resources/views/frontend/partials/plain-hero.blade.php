<section class="hero space-hero itqan-banner" data-itqan-banner>
  @include('frontend.partials.hero-space')
  <div class="container">
    <div class="hero-copy reveal">
      <div class="label">{{ $hero['label'] }}</div>
      <h1>{{ $hero['title'] }}</h1>
      <p>{{ $hero['description'] }}</p>
    </div>
  </div>
</section>
