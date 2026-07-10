<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Guest Map Admin')</title>
    <style>
        *{box-sizing:border-box}html{background:#f4f1e8}body{margin:0;font-family:Inter,Arial,sans-serif;background:#f4f1e8;color:#16251f}.layout{display:grid;grid-template-columns:260px minmax(0,1fr);min-height:100vh}.nav{background:#173d2c;color:#fff;padding:22px}.nav h1{font-size:20px;margin:0 0 18px}.nav a{display:block;color:#ddecdf;text-decoration:none;padding:10px 12px;border-radius:12px;margin:4px 0}.nav a:hover,.nav a.active{background:#245d43;color:#fff}.content{min-width:0;padding:24px}.card{background:#fff;border:1px solid #e5dfd3;border-radius:18px;padding:18px;margin-bottom:18px;box-shadow:0 8px 25px rgba(20,35,29,.06)}.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:14px}.metric{background:#f9f7f0;border:1px solid #e5dfd3;border-radius:16px;padding:16px}.metric b{display:block;font-size:28px}.metric span{font-size:12px;color:#66736c;text-transform:uppercase;font-weight:800}.btn{border:0;border-radius:12px;padding:10px 13px;background:#174a33;color:#fff;font-weight:800;text-decoration:none;display:inline-block;cursor:pointer}.btn.secondary{background:#eee7d8;color:#513c1d}.btn.danger{background:#b7352b}.alert{background:#eaf7ed;color:#174a33;border:1px solid #bddfc6;border-radius:14px;padding:12px;margin-bottom:14px}.table-scroll{width:100%;overflow:auto;border-radius:16px;border:1px solid #eee8dd}table{width:100%;border-collapse:collapse;background:#fff;min-width:760px}th,td{border-bottom:1px solid #eee8dd;text-align:left;padding:10px;font-size:13px;vertical-align:top}th{background:#174a33;color:#fff;position:sticky;top:0;z-index:1}label{display:block;margin-bottom:5px;font-size:12px;font-weight:800;color:#425149}input,select,textarea{width:100%;border:1px solid #d8d1c4;border-radius:10px;padding:9px;background:#fff;color:#16251f}input:focus,select:focus,textarea:focus{outline:3px solid rgba(23,74,51,.1);border-color:#6f9f83}textarea{min-height:78px;font-family:ui-monospace,Menlo,monospace;font-size:12px}.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;align-items:end}.checkbox-row{display:flex;gap:12px;flex-wrap:wrap;margin-top:10px}.checkbox-row label{display:flex;gap:6px;align-items:center;font-size:12px;margin:0}.checkbox-row input{width:auto}.toolbar-row{align-items:center}.small{font-size:12px;color:#66736c;line-height:1.5}.map-preview{position:relative;border-radius:18px;overflow:hidden;border:1px solid #d8d1c4;background:#bda160}.map-preview img{width:100%;display:block}.pin{position:absolute;width:20px;height:20px;border-radius:50% 50% 50% 6px;background:#174a33;color:#fff;display:grid;place-items:center;font-size:10px;font-weight:900;transform:translate(-50%,-100%) rotate(-45deg);border:2px solid white}.pin span{transform:rotate(45deg)}.drawing-scroll{position:relative;overflow:auto;max-height:620px;border:1px solid #d8d1c4;border-radius:16px;margin-top:12px;background:#bda160;overscroll-behavior:contain}.drawing-canvas{position:relative;transform-origin:0 0;cursor:crosshair}.drawing-canvas>img{display:block;user-select:none;pointer-events:none}.drawing-canvas>svg{position:absolute;inset:0;pointer-events:none}.picker-pin{position:absolute;width:24px;height:24px;border-radius:50% 50% 50% 6px;background:#d3483e;transform:translate(-50%,-100%) rotate(-45deg);border:3px solid #fff;box-shadow:0 8px 16px rgba(0,0,0,.25)}code{font-size:11px;white-space:pre-wrap;word-break:break-word}
        @media(max-width:800px){.layout{grid-template-columns:1fr}.nav{position:static;padding:14px;display:flex;gap:6px;overflow:auto;align-items:center}.nav h1{font-size:15px;margin:0 8px 0 0;white-space:nowrap}.nav a{flex:0 0 auto;padding:8px 10px;font-size:12px}.content{padding:12px}.card{padding:14px;border-radius:15px}.form-grid{grid-template-columns:1fr 1fr}.toolbar-row .btn{flex:1 1 130px;text-align:center}.drawing-scroll{max-height:62vh}}
        @media(max-width:520px){.form-grid{grid-template-columns:1fr}.content{padding:9px}.card{padding:12px}.nav h1{display:none}}
    </style>
</head>
<body>
<div class="layout">
    <aside class="nav">
        <h1>Palace Map Admin</h1>
        <a href="{{ route('admin.dashboard') }}">← ITQAN Dashboard</a>
        <a href="{{ route('external-guest-map.index') }}">Guest Map</a>
        <a href="{{ route('admin.map.dashboard') }}" class="{{ request()->routeIs('admin.map.dashboard') ? 'active' : '' }}">Dashboard</a>
        <a href="{{ route('admin.map.settings') }}" class="{{ request()->routeIs('admin.map.settings') ? 'active' : '' }}">Map Settings</a>
        <a href="{{ route('admin.map.places') }}" class="{{ request()->routeIs('admin.map.places') ? 'active' : '' }}">Places</a>
        <a href="{{ route('admin.map.nodes') }}" class="{{ request()->routeIs('admin.map.nodes') ? 'active' : '' }}">Vertices</a>
        <a href="{{ route('admin.map.edges') }}" class="{{ request()->routeIs('admin.map.edges') ? 'active' : '' }}">Paths</a>
        <a href="{{ route('admin.map.preview') }}" class="{{ request()->routeIs('admin.map.preview') ? 'active' : '' }}">Preview</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="margin-top:14px">
            @csrf
            <button class="btn danger" type="submit" style="width:100%">Logout</button>
        </form>
    </aside>
    <main class="content">
        @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert" style="background:#fff3f1;color:#8a2d22;border-color:#f3c4bc">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>
