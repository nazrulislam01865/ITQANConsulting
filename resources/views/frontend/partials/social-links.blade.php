@isset($includeEmail)
  @if($includeEmail)
    <a class="social-link" href="mailto:{{ $site['email'] }}" aria-label="Email {{ $site['name'] }}">
      <span class="social-link-fallback" aria-hidden="true">@</span>
    </a>
  @endif
@endisset

@foreach($socialLinks as $social)
  @php
    $href = $social['url'] ?? '#';
    $isExternal = \Illuminate\Support\Str::startsWith($href, ['http://', 'https://']);
    $iconUrl = $social['icon_url'] ?? null;
    $label = $social['label'] ?? ($social['platform'] ?? 'Social link');
  @endphp
  <a class="social-link" href="{{ $href }}" aria-label="{{ $label }}" @if($isExternal) target="_blank" rel="noopener" @endif>
    @if($iconUrl)
      <img src="{{ $iconUrl }}" alt="" loading="lazy" decoding="async" width="18" height="18">
    @else
      <span class="social-link-fallback" aria-hidden="true">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($label, 0, 1)) }}</span>
    @endif
  </a>
@endforeach
