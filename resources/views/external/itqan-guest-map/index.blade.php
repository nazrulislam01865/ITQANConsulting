<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <meta name="theme-color" content="#ffffff">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="format-detection" content="telephone=no">
    <title>ITQAN Palace Guest Map</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ request()->getBaseUrl() }}/vendor/itqan-guest-map-leaflet/leaflet.css?v=1.9.4">
    <link rel="stylesheet" href="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/guest-map.css?v=20260710-google-ui1">
</head>
<body
    data-base-url="{{ request()->getBaseUrl() }}"
    data-default-from="{{ $from }}"
    data-default-to="{{ $to }}"
    data-map-data-url="{{ route('external-guest-map.api.data', [], false) }}"
    data-map-route-url="{{ route('external-guest-map.api.route', [], false) }}"
    data-map-location-url="{{ route('external-guest-map.api.location', [], false) }}"
    data-map-finish-url="{{ route('external-guest-map.api.finish', [], false) }}"
    data-map-fallback-url="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/template-map.svg"
>
<div class="app" id="guestMapApp">
    <main class="map-shell">
        <div id="leafletMap" class="leaflet-map" aria-label="Interactive resort map"></div>

        <button class="map-search-launcher" id="mobileToggle" type="button" aria-label="Open directions">
            <span class="launcher-menu">☰</span>
            <span class="launcher-copy">Search ITQAN Palace</span>
            <span class="launcher-directions">➤</span>
        </button>

        <section class="nav-guidance" id="navGuidance" aria-live="polite">
            <div class="maneuver-symbol" id="maneuverIcon">↑</div>
            <div class="maneuver-copy">
                <span class="maneuver-distance" id="maneuverDistance">Route ready</span>
                <strong id="maneuverText">Start navigation</strong>
                <small id="maneuverSubtext">Live turn guidance will appear here.</small>
            </div>
            <button class="guidance-action" id="voiceBtn" type="button" title="Toggle spoken directions" aria-label="Toggle spoken directions">🔊</button>
        </section>

        <div class="navigation-status" id="navigationStatus">
            <span id="trackingModeBadge">Device sensors checking</span>
            <span id="rerouteBadge">On route</span>
        </div>

        <button class="follow-location" id="followBtn" type="button" title="Follow my location" aria-label="Follow my location">➤</button>
        <div class="popup" id="popup"></div>

        <div class="map-controls">
            <button class="map-control" id="resetView" type="button" title="Show whole resort" aria-label="Show whole resort">⌖</button>
            <div class="zoom-pair">
                <button class="map-control" id="zoomIn" type="button" aria-label="Zoom in">+</button>
                <button class="map-control" id="zoomOut" type="button" aria-label="Zoom out">−</button>
            </div>
        </div>

        <div class="bottom-card" id="bottomCard">
            <div class="bottom-route-copy">
                <strong id="bottomTitle">Route ready</strong>
                <p id="bottomText">Choose a destination to see directions.</p>
            </div>
            <div class="bottom-route-actions">
                <button class="btn primary" id="bottomStartBtn" type="button">Start</button>
                <button class="btn secondary" id="bottomStopBtn" type="button">Stop</button>
            </div>
        </div>

        <div class="toast" id="toast"></div>
    </main>

    <aside class="side open" id="sidePanel" aria-label="Directions panel">
        <div class="sheet-handle" aria-hidden="true"></div>
        <div class="panel-heading">
            <button class="icon-button" id="panelCloseBtn" type="button" aria-label="Close directions">←</button>
            <div>
                <h1>Directions</h1>
                <p>ITQAN Palace Resort</p>
            </div>
            <div class="brand-mark" aria-label="ITQAN Palace">IP</div>
        </div>

        <div class="route-input-card">
            <div class="route-points" aria-hidden="true">
                <span class="origin-dot"></span>
                <span class="route-point-line"></span>
                <span class="destination-pin"></span>
            </div>
            <div class="route-fields">
                <div class="route-field">
                    <label for="fromSelect">Starting point</label>
                    <select id="fromSelect" aria-label="Starting point"></select>
                </div>
                <div class="route-divider"></div>
                <div class="route-field">
                    <label for="toSelect">Destination</label>
                    <select id="toSelect" aria-label="Destination"></select>
                </div>
            </div>
            <button class="swap-button" id="swapBtn" type="button" title="Swap start and destination" aria-label="Swap start and destination">⇅</button>
        </div>

        <div class="origin-tools">
            <button class="tool-chip" id="pickStartBtn" type="button"><span>⌖</span> Choose start on map</button>
            <button class="tool-chip" id="setStartBtn" type="button"><span>◉</span> Align phone direction</button>
        </div>

        <div class="primary-actions">
            <button class="btn route-action" id="routeBtn" type="button">Directions</button>
            <button class="btn primary" id="startNavBtn" type="button">Start navigation</button>
        </div>

        <div class="route-overview summary">
            <div class="overview-heading">
                <div>
                    <b id="summaryTitle">Choose a destination</b>
                    <p id="summaryText">Select any starting point and destination. You can also tap “Choose start on map” and select a mapped place.</p>
                </div>
            </div>
            <div class="stats">
                <div class="stat"><b id="distanceStat">—</b><span>Distance</span></div>
                <div class="stat"><b id="walkStat">—</b><span>Walk</span></div>
                <div class="stat"><b id="buggyStat">—</b><span>Buggy</span></div>
                <div class="stat live-stat"><b id="gpsStat">Off</b><span>Tracking</span></div>
                <div class="stat live-stat"><b id="progressStat">0%</b><span>Progress</span></div>
                <div class="stat live-stat"><b id="movedStat">0 m</b><span>Moved</span></div>
                <div class="stat live-stat"><b id="headingStat">—</b><span>Facing</span></div>
                <div class="stat live-stat"><b id="sensorStat">Checking</b><span>Sensor</span></div>
            </div>
        </div>

        <div class="search-wrap">
            <span class="search-icon">⌕</span>
            <input id="search" class="search-input" placeholder="Search reception, villa, pool..." autocomplete="off">
        </div>
        <div class="filters" id="filters"></div>

        <div class="section-heading">
            <div>
                <h2>Explore the resort</h2>
                <p>Tap a place to use it as your destination.</p>
            </div>
        </div>
        <div class="places" id="places"></div>

        <div class="directions-list">
            <h2>Route steps</h2>
            <ol class="steps" id="steps"></ol>
        </div>

        <details class="advanced-tools">
            <summary>Navigation tools</summary>
            <div class="mini-actions">
                <button class="btn" id="centerBtn" type="button">Center resort</button>
                <button class="btn" id="stopNavBtn" type="button">Stop navigation</button>
                <button class="btn" id="simulateBtn" type="button">Simulate movement</button>
                <button class="btn" id="keyboardHintBtn" type="button">Keyboard test</button>
                <button class="btn" id="graphBtn" type="button">Show route graph</button>
                <button class="btn" id="copyBtn" type="button">Copy route link</button>
            </div>
        </details>

        <div class="notice">
            Start and destination are both changeable. Live motion uses the selected starting place, then follows mapped resort roads and chooses the closest branch to the phone heading at each junction.
        </div>
    </aside>
</div>
<script src="{{ request()->getBaseUrl() }}/vendor/itqan-guest-map-leaflet/leaflet.js?v=1.9.4"></script>
<script src="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/motion-engine.js?v=20260710-google-ui1"></script>
<script src="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/route-follow-engine.js?v=20260710-google-ui1"></script>
<script src="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/graph-walker-engine.js?v=20260710-google-ui1"></script>
<script src="{{ request()->getBaseUrl() }}/assets/itqan-external-guest-map/guest-map.js?v=20260710-google-ui1"></script>
</body>
</html>
