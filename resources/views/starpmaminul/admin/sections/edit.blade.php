@extends('starpmaminul.admin.layouts.app')

@section('title', $definition['label'])
@section('page-title', $definition['label'])

@section('content')
    @php
        $formData = old('data', $data);
    @endphp

    <div class="editor-heading">
        <div>
            <span class="content-eyebrow">Section editor</span>
            <h2>{{ $definition['label'] }}</h2>
            <p>{{ $definition['description'] }}</p>
        </div>
        <a href="{{ route('starpmaminul.portfolio') }}" target="_blank" rel="noopener" class="secondary-action">Preview site ↗</a>
    </div>

    @if($errors->any())
        <div class="alert alert-error" role="alert">
            <span>!</span>
            <div>
                <strong>Please review the highlighted fields.</strong>
                <p>{{ $errors->first() }}</p>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('starpmaminul.admin.sections.update', $sectionKey) }}" enctype="multipart/form-data" class="section-form" data-dirty-form>
        @csrf
        @method('PUT')

        <div class="form-panel">
            @foreach($definition['fields'] as $field)
                <x-starpmaminul.admin.field
                    :field="$field"
                    :value="data_get($formData, $field['name'])"
                    :input-name="'data['.$field['name'].']'"
                    :path="$field['name']"
                />
            @endforeach
        </div>

        <div class="save-bar">
            <div>
                <strong>Save {{ $definition['label'] }}</strong>
                <span>Changes appear on the public site immediately.</span>
            </div>
            <button type="submit" class="primary-action">Save changes <span>✓</span></button>
        </div>
    </form>
@endsection
