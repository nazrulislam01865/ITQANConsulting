<header class="topbar">
  <div>
    <h1>@yield('title', 'Admin')</h1>
    <div class="help">Signed in as {{ auth()->user()->name ?? 'Administrator' }}</div>
  </div>
  <form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button class="btn small" type="submit">Logout</button>
  </form>
</header>
