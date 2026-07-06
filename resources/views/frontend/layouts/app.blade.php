<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $page['title'] ?? $site['name'] }}</title>
  <meta name="description" content="{{ $page['meta_description'] ?? $site['description'] }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/itqan-template.css') }}?v=logo-2">
  @vite(['resources/js/app.js'])
  @stack('head')
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>
  <div class="site-bg" aria-hidden="true"></div>
  <div class="grain" aria-hidden="true"></div>

  @include('frontend.partials.header')

  <div id="main-content">
    @yield('content')
  </div>

  @include('frontend.partials.footer')
  @stack('scripts')
</body>
</html>
