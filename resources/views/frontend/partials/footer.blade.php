<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        @include('frontend.partials.brand')
        <p style="margin-top:18px">{{ $site['description'] }}</p>
      </div>
      @if(!empty($footer['menus']))
        @foreach($footer['menus'] as $menu)
          <div>
            <h3>{{ $menu['title'] }}</h3>
            <ul>
              @foreach($menu['items'] as $item)
                <li>
                  @if(!empty($item['route']) && Route::has($item['route']))
                    <a href="{{ route($item['route']) }}">{{ $item['label'] }}</a>
                  @elseif(!empty($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                  @else
                    {{ $item['label'] }}
                  @endif
                </li>
              @endforeach
            </ul>
          </div>
        @endforeach
      @else
        <div>
          <h3>Pages</h3>
          <ul>
            @foreach($navigation as $item)
              <li><a href="{{ route($item['route']) }}">{{ $item['label'] === 'Catalog' ? 'Digital Catalog' : $item['label'] }}</a></li>
            @endforeach
          </ul>
        </div>
        <div>
          <h3>Services</h3>
          <ul>
            @foreach($footer['service_links'] as $service)
              <li>{{ $service }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div>
        <h3>Contact</h3>
        <ul>
          <li><a href="mailto:{{ $site['email'] }}">{{ $site['email'] }}</a></li>
          <li>{{ $site['address'] }}</li>
        </ul>
        <div class="social-row">
          @include('frontend.partials.social-links')
        </div>
      </div>
    </div>
    <div class="footer-bottom"><span>{{ $footer['bottom_left'] }}</span><span>{{ $footer['copyright'] }}</span></div>
  </div>
</footer>
