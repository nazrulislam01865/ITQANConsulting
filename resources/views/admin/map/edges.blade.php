@extends('admin.map.layout')
@section('title','Path Connections')
@section('content')
@php($mapImage = $map->map_file ?: 'assets/itqan-external-guest-map/template-map.svg')
<div class="card">
    <h2 style="margin-top:0">Create Curved Path Connection</h2>
    <p class="small">Select the exact From and To vertices, load their endpoints, then click along the road to add gentle control points. The first and last saved points are always snapped to the selected vertices.</p>
    <form method="post" action="{{ route('admin.map.edges.store') }}" id="edgeCreateForm">
        @csrf
        <div class="form-grid">
            <div><label>From Vertex</label><select name="from_node_id" id="fromNodeCreate" required>@foreach($nodes as $node)<option value="{{ $node->id }}" data-x="{{ $node->x }}" data-y="{{ $node->y }}">{{ $node->name }}</option>@endforeach</select></div>
            <div><label>To Vertex</label><select name="to_node_id" id="toNodeCreate" required>@foreach($nodes as $node)<option value="{{ $node->id }}" data-x="{{ $node->x }}" data-y="{{ $node->y }}">{{ $node->name }}</option>@endforeach</select></div>
            <div><label>Exact distance (optional)</label><input name="distance_meters" type="number" step="0.01" min="0.01" placeholder="Auto-calculated if blank"></div>
            <div><label>Sort</label><input name="sort_order" type="number" min="0" value="0"></div>
        </div>
        <div style="margin-top:10px"><label>Saved path control points</label><textarea name="path_points" id="pathPointsCreate" placeholder='[{"x":200,"y":400},{"x":260,"y":410}]'></textarea></div>
        <div class="checkbox-row">
            <label><input type="checkbox" name="walk_enabled" value="1" checked> Walking path</label>
            <label><input type="checkbox" name="buggy_enabled" value="1" checked> Buggy path</label>
            <label><input type="checkbox" name="staff_only" value="1"> Staff only</label>
            <label><input type="checkbox" name="is_active" value="1" checked> Visible</label>
        </div>
        <p><button class="btn" type="submit">Save Path Connection</button></p>
    </form>
</div>

<div class="card">
    <h2 style="margin-top:0">Path Drawing Helper</h2>
    <p class="small">Click points in road order. The blue preview is curved only for display; the control points themselves are stored in the database.</p>
    <div class="checkbox-row toolbar-row">
        <button type="button" class="btn secondary" id="loadEndpointsBtn">Load exact endpoints</button>
        <button type="button" class="btn secondary" id="undoPointBtn">Undo</button>
        <button type="button" class="btn danger" id="clearPointsBtn">Clear</button>
        <button type="button" class="btn" id="usePointsBtn">Use these points</button>
    </div>
    <textarea id="helperJson" readonly style="margin-top:10px"></textarea>
    <div class="drawing-scroll">
        <div id="drawCanvas" class="drawing-canvas" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <img src="{{ asset($mapImage) }}" alt="Resort map" style="width:{{ $map->width }}px;height:{{ $map->height }}px">
            <svg id="helperOverlay" viewBox="0 0 {{ $map->width }} {{ $map->height }}" width="{{ $map->width }}" height="{{ $map->height }}">
                <path id="helperPathCasing" d="" fill="none" stroke="#fff" stroke-width="14" stroke-linecap="round" stroke-linejoin="round" opacity=".9"></path>
                <path id="helperPath" d="" fill="none" stroke="#1976d2" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"></path>
                <g id="helperDots"></g>
            </svg>
        </div>
    </div>
</div>

<div class="card">
    <h2 style="margin-top:0">Saved Path Connections</h2>
    <div class="table-scroll">
    <table>
        <thead><tr><th>Connection</th><th>Access</th><th>Geometry</th><th>Edit</th></tr></thead>
        <tbody>
        @foreach($edges as $edge)
            <tr>
                <td><strong>{{ $edge->fromNode?->name }}</strong> → <strong>{{ $edge->toNode?->name }}</strong><br><span class="small">{{ $edge->distance_meters ? number_format($edge->distance_meters,1).' m' : 'Auto distance' }}</span></td>
                <td>{{ $edge->walk_enabled ? 'Walk ' : '' }}{{ $edge->buggy_enabled ? 'Buggy ' : '' }}{{ $edge->staff_only ? 'Staff only ' : '' }}{{ $edge->is_active ? 'Visible' : 'Hidden' }}</td>
                <td><code>{{ count($edge->path_points ?? []) }} control points</code></td>
                <td style="min-width:430px">
                    <form method="post" action="{{ route('admin.map.edges.update', $edge) }}">
                        @csrf @method('PUT')
                        <div class="form-grid">
                            <select name="from_node_id" required>@foreach($nodes as $node)<option value="{{ $node->id }}" @selected($edge->from_node_id === $node->id)>{{ $node->name }}</option>@endforeach</select>
                            <select name="to_node_id" required>@foreach($nodes as $node)<option value="{{ $node->id }}" @selected($edge->to_node_id === $node->id)>{{ $node->name }}</option>@endforeach</select>
                            <input name="distance_meters" type="number" step="0.01" min="0.01" value="{{ $edge->distance_meters }}" placeholder="Auto distance">
                            <input name="sort_order" type="number" min="0" value="{{ $edge->sort_order }}">
                        </div>
                        <textarea name="path_points">{{ json_encode($edge->path_points, JSON_PRETTY_PRINT) }}</textarea>
                        <div class="checkbox-row">
                            <label><input type="checkbox" name="walk_enabled" value="1" @checked($edge->walk_enabled)> Walk</label>
                            <label><input type="checkbox" name="buggy_enabled" value="1" @checked($edge->buggy_enabled)> Buggy</label>
                            <label><input type="checkbox" name="staff_only" value="1" @checked($edge->staff_only)> Staff only</label>
                            <label><input type="checkbox" name="is_active" value="1" @checked($edge->is_active)> Visible</label>
                            <label><input type="checkbox" name="recalculate_distance" value="1" checked> Recalculate distance</label>
                        </div>
                        <button class="btn secondary" type="submit">Update Path</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</div>
<script src="{{ asset('assets/itqan-external-guest-map/path-geometry.js') }}?v=20260710-map-only"></script>
<script>
(function(){
    const points=[];
    const canvas=document.getElementById('drawCanvas');
    const path=document.getElementById('helperPath');
    const casing=document.getElementById('helperPathCasing');
    const dots=document.getElementById('helperDots');
    const json=document.getElementById('helperJson');
    const target=document.getElementById('pathPointsCreate');
    const from=document.getElementById('fromNodeCreate');
    const to=document.getElementById('toNodeCreate');
    const geometry=window.ResortPathGeometry;

    function selectedPoint(select){const option=select.options[select.selectedIndex];return{x:Number(option.dataset.x),y:Number(option.dataset.y)}}
    function update(){
        const d=geometry.svgPath(points,{subtleBend:true,seed:Number(from.value||0)+Number(to.value||0),tension:.62,sampleEvery:7,maxHandleRatio:.24});
        path.setAttribute('d',d);casing.setAttribute('d',d);
        dots.innerHTML=points.map((point,index)=>`<g><circle cx="${point.x}" cy="${point.y}" r="8" fill="${index===0?'#17834d':(index===points.length-1?'#d3483e':'#1976d2')}" stroke="#fff" stroke-width="3"></circle><text x="${point.x}" y="${point.y+3.5}" text-anchor="middle" fill="#fff" font-size="9" font-weight="900">${index+1}</text></g>`).join('');
        json.value=JSON.stringify(points,null,2);
    }
    canvas.addEventListener('click',function(event){
        const rect=canvas.getBoundingClientRect();
        points.push({x:Math.round((event.clientX-rect.left)*({{ $map->width }}/rect.width)),y:Math.round((event.clientY-rect.top)*({{ $map->height }}/rect.height))});
        update();
    });
    document.getElementById('loadEndpointsBtn').addEventListener('click',function(){points.length=0;points.push(selectedPoint(from),selectedPoint(to));update()});
    document.getElementById('undoPointBtn').addEventListener('click',function(){points.pop();update()});
    document.getElementById('clearPointsBtn').addEventListener('click',function(){points.length=0;update()});
    document.getElementById('usePointsBtn').addEventListener('click',function(){target.value=json.value;target.scrollIntoView({behavior:'smooth',block:'center'})});
    from.addEventListener('change',()=>{if(points.length){points[0]=selectedPoint(from);update()}});
    to.addEventListener('change',()=>{if(points.length>1){points[points.length-1]=selectedPoint(to);update()}});
})();
</script>
@endsection
