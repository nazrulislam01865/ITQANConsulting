@extends('admin.layouts.app')

@section('title', $section->admin_title)

@section('content')
<div class="page-head">
  <div>
    <h2>{{ $section->admin_title }}</h2>
  </div>
  <a class="btn" href="{{ route('admin.home.index') }}">Back to Home Sections</a>
</div>

<form class="form-card" method="POST" action="{{ route('admin.home.sections.update', $section) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  @includeIf('admin.home.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes])
  <div class="button-row"><button class="btn primary" type="submit">Save Section Content</button></div>
</form>

@unless(in_array($section->section_key, ['home_founder', 'home_works_preview', 'home_cta'], true))
  @include('admin.home.sections.items', ['section' => $section, 'routes' => $routes])
@endunless
@endsection
