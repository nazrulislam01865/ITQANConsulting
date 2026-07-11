@extends('admin.layouts.app')

@section('title', $section->admin_title)

@section('content')
<div class="page-head">
  <div>
    <h2>{{ $section->admin_title }}</h2>
    <p>{{ $pageTitle }}</p>
  </div>
  <a class="btn" href="{{ route('admin.pages.index', $pageKey) }}">Back to {{ $pageTitle }}</a>
</div>

<form class="form-card" method="POST" action="{{ route('admin.pages.sections.update', $section) }}" enctype="multipart/form-data">
  @csrf
  @method('PUT')
  @includeIf('admin.pages.sections.' . str_replace('_', '-', $section->section_key), ['section' => $section, 'routes' => $routes])
  <div class="button-row"><button class="btn primary" type="submit">Save Section Content</button></div>
</form>

@include('admin.pages.sections.items', ['section' => $section, 'routes' => $routes])
@endsection
