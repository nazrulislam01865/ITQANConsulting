<!DOCTYPE html>
<html lang="en" class="admin-nav-hydrating">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @include('frontend.partials.favicon')
  <title>@yield('title', 'Admin') | ITQAN Consulting</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <meta name="admin-session-timeout-seconds" content="{{ max(1, (int) config('itqan_security.admin_session_timeout_minutes', 30)) * 60 }}">
  <meta name="admin-session-expired-url" content="{{ route('admin.session-expired') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v=admin-favicon-upload-1">
</head>
<body>
  <div class="admin-bg" aria-hidden="true"></div>
  <div class="admin-mobile-overlay" data-admin-sidebar-close aria-hidden="true"></div>
  <header class="admin-mobile-bar" aria-label="Mobile admin header">
    @include('admin.partials.brand', [
      'href' => route('admin.dashboard'),
      'title' => 'ITQAN Admin',
      'subtitle' => 'Backend Control',
    ])
    <button class="admin-mobile-menu-toggle" type="button" data-admin-menu-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open admin menu">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </button>
  </header>
  <div class="admin-layout">
    @include('admin.partials.sidebar')
    <main class="admin-main">
      @include('admin.partials.topbar')
      <div class="content">
        @include('admin.partials.alerts')
        @yield('content')
      </div>
    </main>
  </div>
  <script src="{{ asset('assets/js/admin.js') }}?v=admin-favicon-upload-1"></script>
</body>
</html>
