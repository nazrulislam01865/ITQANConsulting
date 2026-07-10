@extends('admin.map.layout')
@section('title','Route Vertices')
@section('content')
@php($mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg')
<div class="card">
    <h2 style="margin-top:0">Create Route Vertex</h2>
    <p class="small">Vertices are exact road endpoints and junctions. Click the coordinate picker below, then save the vertex.</p>
    <form method="post" action="{{ route('admin.map.nodes.store') }}">
        @csrf
        <div class="form-grid">
            <div><label>Name</label><input name="name" required></div>
            <div><label>Code</label><input name="code" placeholder="Generated automatically"></div>
            <div><label>X</label><input id="nodeXCreate" name="x" type="number" step="0.001" min="0" max="{{ $map->width }}" required></div>
            <div><label>Y</label><input id="nodeYCreate" name="y" type="number" step="0.001" min="0" max="{{ $map->height }}" required></div>
            <div><label>Type</label><select name="node_type"><option value="junction">Junction</option><option value="place">Place connector</option><option value="entry">Entry</option><option value="exit">Exit</option></select></div>
        </div>
        <div class="checkbox-row"><label><input type="checkbox" name="is_active" value="1" checked> Active</label></div>
        <p><button class="btn" type="submit">Save Vertex</button></p>
    </form>
</div>

<div class="card">
    <h2 style="margin-top:0">Vertex Coordinate Picker</h2>
    <p class="small">Tap or click the exact center of a road junction. Connected path endpoints will use this precise coordinate.</p>
    <div class="drawing-scroll">
        <div id="nodePickerCanvas" class="drawing-canvas" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <img src="{{ asset($mapImage) }}" alt="Resort map" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <div id="nodePickerPin" class="picker-pin" hidden></div>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-top:0">Saved Route Vertices</h2>
    <p class="small">Moving a vertex automatically realigns the first or last control point of every connected path.</p>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Vertex</th><th>Position</th><th>Type</th><th>Edit</th></tr></thead>
        <tbody>
        @foreach($nodes as $node)
            <tr>
                <td><strong>{{ $node->name }}</strong><br><span class="small">{{ $node->code }}</span></td>
                <td>{{ $node->x }}, {{ $node->y }}</td>
                <td>{{ $node->node_type }} | {{ $node->is_active ? 'Active' : 'Inactive' }}</td>
                <td style="min-width:430px">
                    <form method="post" action="{{ route('admin.map.nodes.update', $node) }}">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <div><label>Name</label><input name="name" value="{{ $node->name }}" required></div>
                            <div><label>Code</label><input name="code" value="{{ $node->code }}"></div>
                            <div><label>X</label><input name="x" type="number" min="0" max="{{ $map->width }}" step="0.001" value="{{ $node->x }}" required></div>
                            <div><label>Y</label><input name="y" type="number" min="0" max="{{ $map->height }}" step="0.001" value="{{ $node->y }}" required></div>
                            <div><label>Type</label><select name="node_type"><option value="junction" @selected($node->node_type==='junction')>Junction</option><option value="place" @selected($node->node_type==='place')>Place connector</option><option value="entry" @selected($node->node_type==='entry')>Entry</option><option value="exit" @selected($node->node_type==='exit')>Exit</option></select></div>
                        </div>
                        <div class="checkbox-row"><label><input type="checkbox" name="is_active" value="1" @checked($node->is_active)> Active</label></div>
                        <button class="btn secondary" type="submit">Update Vertex</button>
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
    const canvas=document.getElementById('nodePickerCanvas');
    const pin=document.getElementById('nodePickerPin');
    const xInput=document.getElementById('nodeXCreate');
    const yInput=document.getElementById('nodeYCreate');
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
