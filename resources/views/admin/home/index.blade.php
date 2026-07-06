@extends('admin.layouts.app')

@section('title', 'Home Page')

@section('content')
<div class="page-head">
  <div>
    <h2>Home page sections.</h2>
    <p>Each frontend home section is controlled separately. The frontend design classes stay untouched.</p>
  </div>
</div>

<div class="grid two">
  @foreach($sections as $section)
    <article class="card section-card">
      <div>
        <div class="section-key">{{ $section->section_key }}</div>
        <h3>{{ $section->admin_title }}</h3>
        <p>{{ $section->label ?: 'No label set' }} • {{ $section->items_count }} item(s)</p>
        <div style="margin-top:12px"><span class="pill {{ $section->is_active ? '' : 'off' }}">{{ $section->is_active ? 'Active' : 'Hidden' }}</span></div>
      </div>
      <a class="btn primary" href="{{ route('admin.home.sections.edit', $section) }}">Edit</a>
    </article>
  @endforeach
</div>
@endsection
