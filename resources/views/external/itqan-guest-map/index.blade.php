<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>External Palace Guest Map</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('vendor/itqan-guest-map-leaflet/leaflet.css') }}?v=1.9.4">
    <link rel="stylesheet" href="{{ asset('assets/itqan-external-guest-map/guest-map.css') }}?v=itqan-ext-map-2">
</head>
<body data-base-url="{{ url('/') }}" data-default-from="{{ $from }}" data-default-to="{{ $to }}">
<div class="app" id="guestMapApp">
    <aside class="side" id="sidePanel">
        <div class="brand">
            <div class="brand-mark">PM</div>
            <div>
                <h1>External Palace Guest Map</h1>
                <p>Isolated Itqan test route</p>
            </div>
        </div>
        <div class="notice">
            This isolated test map uses Leaflet.js with CRS.Simple as the actual map engine. It is separate from the Itqan website menus, frontend pages, and normal database tables. It is still offline/no Google Maps/no external tiles. The first phone GPS sample is anchored to the selected start place. After that, movement is projected only onto valid road graph segments. Left/right movement only works when that movement lands on a connected road; otherwise the marker stays at the last valid road point. The highlighted line shows only the remaining route.
        </div>

        <div class="search-wrap">
            <div style="position:relative">
                <span class="search-icon">⌕</span>
                <input id="search" class="search-input" placeholder="Search reception, villa, pool...">
            </div>
        </div>

        <div class="filters" id="filters"></div>

        <div class="field">
            <label>Guest is here</label>
            <select id="fromSelect"></select>
        </div>
        <div class="field">
            <label>Going to</label>
            <select id="toSelect"></select>
        </div>

        <div class="buttons">
            <button class="btn primary" id="routeBtn">Show route</button>
            <button class="btn gold" id="startNavBtn">Start direction</button>
        </div>
        <div class="mini-actions">
            <button class="btn" id="setStartBtn">Set start on map</button>
            <button class="btn" id="centerBtn">Center</button>
            <button class="btn" id="stopNavBtn">Stop</button>
            <button class="btn" id="simulateBtn">Simulate movement</button>
            <button class="btn" id="keyboardHintBtn">Keyboard test</button>
            <button class="btn" id="graphBtn">Show route graph</button>
            <button class="btn" id="copyBtn">Copy QR link</button>
            <button class="btn" id="swapBtn">Swap</button>
        </div>

        <div class="summary">
            <b id="summaryTitle">Choose a destination</b>
            <p id="summaryText">Select a start and destination. Then press Start Direction to make the blue marker move like navigation mode.</p>
            <div class="stats">
                <div class="stat"><b id="distanceStat">—</b><span>Distance</span></div>
                <div class="stat"><b id="walkStat">—</b><span>Walk</span></div>
                <div class="stat"><b id="buggyStat">—</b><span>Buggy</span></div>
                <div class="stat live-stat"><b id="gpsStat">Off</b><span>GPS</span></div>
                <div class="stat live-stat"><b id="progressStat">0%</b><span>Progress</span></div>
                <div class="stat live-stat"><b id="movedStat">0 m</b><span>Moved</span></div>
                <div class="stat live-stat"><b id="headingStat">—</b><span>Facing</span></div>
                <div class="stat live-stat"><b id="sensorStat">Checking</b><span>Sensor</span></div>
            </div>
        </div>

        <ol class="steps" id="steps"></ol>

        <label>Explore the resort</label>
        <div class="places" id="places"></div>
    </aside>

    <main class="map-shell">
        <div class="topbar">
            <div class="pill">📍 Leaflet CRS.Simple + road-constrained GPS follow</div>
            <div class="pill hide-sm">Phone GPS + compass; WASD/keyboard is test-only</div>
        </div>
        <button class="btn mobile-toggle" id="mobileToggle">Plan Route</button>

        <div id="leafletMap" class="leaflet-map" aria-label="Interactive resort map"></div>
        <div class="popup" id="popup"></div>

        <div class="map-controls">
            <button class="btn" id="resetView" title="Reset view">⌖</button>
            <div class="zoom-pair">
                <button class="btn" id="zoomIn">+</button>
                <button class="btn" id="zoomOut">−</button>
            </div>
        </div>

        <div class="bottom-card" id="bottomCard">
            <strong id="bottomTitle">Route ready</strong>
            <p id="bottomText">Press Start Direction to begin live navigation.</p>
            <button class="btn primary" id="bottomStartBtn">Start direction</button>
            <button class="btn" id="bottomStopBtn">Stop</button>
        </div>

        <div class="toast" id="toast"></div>
    </main>
</div>
<script src="{{ asset('vendor/itqan-guest-map-leaflet/leaflet.js') }}?v=1.9.4"></script>
<script src="{{ asset('assets/itqan-external-guest-map/guest-map.js') }}?v=itqan-ext-map-2"></script>
</body>
</html>
