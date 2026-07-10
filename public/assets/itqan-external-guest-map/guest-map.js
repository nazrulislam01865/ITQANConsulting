(function () {
  'use strict';

  const Geometry = window.ResortPathGeometry;
  const state = {
    data: null,
    map: null,
    bounds: null,
    activeCategory: 'all',
    search: '',
    placeMarkers: new Map(),
    roadLayers: [],
    junctionLayers: [],
    routeLayers: [],
    selectedFrom: null,
    selectedTo: null,
    activeRoute: null,
    activeRouteBounds: null,
    toastTimer: null,
    viewportTimer: null,
    viewportWidth: 0,
    viewportHeight: 0,
    viewportOrientation: '',
  };

  const el = {
    app: document.getElementById('mapApp'),
    panel: document.getElementById('mapPanel'),
    backdrop: document.getElementById('panelBackdrop'),
    panelHandle: document.getElementById('panelHandle'),
    openPanel: document.getElementById('openPanelBtn'),
    closePanel: document.getElementById('closePanelBtn'),
    search: document.getElementById('searchInput'),
    categoryChips: document.getElementById('categoryChips'),
    placeList: document.getElementById('placeList'),
    placeCount: document.getElementById('placeCount'),
    from: document.getElementById('fromSelect'),
    to: document.getElementById('toSelect'),
    showPath: document.getElementById('showPathBtn'),
    clearPath: document.getElementById('clearPathBtn'),
    swap: document.getElementById('swapBtn'),
    routeResult: document.getElementById('routeResult'),
    routeResultTitle: document.getElementById('routeResultTitle'),
    distance: document.getElementById('distanceValue'),
    walk: document.getElementById('walkValue'),
    buggy: document.getElementById('buggyValue'),
    mapName: document.getElementById('mapName'),
    zoomIn: document.getElementById('zoomInBtn'),
    zoomOut: document.getElementById('zoomOutBtn'),
    fitMap: document.getElementById('fitMapBtn'),
    fullscreen: document.getElementById('fullscreenBtn'),
    mobileRoute: document.getElementById('mobileRouteCard'),
    mobileRouteTitle: document.getElementById('mobileRouteTitle'),
    mobileDistance: document.getElementById('mobileDistance'),
    mobileClear: document.getElementById('mobileClearBtn'),
    toast: document.getElementById('mapToast'),
  };

  function baseUrl(path) {
    const root = String(document.body.dataset.baseUrl || '').replace(/\/$/, '');
    return `${root}${path}`;
  }

  function isMobile() {
    return window.matchMedia('(max-width: 900px), (max-width: 1100px) and (max-height: 600px)').matches;
  }

  function isCompactLandscape() {
    const viewportHeight = window.visualViewport?.height || window.innerHeight;
    return isMobile() && window.matchMedia('(orientation: landscape)').matches && viewportHeight <= 600;
  }

  function syncViewportMetrics(options = {}) {
    const viewport = window.visualViewport;
    const height = Math.max(240, Math.round(viewport?.height || window.innerHeight));
    const width = Math.max(280, Math.round(viewport?.width || window.innerWidth));
    const offsetTop = Math.max(0, Math.round(viewport?.offsetTop || 0));
    const orientation = width >= height ? 'landscape' : 'portrait';
    const widthChanged = Math.abs(width - state.viewportWidth) > 36;
    const orientationChanged = Boolean(state.viewportOrientation && state.viewportOrientation !== orientation);

    state.viewportWidth = width;
    state.viewportHeight = height;
    state.viewportOrientation = orientation;

    document.documentElement.style.setProperty('--app-height', `${height}px`);
    document.documentElement.style.setProperty('--app-width', `${width}px`);
    document.documentElement.style.setProperty('--visual-offset-top', `${offsetTop}px`);

    if (!state.map) return;
    clearTimeout(state.viewportTimer);
    state.viewportTimer = setTimeout(() => {
      state.map.invalidateSize(false);
      if (options.refit || widthChanged || orientationChanged) {
        refitCurrentView(false);
      }
      updateZoomButtons();
    }, 90);
  }

  syncViewportMetrics();

  function pointToLatLng(point) {
    return L.latLng(Number(point.y), Number(point.x));
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, (character) => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'}[character]));
  }

  function showToast(message) {
    clearTimeout(state.toastTimer);
    el.toast.textContent = message;
    el.toast.classList.add('show');
    state.toastTimer = setTimeout(() => el.toast.classList.remove('show'), 2600);
  }

  async function fetchJson(url) {
    const response = await fetch(url, {headers: {'Accept': 'application/json'}});
    let payload = {};
    try { payload = await response.json(); } catch (_) { payload = {}; }
    if (!response.ok) throw new Error(payload.message || 'The map request could not be completed.');
    return payload;
  }

  async function initialize() {
    try {
      state.data = await fetchJson(document.body.dataset.mapDataUrl || baseUrl('/external-guest-map/api/data'));
      el.mapName.textContent = state.data.map.name;
      buildMap();
      renderCategories();
      renderSelects();
      renderPlaces();
      wireEvents();
      collapsePanel();
      applyInitialSelection();
    } catch (error) {
      console.error(error);
      showToast(error.message || 'Unable to load the resort map.');
    }
  }

  function buildMap() {
    const mapData = state.data.map;
    state.bounds = L.latLngBounds([[0, 0], [Number(mapData.height), Number(mapData.width)]]);
    state.map = L.map('leafletMap', {
      crs: L.CRS.Simple,
      minZoom: -2.5,
      maxZoom: 3,
      zoomSnap: 0.25,
      zoomDelta: 0.5,
      zoomControl: false,
      attributionControl: false,
      preferCanvas: true,
      wheelPxPerZoomLevel: 90,
      tap: true,
      bounceAtZoomLimits: false,
      zoomAnimation: false,
      fadeAnimation: false,
      markerZoomAnimation: false,
      maxBoundsViscosity: 0.35,
    });

    L.imageOverlay(mapData.image, state.bounds, {interactive: false, className: 'resort-template-overlay'}).addTo(state.map);
    state.map.setMaxBounds(state.bounds.pad(0.32));
    drawRoadNetwork();
    drawPlaces();

    requestAnimationFrame(() => {
      state.map.invalidateSize(false);
      fitWholeMap(false);
      updateLabelVisibility();
      updateZoomButtons();
    });

    state.map.on('zoomend', () => {
      updateLabelVisibility();
      updateZoomButtons();
    });
    state.map.on('click', () => {
      if (isMobile() && el.panel.classList.contains('expanded')) collapsePanel();
    });
  }

  function edgeCurve(edge) {
    return Geometry.smoothPolyline(edge.path_points, {
      seed: Number(edge.id),
      subtleBend: true,
      tension: 0.6,
      sampleEvery: 8,
      maxHandleRatio: 0.25,
    });
  }

  function drawRoadNetwork() {
    const renderer = L.canvas({padding: 0.4});
    state.data.edges.forEach((edge) => {
      const points = edgeCurve(edge);
      if (points.length < 2) return;
      const layer = L.polyline(points.map(pointToLatLng), {
        renderer,
        color: '#727c77',
        weight: 5,
        opacity: 0.46,
        lineCap: 'butt',
        lineJoin: 'round',
        interactive: false,
        smoothFactor: 0.4,
      }).addTo(state.map);
      state.roadLayers.push(layer);
    });

    Object.values(state.data.nodes).forEach((node) => {
      const layer = L.circleMarker(pointToLatLng(node), {
        renderer,
        radius: 2.65,
        stroke: false,
        fill: true,
        fillColor: '#727c77',
        fillOpacity: 0.55,
        interactive: false,
      }).addTo(state.map);
      state.junctionLayers.push(layer);
    });
  }

  function markerHtml(place) {
    const content = place.pin_number || place.icon || '•';
    return `<div class="map-pin-wrap" data-place-id="${escapeHtml(place.id)}">
      <div class="map-pin" style="--pin-color:${escapeHtml(place.color)}">${escapeHtml(content)}</div>
      <div class="map-pin-label">${escapeHtml(place.name)}</div>
    </div>`;
  }

  function drawPlaces() {
    state.data.places.forEach((place) => {
      const icon = L.divIcon({
        className: '',
        html: markerHtml(place),
        iconSize: [34, 50],
        iconAnchor: [17, 42],
        popupAnchor: [0, -38],
      });
      const marker = L.marker(pointToLatLng(place), {icon, keyboard: true, title: place.name});
      marker.bindPopup(placePopupHtml(place), {className: 'place-popup-shell', maxWidth: 280});
      marker.on('click', () => markSelectedPlace(place.id));
      marker.addTo(state.map);
      state.placeMarkers.set(place.id, marker);
    });
  }

  function placePopupHtml(place) {
    const subtitle = place.description || place.subtitle || place.category_name || 'Resort place';
    return `<div class="place-popup">
      <h3>${escapeHtml(place.icon || '')} ${escapeHtml(place.name)}</h3>
      <p>${escapeHtml(subtitle)}</p>
      <div class="popup-actions">
        <button class="start" type="button" data-map-action="from" data-place-id="${escapeHtml(place.id)}">Use as start</button>
        <button class="destination" type="button" data-map-action="to" data-place-id="${escapeHtml(place.id)}">Path to here</button>
      </div>
    </div>`;
  }

  function renderCategories() {
    const categories = [{id: 'all', name: 'All'}, ...state.data.categories];
    el.categoryChips.innerHTML = categories.map((category) => `
      <button type="button" class="category-chip${category.id === state.activeCategory ? ' active' : ''}" data-category="${escapeHtml(category.id)}">${escapeHtml(category.name)}</button>
    `).join('');
  }

  function renderSelects() {
    const options = state.data.places.map((place) => `<option value="${escapeHtml(place.id)}">${escapeHtml(place.name)}</option>`).join('');
    el.from.innerHTML = options;
    el.to.innerHTML = options;
  }

  function filteredPlaces() {
    const query = state.search.trim().toLowerCase();
    return state.data.places.filter((place) => {
      const categoryMatches = state.activeCategory === 'all' || place.category === state.activeCategory;
      const text = `${place.name} ${place.subtitle || ''} ${place.description || ''} ${place.category_name || ''}`.toLowerCase();
      return categoryMatches && (!query || text.includes(query));
    });
  }

  function renderPlaces() {
    const places = filteredPlaces();
    el.placeCount.textContent = String(places.length);
    if (!places.length) {
      el.placeList.innerHTML = '<div class="empty-state">No places match this search.</div>';
    } else {
      el.placeList.innerHTML = places.map((place) => `
        <button type="button" class="place-card" data-place-card="${escapeHtml(place.id)}" style="--pin-color:${escapeHtml(place.color)}">
          <span class="place-card-icon">${escapeHtml(place.icon || '📍')}</span>
          <span class="place-card-copy"><strong>${escapeHtml(place.name)}</strong><span>${escapeHtml(place.category_name || place.subtitle || 'Resort place')}</span></span>
          <span class="place-card-arrow">›</span>
        </button>
      `).join('');
    }
    updateMarkerVisibility(new Set(places.map((place) => place.id)));
  }

  function updateMarkerVisibility(visibleIds) {
    state.placeMarkers.forEach((marker, id) => {
      if (visibleIds.has(id)) {
        if (!state.map.hasLayer(marker)) marker.addTo(state.map);
      } else if (state.map.hasLayer(marker)) {
        marker.removeFrom(state.map);
      }
    });
  }

  function updateLabelVisibility() {
    const visible = state.map.getZoom() >= 0.25 && !isMobile();
    state.map.getContainer().classList.toggle('labels-visible', visible);
  }

  function applyInitialSelection() {
    const from = document.body.dataset.defaultFrom;
    const to = document.body.dataset.defaultTo;
    const ids = new Set(state.data.places.map((place) => place.id));
    el.from.value = ids.has(from) ? from : state.data.places[0]?.id || '';
    el.to.value = ids.has(to) ? to : state.data.places.find((place) => place.id !== el.from.value)?.id || el.from.value;
    state.selectedFrom = el.from.value;
    state.selectedTo = el.to.value;
    if (ids.has(from) && ids.has(to) && from !== to) showPath();
  }

  function wireEvents() {
    el.search.addEventListener('input', () => {
      state.search = el.search.value;
      renderPlaces();
    });
    el.categoryChips.addEventListener('click', (event) => {
      const button = event.target.closest('[data-category]');
      if (!button) return;
      state.activeCategory = button.dataset.category;
      renderCategories();
      renderPlaces();
    });
    el.placeList.addEventListener('click', (event) => {
      const card = event.target.closest('[data-place-card]');
      if (!card) return;
      focusPlace(card.dataset.placeCard);
    });
    el.from.addEventListener('change', () => { state.selectedFrom = el.from.value; markSelectedPlace(el.from.value); });
    el.to.addEventListener('change', () => { state.selectedTo = el.to.value; markSelectedPlace(el.to.value); });
    el.showPath.addEventListener('click', showPath);
    el.clearPath.addEventListener('click', clearRoute);
    el.mobileClear.addEventListener('click', clearRoute);
    el.mobileRoute.addEventListener('click', (event) => { if (!event.target.closest('button')) expandPanel(); });
    el.swap.addEventListener('click', () => {
      const previous = el.from.value;
      el.from.value = el.to.value;
      el.to.value = previous;
      state.selectedFrom = el.from.value;
      state.selectedTo = el.to.value;
      if (state.activeRoute) showPath();
    });
    bindMapTool(el.zoomIn, () => changeZoom(0.5));
    bindMapTool(el.zoomOut, () => changeZoom(-0.5));
    bindMapTool(el.fitMap, () => fitWholeMap(true));
    bindMapTool(el.fullscreen, toggleFullscreen);
    el.panelHandle.addEventListener('click', (event) => {
      event.preventDefault();
      el.panel.classList.contains('expanded') ? collapsePanel() : expandPanel();
    });
    el.openPanel.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      expandPanel();
    });
    el.closePanel.addEventListener('click', collapsePanel);
    el.backdrop.addEventListener('click', collapsePanel);
    document.addEventListener('click', handlePopupAction);
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') collapsePanel();
    });
    const handleViewportChange = () => {
      syncViewportMetrics();
      updateLabelVisibility();
      if (!isMobile()) collapsePanel();
    };
    window.addEventListener('resize', handleViewportChange, {passive: true});
    window.addEventListener('orientationchange', () => {
      setTimeout(() => syncViewportMetrics({refit: true}), 160);
    }, {passive: true});
    if (window.visualViewport) {
      window.visualViewport.addEventListener('resize', handleViewportChange, {passive: true});
      window.visualViewport.addEventListener('scroll', () => syncViewportMetrics(), {passive: true});
    }
    if ('ResizeObserver' in window) {
      const observer = new ResizeObserver(() => syncViewportMetrics());
      observer.observe(el.app);
    }
  }

  function handlePopupAction(event) {
    const button = event.target.closest('[data-map-action]');
    if (!button) return;
    const placeId = button.dataset.placeId;
    if (button.dataset.mapAction === 'from') {
      el.from.value = placeId;
      state.selectedFrom = placeId;
      showToast('Starting place selected.');
    } else {
      el.to.value = placeId;
      state.selectedTo = placeId;
      state.map.closePopup();
      showPath();
    }
  }

  function focusPlace(placeId) {
    const place = state.data.places.find((item) => item.id === placeId);
    const marker = state.placeMarkers.get(placeId);
    if (!place || !marker) return;
    markSelectedPlace(placeId);
    state.map.flyTo(pointToLatLng(place), Math.max(state.map.getZoom(), 0.9), {duration: 0.45});
    marker.openPopup();
    if (isMobile()) collapsePanel();
  }

  function markSelectedPlace(placeId) {
    document.querySelectorAll('.map-pin-wrap.selected').forEach((node) => node.classList.remove('selected'));
    const marker = state.placeMarkers.get(placeId);
    const node = marker && marker.getElement()?.querySelector('.map-pin-wrap');
    if (node) node.classList.add('selected');
  }

  async function showPath() {
    const from = el.from.value;
    const to = el.to.value;
    if (!from || !to) return showToast('Choose a starting place and destination.');
    if (from === to) return showToast('Choose two different places.');

    el.showPath.disabled = true;
    el.showPath.textContent = 'Loading…';
    try {
      const query = new URLSearchParams({from, to, mode: 'walk'});
      const routeBase = document.body.dataset.mapRouteUrl || baseUrl('/external-guest-map/api/route');
      const route = await fetchJson(`${routeBase}?${query.toString()}`);
      state.selectedFrom = from;
      state.selectedTo = to;
      state.activeRoute = route;
      drawRoute(route);
      updateRouteSummary(route);
      updateUrl(from, to);
      if (isMobile()) collapsePanel();
    } catch (error) {
      showToast(error.message);
    } finally {
      el.showPath.disabled = false;
      el.showPath.textContent = 'Show pathway';
    }
  }

  function drawRoute(route) {
    clearRouteLayers();
    const curved = Geometry.smoothPolyline(route.path, {
      subtleBend: true,
      seed: (route.edge_path || []).reduce((sum, value) => sum + Number(value || 0), 0),
      tension: 0.62,
      sampleEvery: 7,
      maxHandleRatio: 0.24,
    });
    const latLngs = curved.map(pointToLatLng);
    if (!latLngs.length) return;

    const casing = L.polyline(latLngs, {
      color: '#ffffff',
      weight: 14,
      opacity: 0.92,
      lineCap: 'round',
      lineJoin: 'round',
      interactive: false,
      smoothFactor: 0.2,
      pane: 'overlayPane',
    }).addTo(state.map);
    const line = L.polyline(latLngs, {
      color: '#1976d2',
      weight: 7,
      opacity: 0.98,
      lineCap: 'round',
      lineJoin: 'round',
      interactive: false,
      smoothFactor: 0.2,
      pane: 'overlayPane',
    }).addTo(state.map);

    const start = L.marker(pointToLatLng(route.from), {
      icon: L.divIcon({className: '', html: '<div class="route-start-marker">A</div>', iconSize: [32, 32], iconAnchor: [16, 16]}),
      interactive: false,
    }).addTo(state.map);
    const end = L.marker(pointToLatLng(route.to), {
      icon: L.divIcon({className: '', html: '<div class="route-end-marker">B</div>', iconSize: [32, 32], iconAnchor: [16, 16]}),
      interactive: false,
    }).addTo(state.map);

    state.routeLayers.push(casing, line, start, end);
    state.activeRouteBounds = line.getBounds();
    fitRouteBounds(true);
  }

  function updateRouteSummary(route) {
    const title = `${route.from.name} → ${route.to.name}`;
    el.routeResult.hidden = false;
    el.routeResultTitle.textContent = title;
    el.distance.textContent = formatDistance(route.distance_meters);
    el.walk.textContent = formatMinutes(route.walk_minutes);
    el.buggy.textContent = formatMinutes(route.buggy_minutes);
    el.mobileRoute.hidden = false;
    el.mobileRouteTitle.textContent = title;
    el.mobileDistance.textContent = formatDistance(route.distance_meters);
  }

  function clearRoute() {
    clearRouteLayers();
    state.activeRoute = null;
    el.routeResult.hidden = true;
    el.mobileRoute.hidden = true;
    updateUrl(null, null);
    fitWholeMap();
  }

  function clearRouteLayers() {
    state.routeLayers.forEach((layer) => layer.removeFrom(state.map));
    state.routeLayers = [];
    state.activeRouteBounds = null;
  }

  function updateUrl(from, to) {
    const url = new URL(window.location.href);
    if (from && to) {
      url.searchParams.set('from', from);
      url.searchParams.set('to', to);
    } else {
      url.searchParams.delete('from');
      url.searchParams.delete('to');
    }
    history.replaceState({}, '', url);
  }

  function formatDistance(meters) {
    const value = Number(meters || 0);
    return value >= 1000 ? `${(value / 1000).toFixed(value >= 10000 ? 0 : 1)} km` : `${Math.round(value)} m`;
  }

  function formatMinutes(minutes) {
    const value = Number(minutes || 0);
    return value === 0 ? '0 min' : `${Math.max(1, Math.round(value))} min`;
  }

  function currentMapPadding() {
    if (!isMobile()) {
      return {topLeft: [12, 12], bottomRight: [12, 12]};
    }

    if (isCompactLandscape()) {
      return {topLeft: [10, 54], bottomRight: [10, 62]};
    }

    return {topLeft: [14, 64], bottomRight: [14, 82]};
  }

  function currentRoutePadding() {
    if (!isMobile()) {
      return {topLeft: [28, 74], bottomRight: [28, 54]};
    }

    if (isCompactLandscape()) {
      return {topLeft: [18, 58], bottomRight: [18, 72]};
    }

    return {topLeft: [22, 70], bottomRight: [22, 188]};
  }

  function fitRouteBounds(animate = true) {
    if (!state.map || !state.activeRouteBounds?.isValid()) return;
    state.map.stop();
    state.map.invalidateSize(false);
    const padding = currentRoutePadding();
    state.map.fitBounds(state.activeRouteBounds, {
      paddingTopLeft: padding.topLeft,
      paddingBottomRight: padding.bottomRight,
      maxZoom: 1.8,
      animate,
      duration: animate ? 0.35 : 0,
    });
    updateZoomButtons();
  }

  function refitCurrentView(animate = false) {
    if (state.activeRouteBounds?.isValid()) {
      fitRouteBounds(animate);
    } else {
      fitWholeMap(animate);
    }
  }

  function fitWholeMap(animate = true) {
    if (!state.map || !state.bounds) return;
    state.map.stop();
    state.map.invalidateSize(false);
    const padding = currentMapPadding();
    state.map.fitBounds(state.bounds, {
      paddingTopLeft: padding.topLeft,
      paddingBottomRight: padding.bottomRight,
      animate,
      duration: animate ? 0.25 : 0,
    });
    updateZoomButtons();
  }

  function changeZoom(delta) {
    if (!state.map) return;
    state.map.stop();
    state.map.invalidateSize(false);
    const current = state.map.getZoom();
    const next = Math.max(state.map.getMinZoom(), Math.min(state.map.getMaxZoom(), current + delta));
    if (Math.abs(next - current) < 0.001) return;
    state.map.setZoom(next, {animate: false});
    updateZoomButtons();
  }

  function updateZoomButtons() {
    if (!state.map) return;
    const zoom = state.map.getZoom();
    el.zoomIn.disabled = zoom >= state.map.getMaxZoom() - 0.001;
    el.zoomOut.disabled = zoom <= state.map.getMinZoom() + 0.001;
  }

  function bindMapTool(button, handler) {
    if (!button) return;
    L.DomEvent.disableClickPropagation(button);
    L.DomEvent.disableScrollPropagation(button);
    button.addEventListener('pointerdown', (event) => event.stopPropagation());
    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      handler();
    });
  }

  async function toggleFullscreen() {
    try {
      if (!document.fullscreenElement) await document.documentElement.requestFullscreen();
      else await document.exitFullscreen();
      setTimeout(() => syncViewportMetrics({refit: true}), 180);
    } catch (_) {
      showToast('Fullscreen is not available in this browser.');
    }
  }

  function expandPanel() {
    if (!isMobile()) return;
    el.panel.classList.add('expanded');
    el.backdrop.classList.add('visible');
    el.openPanel.setAttribute('aria-expanded', 'true');
    el.panel.setAttribute('aria-hidden', 'false');
    document.body.classList.add('panel-open');
    el.panel.scrollTop = 0;
  }

  function collapsePanel() {
    el.panel.classList.remove('expanded');
    el.backdrop.classList.remove('visible');
    el.openPanel.setAttribute('aria-expanded', 'false');
    el.panel.setAttribute('aria-hidden', isMobile() ? 'true' : 'false');
    document.body.classList.remove('panel-open');
  }

  initialize();
})();
