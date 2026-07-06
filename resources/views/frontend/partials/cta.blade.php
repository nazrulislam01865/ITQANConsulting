<section class="section">
  <div class="container cta-box reveal">
    <h2>{{ $cta['title'] }}</h2>
    <p>{{ $cta['text'] }}</p>
    @include('frontend.partials.button', ['button' => $cta['button'], 'class' => 'primary'])
  </div>
</section>
