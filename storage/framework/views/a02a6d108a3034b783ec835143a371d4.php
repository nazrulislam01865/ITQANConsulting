<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#173f2d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>The Palace Resort Map</title>
    <link rel="stylesheet" href="<?php echo e(request()->getBaseUrl()); ?>/vendor/itqan-guest-map-leaflet/leaflet.css?v=1.9.4">
    <link rel="stylesheet" href="<?php echo e(request()->getBaseUrl()); ?>/assets/itqan-external-guest-map/guest-map.css?v=20260710-mobile-ui-fix">
</head>
<body data-base-url="<?php echo e(request()->getBaseUrl()); ?>" data-map-data-url="<?php echo e(route('external-guest-map.api.data', [], false)); ?>" data-map-route-url="<?php echo e(route('external-guest-map.api.route', [], false)); ?>" data-default-from="<?php echo e($from); ?>" data-default-to="<?php echo e($to); ?>">
<div class="map-app" id="mapApp">
    <aside class="map-panel" id="mapPanel" aria-label="Map controls" aria-hidden="false">
        <button class="panel-handle" id="panelHandle" type="button" aria-label="Expand or collapse places panel"><span></span></button>

        <header class="brand-row">
            <div class="brand-mark">P</div>
            <div class="brand-copy">
                <h1>The Palace Resort Map</h1>
                <p>Find places and display the saved pathway</p>
            </div>
            <button class="icon-button mobile-only" id="closePanelBtn" type="button" aria-label="Collapse panel">⌄</button>
        </header>

        <div class="search-box">
            <span aria-hidden="true">⌕</span>
            <input id="searchInput" type="search" autocomplete="off" placeholder="Search reception, pool, cafe...">
        </div>

        <section class="route-planner" aria-labelledby="routePlannerTitle">
            <div class="section-heading">
                <div>
                    <span class="eyebrow">Pathway</span>
                    <h2 id="routePlannerTitle">Choose two places</h2>
                </div>
                <button class="text-button" id="swapBtn" type="button">Swap</button>
            </div>

            <label class="select-field">
                <span>Starting place</span>
                <select id="fromSelect"></select>
            </label>
            <label class="select-field">
                <span>Destination</span>
                <select id="toSelect"></select>
            </label>

            <div class="route-actions">
                <button class="primary-button" id="showPathBtn" type="button">Show pathway</button>
                <button class="secondary-button" id="clearPathBtn" type="button">Clear</button>
            </div>
        </section>

        <section class="route-result" id="routeResult" hidden aria-live="polite">
            <div class="route-result-copy">
                <span id="routeResultLabel">Saved pathway</span>
                <strong id="routeResultTitle">—</strong>
            </div>
            <div class="route-metrics">
                <div><b id="distanceValue">—</b><span>Distance</span></div>
                <div><b id="walkValue">—</b><span>Walk</span></div>
                <div><b id="buggyValue">—</b><span>Buggy</span></div>
            </div>
        </section>

        <section class="places-section">
            <div class="section-heading places-heading">
                <div>
                    <span class="eyebrow">Explore</span>
                    <h2>Places</h2>
                </div>
                <span class="place-count" id="placeCount">0</span>
            </div>
            <div class="category-chips" id="categoryChips" aria-label="Place categories"></div>
            <div class="place-list" id="placeList"></div>
        </section>
    </aside>

    <button class="panel-backdrop" id="panelBackdrop" type="button" aria-label="Close places and pathway panel" tabindex="-1"></button>

    <main class="map-stage">
        <div id="leafletMap" class="leaflet-map" aria-label="Interactive resort map"></div>

        <div class="map-heading">
            <div>
                <span>Interactive map</span>
                <strong id="mapName">The Palace Resort</strong>
            </div>
            <a href="<?php echo e(route('admin.map.dashboard')); ?>" class="admin-link">Map setup</a>
        </div>

        <button class="open-panel-button" id="openPanelBtn" type="button" aria-controls="mapPanel" aria-expanded="false">
            <span>⌕</span> Places &amp; pathway
        </button>

        <div class="map-tools" aria-label="Map zoom controls">
            <button type="button" id="zoomInBtn" aria-label="Zoom in">+</button>
            <button type="button" id="zoomOutBtn" aria-label="Zoom out">−</button>
            <button type="button" id="fitMapBtn" aria-label="Fit whole map">⌖</button>
            <button type="button" id="fullscreenBtn" aria-label="Toggle fullscreen">⛶</button>
        </div>

        <div class="mobile-route-card" id="mobileRouteCard" hidden>
            <div>
                <span id="mobileRouteLabel">Saved pathway</span>
                <strong id="mobileRouteTitle">—</strong>
            </div>
            <b id="mobileDistance">—</b>
            <button type="button" id="mobileClearBtn" aria-label="Clear pathway">×</button>
        </div>

        <div class="map-toast" id="mapToast" role="status" aria-live="polite"></div>
    </main>
</div>
<script src="<?php echo e(request()->getBaseUrl()); ?>/vendor/itqan-guest-map-leaflet/leaflet.js?v=1.9.4"></script>
<script src="<?php echo e(request()->getBaseUrl()); ?>/assets/itqan-external-guest-map/path-geometry.js?v=20260710-mobile-ui-fix"></script>
<script src="<?php echo e(request()->getBaseUrl()); ?>/assets/itqan-external-guest-map/guest-map.js?v=20260710-mobile-ui-fix"></script>
</body>
</html>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/ITQANConsulting/resources/views/external/itqan-guest-map/index.blade.php ENDPATH**/ ?>