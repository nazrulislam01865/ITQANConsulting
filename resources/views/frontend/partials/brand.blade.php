@php
  $brandHref = isset($href) ? $href : route('home');
  $brandName = $site['name'] ?? 'ITQAN Consulting';
  $brandTagline = $site['tagline'] ?? '';
  $brandLogo = $site['logo_url'] ?? null;
@endphp

<a class="brand {{ $brandLogo ? 'brand-image-only' : '' }}" href="{{ $brandHref }}" aria-label="{{ $brandName }} home">
  @if($brandLogo)
    <img class="brand-logo-image" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
  @else
    <span class="mark">{{ $site['mark'] ?? 'IC' }}</span>
    <span>{{ $brandName }}<small>{{ $brandTagline }}</small></span>
  @endif
</a>
