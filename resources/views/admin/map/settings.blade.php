@extends('admin.map.layout')
@section('title','Map Settings')
@section('content')
<div class="card">
    <h2 style="margin-top:0">Map Settings</h2>
    <p class="small">Upload or replace the illustrated resort map. Places, route vertices, and curved path control points are stored separately in the database.</p>
    <form method="post" action="{{ route('admin.map.settings.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-grid">
            <div><label>Name</label><input name="name" value="{{ $map->name }}" required></div>
            <div><label>Map Type</label><select name="map_type"><option value="image" @selected($map->map_type === 'image')>Image / 2.5D</option><option value="svg" @selected($map->map_type === 'svg')>SVG</option><option value="template_svg" @selected($map->map_type === 'template_svg')>Built-in SVG template</option><option value="3d" @selected($map->map_type === '3d')>3D preview</option></select></div>
            <div><label>Exact Width</label><input name="width" type="number" value="{{ $map->width }}" required></div>
            <div><label>Exact Height</label><input name="height" type="number" value="{{ $map->height }}" required></div>
            <div><label>Meters per pixel</label><input name="meters_per_pixel" type="number" step="0.0001" value="{{ $map->meters_per_pixel }}" required></div>
            <div><label>Walk meters/min</label><input name="walk_meters_per_minute" type="number" value="{{ $map->walk_meters_per_minute }}" required></div>
            <div><label>Buggy meters/min</label><input name="buggy_meters_per_minute" type="number" value="{{ $map->buggy_meters_per_minute }}" required></div>
        </div>
        <div style="margin-top:10px"><label>Current map file</label><input name="map_file" value="{{ $map->map_file }}" placeholder="Built-in template is used when blank"></div>
        <div style="margin-top:10px"><label>Upload new map image</label><input name="map_upload" type="file" accept="image/*"></div>
        <p class="small">The width and height must match the coordinate system used by saved places and paths.</p>
        <p><button class="btn" type="submit">Save Map Settings</button></p>
    </form>
</div>
<div class="card">
    <h2 style="margin-top:0">Current Map Preview</h2>
    <div class="map-preview"><img src="{{ $map->map_file ? asset($map->map_file) : asset('assets/itqan-external-guest-map/template-map.svg') }}" alt="Map preview"></div>
</div>
@endsection
