<div class="hero-space" aria-hidden="true">
  <div class="space-glow glow-a"></div>
  <div class="space-glow glow-b"></div>
  <div class="space-glow glow-c"></div>
  <div class="star-layer layer-1"></div>
  <div class="star-layer layer-2"></div>
  <div class="star-layer layer-3"></div>
  @for($i = 1; $i <= 20; $i++)
    <span class="space-particle p{{ $i }}"></span>
  @endfor
  <span class="space-comet c1"></span>
  <span class="space-comet c2"></span>
</div>
