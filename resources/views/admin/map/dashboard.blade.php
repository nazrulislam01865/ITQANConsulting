@extends('admin.map.layout')
@section('title','Palace Map Admin')
@section('content')
<div class="card">
    <h2 style="margin-top:0">{{ $map->name }}</h2>
    <p class="small">Map size: {{ $map->width }} × {{ $map->height }} | Scale: {{ $map->meters_per_pixel }} m/px</p>
    <a href="{{ route('admin.map.preview') }}" class="btn">Open Guest Preview</a>
    <form method="post" action="{{ route('admin.map.reset-seed') }}" style="display:inline" onsubmit="return confirm('Reset all map places, vertices, and path geometry from the seed file?')">
        @csrf
        <button class="btn secondary" type="submit">Reset Saved Demo Data</button>
    </form>
</div>
<div class="grid">
    <div class="metric"><b>{{ $placesCount }}</b><span>Saved Places</span></div>
    <div class="metric"><b>{{ $activePlacesCount }}</b><span>Visible Places</span></div>
    <div class="metric"><b>{{ $nodesCount }}</b><span>Route Vertices</span></div>
    <div class="metric"><b>{{ $edgesCount }}</b><span>Saved Path Edges</span></div>
    <div class="metric"><b>{{ $activeEdgesCount }}</b><span>Visible Path Edges</span></div>
</div>
<div class="card">
    <h3>Map data model</h3>
    <p>Places are guest-visible destinations. Route vertices are exact junction coordinates. Path connections store the curved control points between those vertices. Every saved path endpoint is automatically snapped to its selected vertex, which prevents gaps and overlapping route joins.</p>
</div>
@endsection
