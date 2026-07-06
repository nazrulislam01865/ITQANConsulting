@extends('admin.layouts.app')

@section('title', $pageTitle)

@section('content')
<div class="page-head">
  <div>
    <h2>{{ $pageTitle }} sections.</h2>
    <p>Each frontend section is controlled separately. Fields are arranged in the same sequence as the frontend.</p>
  </div>
</div>

<div class="grid two">
  @foreach($sections as $section)
    <article class="card section-card">
      <div>
        <div class="section-key">{{ $section->section_key }}</div>
        <h3>{{ $section->admin_title }}</h3>
        <p>{{ $section->label ?: 'No label set' }} • {{ $section->items_count }} item(s)</p>
        <span class="pill {{ $section->is_active ? '' : 'off' }}" style="margin-top:14px">{{ $section->is_active ? 'Active' : 'Hidden' }}</span>
      </div>
      <a class="btn primary" href="{{ route('admin.pages.sections.edit', [$pageKey, $section]) }}">Edit</a>
    </article>
  @endforeach
</div>
@endsection
