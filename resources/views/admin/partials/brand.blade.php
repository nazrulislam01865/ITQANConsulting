@php
  $brandHref = $href ?? route('admin.dashboard');
  $brandName = $adminSite['name'] ?? 'ITQAN Consulting';
  $brandTagline = $adminSite['tagline'] ?? 'Sincere Services. Lasting Results.';
  $brandLogo = $adminSite['logo_url'] ?? null;
  $fallbackTitle = $title ?? 'ITQAN Admin';
  $fallbackSubtitle = $subtitle ?? 'Backend Control';
@endphp

<a class="admin-brand {{ $brandLogo ? 'admin-brand-image-only' : '' }}" href="{{ $brandHref }}" aria-label="{{ $brandName }} admin home">
  @if($brandLogo)
    <img class="admin-brand-logo" src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
  @else
    <span class="admin-mark">IC</span>
    <span>{{ $fallbackTitle }}<small>{{ $fallbackSubtitle }}</small></span>
  @endif
</a>
