<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Portfolio Admin') · Md Aminul Islam Portfolio</title>
    @vite(['resources/css/starpmaminul/admin.css', 'resources/js/starpmaminul/admin.js'])
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand">
                <span class="sidebar-brand-mark">AMI</span>
                <div>
                    <strong>Portfolio Admin</strong>
                    <small>Content management</small>
                </div>
            </div>

            <nav class="sidebar-nav" aria-label="Admin navigation">
                <a href="{{ route('starpmaminul.admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('starpmaminul.admin.dashboard') ? 'active' : '' }}">
                    <span class="sidebar-icon">⌂</span>
                    Dashboard
                </a>

                <div class="sidebar-heading">Portfolio sections</div>
                @foreach(config('starpmaminul.sections', []) as $key => $section)
                    <a href="{{ route('starpmaminul.admin.sections.edit', $key) }}" class="sidebar-link {{ request()->route('sectionKey') === $key ? 'active' : '' }}">
                        <span class="sidebar-icon">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span>{{ $section['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('starpmaminul.portfolio') }}" target="_blank" rel="noopener" class="sidebar-preview">View public site ↗</a>
                <form method="POST" action="{{ route('starpmaminul.admin.logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-logout">Sign out</button>
                </form>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
                <div>
                    <span class="topbar-kicker">Md Aminul Islam Portfolio</span>
                    <h1>@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="topbar-user">
                    <span>{{ strtoupper(substr(auth('starpmaminul')->user()->name, 0, 1)) }}</span>
                    <div>
                        <strong>{{ auth('starpmaminul')->user()->name }}</strong>
                        <small>{{ auth('starpmaminul')->user()->email }}</small>
                    </div>
                </div>
            </header>

            <main class="admin-content">
                @if(session('status'))
                    <div class="alert alert-success" role="status">
                        <span>✓</span>
                        {{ session('status') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>
</body>
</html>
