@extends('starpmaminul.admin.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    <section class="dashboard-hero">
        <div>
            <span class="content-eyebrow">Content overview</span>
            <h2>Manage every portfolio section from one focused workspace.</h2>
            <p>Each card opens only the fields used by that frontend section, keeping updates fast and the original visual design protected.</p>
        </div>
        <a href="{{ route('starpmaminul.portfolio') }}" target="_blank" rel="noopener" class="secondary-action">Open live portfolio ↗</a>
    </section>

    <section class="dashboard-stats" aria-label="Portfolio status">
        <article>
            <span>{{ count($sections) }}</span>
            <p>Editable sections</p>
        </article>
        <article>
            <span>{{ collect($content)->filter(fn ($data) => filled($data))->count() }}</span>
            <p>Sections configured</p>
        </article>
        <article>
            <span>1</span>
            <p>Public portfolio</p>
        </article>
    </section>

    <section class="section-card-grid">
        @foreach($sections as $key => $section)
            <a href="{{ route('starpmaminul.admin.sections.edit', $key) }}" class="section-card">
                <div class="section-card-top">
                    <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <i>↗</i>
                </div>
                <div>
                    <h3>{{ $section['label'] }}</h3>
                    <p>{{ $section['description'] }}</p>
                </div>
                <small>Manage section</small>
            </a>
        @endforeach
    </section>
@endsection
