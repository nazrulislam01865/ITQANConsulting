@extends('admin.layouts.auth')

@section('content')
<section class="auth-shell">
  <div class="auth-card">
    @include('admin.partials.brand', [
      'href' => route('home'),
      'title' => 'ITQAN Consulting',
      'subtitle' => 'Sincere Services. Lasting Results.',
    ])
    <h1 class="auth-title">Admin access for clear control.</h1>
    <p class="auth-lead">Sign in to manage the ITQAN website content, logo, header menu, footer menu, and home page sections.</p>

    @include('admin.partials.alerts')

    <form method="POST" action="{{ route('admin.login.store') }}" novalidate>
      @csrf
      <div class="field">
        <label for="email">Email address</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="admin@example.com" autocomplete="email" required autofocus>
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="Enter password" autocomplete="current-password" required minlength="8">
      </div>
      <label class="check-row">
        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
        Keep me signed in on this device
      </label>
      <button class="btn primary" type="submit">Sign in securely</button>
    </form>
  </div>
</section>
@endsection
