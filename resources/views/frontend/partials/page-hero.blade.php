<section class="hero space-hero">
  @include('frontend.partials.hero-space')
  <div class="container">
    <div class="hero-copy reveal">
      <div class="label">{{ $hero['label'] }}</div>
      <h1>{{ $hero['title'] }}</h1>
      <p>{{ $hero['description'] }}</p>
      @if(!empty($hero['buttons']))
        <div class="button-row">
          @foreach($hero['buttons'] as $button)
            @include('frontend.partials.button', ['button' => $button, 'class' => $button['class'] ?? ''])
          @endforeach
        </div>
      @endif
    </div>
  </div>
</section>
