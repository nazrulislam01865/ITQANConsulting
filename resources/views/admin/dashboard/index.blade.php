@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-head">
  <div>
    <h2>Website control center.</h2>
    <p>Manage the dynamic parts of the ITQAN frontend without touching the template design.</p>
  </div>
  <a class="btn primary" href="{{ route('admin.home.index') }}">Edit Home Page</a>
</div>

<div class="grid three">
  <div class="card"><div class="metric">{{ $homeSections }}</div><h3>Home Sections</h3><p>Every frontend home section is separated for admin editing.</p></div>
  <div class="card"><div class="metric">{{ $pageSections }}</div><h3>Inner Page Sections</h3><p>About, Services, Works, Catalog, and Contact sections are editable separately.</p></div>
  <div class="card"><div class="metric">{{ $headerMenuItems }}</div><h3>Header Menu</h3><p>Control the top navigation labels and links.</p></div>
  <div class="card"><div class="metric">{{ $footerMenuItems }}</div><h3>Footer Menu</h3><p>Control footer page and service link groups separately.</p></div>
  <a class="card" href="{{ route('admin.contact-submissions.index') }}">
    <div class="metric">{{ $contactResponses }}</div>
    <h3>Contact Responses</h3>
    <p>{{ $unreadContactResponses }} unread message(s) from the public contact form.</p>
  </a>
</div>
@endsection
