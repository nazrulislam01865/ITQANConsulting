@extends('admin.map.layout')
@section('title','Map Places')
@section('content')
@php($mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg')
<div class="card">
    <h2 style="margin-top:0">Create Place / POI</h2>
    <p class="small">Click the map picker to fill X and Y, then attach the place to its nearest saved route vertex so it can be used in pathway searches.</p>
    <form method="post" action="{{ route('admin.map.places.store') }}" id="placeCreateForm">
        @csrf
        <div class="form-grid">
            <div><label>Name</label><input name="name" required></div>
            <div><label>Slug</label><input name="slug" placeholder="Generated automatically"></div>
            <div><label>Category</label><select name="map_category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select></div>
            <div><label>Route Vertex</label><select name="map_node_id"><option value="">None</option>@foreach($nodes as $node)<option value="{{ $node->id }}">{{ $node->name }}</option>@endforeach</select></div>
            <div><label>Pin No.</label><input name="pin_number" type="number" min="1"></div>
            <div><label>Icon</label><input name="icon" placeholder="🏨"></div>
            <div><label>X</label><input id="placeXCreate" name="x" type="number" step="0.001" min="0" max="{{ $map->width }}" required></div>
            <div><label>Y</label><input id="placeYCreate" name="y" type="number" step="0.001" min="0" max="{{ $map->height }}" required></div>
            <div><label>Sort</label><input name="sort_order" type="number" min="0" value="0"></div>
        </div>
        <div style="margin-top:10px"><label>Subtitle</label><input name="subtitle"></div>
        <div style="margin-top:10px"><label>Description</label><textarea name="description"></textarea></div>
        <div class="checkbox-row">
            <label><input type="checkbox" name="is_qr_point" value="1"> QR link point</label>
            <label><input type="checkbox" name="is_active" value="1" checked> Visible</label>
        </div>
        <p><button class="btn" type="submit">Save Place</button></p>
    </form>
</div>

<div class="card">
    <h2 style="margin-top:0">Place Coordinate Picker</h2>
    <p class="small">Tap or click the exact place location. The red marker and create-form coordinates update immediately.</p>
    <div class="drawing-scroll">
        <div id="placePickerCanvas" class="drawing-canvas" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <img src="{{ asset($mapImage) }}" alt="Resort map" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <div id="placePickerPin" class="picker-pin" hidden></div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-top:0">Saved Places</h2>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Place</th><th>Position</th><th>Route Vertex</th><th>Status</th><th>Edit</th></tr></thead>
        <tbody>
        @foreach($places as $place)
            <tr>
                <td><strong>{{ $place->pin_number }}. {{ $place->name }}</strong><br><span class="small">{{ $place->slug }} | {{ $place->category?->name }}</span></td>
                <td>{{ $place->x }}, {{ $place->y }}</td>
                <td>{{ $place->routeNode?->name ?? 'Not attached' }}</td>
                <td>{{ $place->is_active ? 'Visible' : 'Hidden' }} @if($place->is_qr_point) / QR @endif</td>
                <td style="min-width:520px">
                    <form method="post" action="{{ route('admin.map.places.update', $place) }}">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <div><label>Name</label><input name="name" value="{{ $place->name }}" required></div>
                            <div><label>Slug</label><input name="slug" value="{{ $place->slug }}"></div>
                            <div><label>Category</label><select name="map_category_id"><option value="">None</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected($place->map_category_id === $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                            <div><label>Route Vertex</label><select name="map_node_id"><option value="">None</option>@foreach($nodes as $node)<option value="{{ $node->id }}" @selected($place->map_node_id === $node->id)>{{ $node->name }}</option>@endforeach</select></div>
                            <div><label>Pin</label><input name="pin_number" type="number" min="1" value="{{ $place->pin_number }}"></div>
                            <div><label>Icon</label><input name="icon" value="{{ $place->icon }}"></div>
                            <div><label>X</label><input name="x" type="number" min="0" max="{{ $map->width }}" step="0.001" value="{{ $place->x }}" required></div>
                            <div><label>Y</label><input name="y" type="number" min="0" max="{{ $map->height }}" step="0.001" value="{{ $place->y }}" required></div>
                            <div><label>Sort</label><input name="sort_order" type="number" min="0" value="{{ $place->sort_order }}"></div>
                        </div>
                        <div style="margin-top:8px"><label>Subtitle</label><input name="subtitle" value="{{ $place->subtitle }}"></div>
                        <div style="margin-top:8px"><label>Description</label><textarea name="description">{{ $place->description }}</textarea></div>
                        <div class="checkbox-row">
                            <label><input type="checkbox" name="is_qr_point" value="1" @checked($place->is_qr_point)> QR point</label>
                            <label><input type="checkbox" name="is_active" value="1" @checked($place->is_active)> Visible</label>
                        </div>
                        <button class="btn secondary" type="submit">Update Place</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
<script>
(function(){
    const canvas=document.getElementById('placePickerCanvas');
    const pin=document.getElementById('placePickerPin');
    const xInput=document.getElementById('placeXCreate');
    const yInput=document.getElementById('placeYCreate');
    canvas.addEventListener('click',function(event){
        const rect=canvas.getBoundingClientRect();
        const x=Math.round((event.clientX-rect.left)*({{ $map->width }}/rect.width));
        const y=Math.round((event.clientY-rect.top)*({{ $map->height }}/rect.height));
        xInput.value=x;yInput.value=y;
        pin.hidden=false;pin.style.left=`${(x/{{ $map->width }})*100}%`;pin.style.top=`${(y/{{ $map->height }})*100}%`;
    });
})();
</script>
@endsection
