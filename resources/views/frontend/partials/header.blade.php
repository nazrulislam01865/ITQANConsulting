<header class="header">
  <div class="container nav">
    @include('frontend.partials.brand')
    <nav class="navlinks" id="navlinks" aria-label="Main navigation">
      @foreach($navigation as $item)
        @php($isRoute = !empty($item['route']) && Route::has($item['route']))
        @php($href = $isRoute ? route($item['route']) : ($item['url'] ?? '#'))
        @php($isActive = $isRoute && request()->routeIs($item['route']))
        <a href="{{ $href }}" class="{{ $isActive ? 'active' : '' }}" @if($isActive) aria-current="page" @endif>{{ $item['label'] }}</a>
      @endforeach
    </nav>
    <div class="nav-actions">
      <button class="motion-toggle" id="motionToggle" type="button" aria-pressed="false"><span></span>Motion On</button>
      @include('frontend.partials.button', ['button' => $site['primary_cta'], 'class' => 'primary'])
      <button class="menu-btn" id="menuBtn" type="button" aria-controls="navlinks" aria-expanded="false">Menu</button>
    </div>
  </div>
</header>
