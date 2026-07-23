<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  @include('frontend.partials.favicon')
  <title>{{ $page['title'] ?? $site['name'] }}</title>
  <meta name="description" content="{{ $page['meta_description'] ?? $site['description'] }}" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/itqan-template.css') }}?v=founder-resume-minimal-2">
  @vite(['resources/js/app.js'])
  @stack('head')
</head>
<body>
  <a class="skip-link" href="#main-content">Skip to content</a>
  <div class="scroll-progress" id="scrollProgress" aria-hidden="true"><span></span></div>
  <div class="site-bg" aria-hidden="true"></div>
  <div class="grain" aria-hidden="true"></div>

  @include('frontend.partials.header')

  <div id="main-content">
    @yield('content')
  </div>

  @include('frontend.partials.footer')

  <button class="back-to-top" id="backToTop" type="button" aria-label="Back to top" title="Back to top" hidden>
    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
      <path d="M12 5.75 5.75 12l1.42 1.42L11 9.59V19h2V9.59l3.83 3.83L18.25 12 12 5.75Z" />
    </svg>
  </button>

  @stack('scripts')
</body>
</html>
