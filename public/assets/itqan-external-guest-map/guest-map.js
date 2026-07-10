(function () {
  'use strict';

  // The resort map uses a selectable mapped place as the navigation origin.
  // Kids Zone remains only the initial default and can be changed by the guest.
  const NAV_CONFIG = Object.freeze({
    defaultStartPlaceId: 'kids',
    headingHistoryMs: 650,
    headingHistoryMax: 12,
    routeBiasDegrees: 9,
  });

  const state = {
    data: null,
    places: [],
    activeCategory: 'all',
    selectedFrom: null,
    selectedTo: null,
    startPoint: null,
    route: null,
    graph: null,
    map: null,
    bounds: null,
    layers: {},
    placeMarkers: new Map(),
    youMarker: null,
    navigation: {
      active: false,
      watchId: null,
      simulateTimer: null,
      anchorGeo: null,
      anchorMapPoint: null,
      lastGeo: null,
      lastRawPoint: null,
      mapPoint: null,
      lastAcceptedPoint: null,
      routeBearing: null,
      heading: null,
      headingSource: null,
      headingListenerActive: false,
      motionEngine: null,
      graphWalker: null,
      headingHistory: [],
      lastBranchMessage: null,
      motionListenerActive: false,
      orientationListenerActive: false,
      motionPermission: 'unknown',
      orientationPermission: 'unknown',
      stepCount: 0,
      lastMotionStepAt: 0,
      lastSensorAt: 0,
      motionDataReceived: false,
      orientationDataReceived: false,
      sensorWatchdogTimer: null,
      wakeLock: null,
      lastAccuracy: null,
      lastSentAt: 0,
      totalMovedMeters: 0,
      remainingDistanceMeters: 0,
      remainingPath: [],
      progressPercent: 0,
      sensorStatus: 'Checking',
      currentRoad: null,
      blockedReason: null,
      source: 'off',
      snapPx: 22,
      movementLimitPx: 55,
      mapNorthRotationDeg: 0,
      keyboardStepMeters: 2.2,
      minGpsMoveMeters: 0.8,
      geoCalibration: null,
      smoothedRawPoint: null,
      followMode: true,
      voiceEnabled: true,
      lastSpokenKey: null,
      currentManeuvers: [],
      currentManeuverIndex: 0,
      routeSignature: null,
      lastRouteRefreshAt: 0,
      offRouteSamples: 0,
      rerouteCount: 0,
    },
    lifecycleWired: false,
    pickingStart: false,
  };

  const el = {
    app: document.getElementById('guestMapApp'),
    map: document.getElementById('leafletMap'),
    fromSelect: document.getElementById('fromSelect'),
    toSelect: document.getElementById('toSelect'),
    filters: document.getElementById('filters'),
    search: document.getElementById('search'),
    places: document.getElementById('places'),
    summaryTitle: document.getElementById('summaryTitle'),
    summaryText: document.getElementById('summaryText'),
    distanceStat: document.getElementById('distanceStat'),
    walkStat: document.getElementById('walkStat'),
    buggyStat: document.getElementById('buggyStat'),
    gpsStat: document.getElementById('gpsStat'),
    progressStat: document.getElementById('progressStat'),
    movedStat: document.getElementById('movedStat'),
    headingStat: document.getElementById('headingStat'),
    sensorStat: document.getElementById('sensorStat'),
    steps: document.getElementById('steps'),
    side: document.getElementById('sidePanel'),
    popup: document.getElementById('popup'),
    bottomCard: document.getElementById('bottomCard'),
    bottomTitle: document.getElementById('bottomTitle'),
    bottomText: document.getElementById('bottomText'),
    toast: document.getElementById('toast'),
    navGuidance: document.getElementById('navGuidance'),
    maneuverIcon: document.getElementById('maneuverIcon'),
    maneuverDistance: document.getElementById('maneuverDistance'),
    maneuverText: document.getElementById('maneuverText'),
    maneuverSubtext: document.getElementById('maneuverSubtext'),
    voiceBtn: document.getElementById('voiceBtn'),
    followBtn: document.getElementById('followBtn'),
    trackingModeBadge: document.getElementById('trackingModeBadge'),
    rerouteBadge: document.getElementById('rerouteBadge'),
    pickStartBtn: document.getElementById('pickStartBtn'),
    panelCloseBtn: document.getElementById('panelCloseBtn'),
  };

  function apiUrl(path) {
    const base = String(document.body.dataset.baseUrl || '').replace(/\/$/, '');
    return base + path;
  }

  function configuredUrl(datasetKey, fallbackPath) {
    return document.body.dataset[datasetKey] || apiUrl(fallbackPath);
  }

  function appendQuery(url, query) {
    return `${url}${url.includes('?') ? '&' : '?'}${query}`;
  }

  function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  function mapFileUrl(path) {
    const value = String(path || '');
    if (/^(https?:|data:|blob:)/i.test(value)) return value;
    return document.body.dataset.baseUrl + '/' + value.replace(/^\/+/, '');
  }

  async function init() {
    try {
      await loadData();
      setupLeafletMap();
      buildGraph();
      buildGeoCalibration();
      setupMotionEngine();
      drawRoadNetwork();
      renderFilters();
      renderSelects();
      loadUrlState();
      renderPlaces();
      renderPlaceMarkers();
      wireControls();
      wireMobileLifecycle();
      updateSensorStatus();

      if (state.selectedFrom) setStartFromPlace(state.selectedFrom);
      if (state.selectedTo) selectPlace(state.selectedTo, false);
      if (state.selectedFrom && state.selectedTo) await showRoute(false);
    } catch (error) {
      console.error(error);
      toast('Could not initialize map. Check console.');
    }
  }

  async function loadData() {
    const response = await fetch(configuredUrl('mapDataUrl', '/api/guest-map/data'), {headers: {'Accept': 'application/json'}});
    if (!response.ok) throw new Error('Could not load map data.');
    state.data = await response.json();
    state.places = state.data.places || [];
    state.navigation.mapNorthRotationDeg = Number(state.data?.map?.map_north_rotation_deg || 0);
  }

  function setupLeafletMap() {
    const w = Number(state.data.map.width || 1200);
    const h = Number(state.data.map.height || 800);

    state.map = L.map(el.map, {
      crs: L.CRS.Simple,
      attributionControl: false,
      zoomControl: false,
      minZoom: -2,
      maxZoom: 3,
      zoomSnap: 0.25,
      wheelPxPerZoomLevel: 80,
      maxBoundsViscosity: 0.75,
    });

    const southWest = state.map.unproject([0, h], 0);
    const northEast = state.map.unproject([w, 0], 0);
    state.bounds = L.latLngBounds(southWest, northEast);

    const mapImage = state.data.map.image || document.body.dataset.mapFallbackUrl || mapFileUrl('assets/itqan-external-guest-map/template-map.svg');
    L.imageOverlay(mapImage, state.bounds, {interactive: false}).addTo(state.map);
    state.map.fitBounds(state.bounds);
    state.map.setMaxBounds(state.bounds.pad(0.35));

    state.layers.roads = L.layerGroup().addTo(state.map);
    state.layers.graph = L.layerGroup().addTo(state.map);
    state.layers.routes = L.layerGroup().addTo(state.map);
    state.layers.markers = L.layerGroup().addTo(state.map);
  }

  function pointToLatLng(point) {
    return state.map.unproject([Number(point.x), Number(point.y)], 0);
  }

  function latLngToPoint(latLng) {
    const p = state.map.project(latLng, 0);
    return {x: p.x, y: p.y};
  }

  function pointsToLatLngs(points) {
    return (points || []).map(pointToLatLng);
  }

  function fitToPoints(points) {
    if (!points || !points.length) return;
    state.map.fitBounds(L.latLngBounds(pointsToLatLngs(points)).pad(0.18), {animate: true});
  }

  function centerMap() {
    state.map.fitBounds(state.bounds, {animate: true});
  }

  function buildGraph() {
    const nodes = state.data.nodes || {};
    const adj = {};
    Object.keys(nodes).forEach(code => { adj[code] = []; });

    const edges = (state.data.edges || []).map(edge => {
      const path = (edge.path_points || []).map(p => ({x: Number(p.x), y: Number(p.y)}));
      const distanceMeters = Number(edge.distance_meters || polylineLengthPx(path) * metersPerPixel());
      return {...edge, path, distanceMeters};
    });

    edges.forEach(edge => {
      if (!edge.from || !edge.to || !adj[edge.from] || !adj[edge.to]) return;
      const forward = {...edge, reversed: false};
      const backward = {...edge, from: edge.to, to: edge.from, path: [...edge.path].reverse(), reversed: true};
      adj[edge.from].push(forward);
      adj[edge.to].push(backward);
    });

    state.graph = {nodes, edges, adj};
  }

  function buildGeoCalibration() {
    const points = (state.data?.map?.calibration_points || []).filter(point =>
      Number.isFinite(Number(point.lat)) && Number.isFinite(Number(point.lng)) &&
      Number.isFinite(Number(point.x)) && Number.isFinite(Number(point.y))
    ).map(point => ({...point, lat: Number(point.lat), lng: Number(point.lng), x: Number(point.x), y: Number(point.y)}));

    state.navigation.geoCalibration = null;
    if (points.length < 2) {
      if (el.trackingModeBadge) el.trackingModeBadge.textContent = 'QR/start-point GPS mode';
      return;
    }

    let pair = null;
    let longest = 0;
    for (let i = 0; i < points.length; i++) {
      for (let j = i + 1; j < points.length; j++) {
        const distance = haversineMeters(points[i], points[j]);
        if (distance > longest) {
          longest = distance;
          pair = [points[i], points[j]];
        }
      }
    }
    if (!pair || longest < 3) {
      if (el.trackingModeBadge) el.trackingModeBadge.textContent = 'Calibration points too close';
      return;
    }

    const origin = pair[0];
    const samples = points.map(point => {
      const offset = geoOffsetMeters(origin, point);
      return {sourceX: offset.east, sourceY: -offset.north, mapX: point.x, mapY: point.y};
    });
    const mean = samples.reduce((acc, sample) => ({
      sourceX: acc.sourceX + sample.sourceX / samples.length,
      sourceY: acc.sourceY + sample.sourceY / samples.length,
      mapX: acc.mapX + sample.mapX / samples.length,
      mapY: acc.mapY + sample.mapY / samples.length,
    }), {sourceX: 0, sourceY: 0, mapX: 0, mapY: 0});
    let denominator = 0;
    let numeratorA = 0;
    let numeratorB = 0;
    samples.forEach(sample => {
      const gx = sample.sourceX - mean.sourceX;
      const gy = sample.sourceY - mean.sourceY;
      const mx = sample.mapX - mean.mapX;
      const my = sample.mapY - mean.mapY;
      denominator += gx * gx + gy * gy;
      numeratorA += mx * gx + my * gy;
      numeratorB += my * gx - mx * gy;
    });
    if (denominator < 0.01) return;
    const a = numeratorA / denominator;
    const b = numeratorB / denominator;
    const translateX = mean.mapX - a * mean.sourceX + b * mean.sourceY;
    const translateY = mean.mapY - b * mean.sourceX - a * mean.sourceY;
    const scalePxPerMeter = Math.hypot(a, b);
    if (!Number.isFinite(scalePxPerMeter) || scalePxPerMeter <= 0) return;

    let squaredError = 0;
    samples.forEach(sample => {
      const predictedX = a * sample.sourceX - b * sample.sourceY + translateX;
      const predictedY = b * sample.sourceX + a * sample.sourceY + translateY;
      squaredError += (predictedX - sample.mapX) ** 2 + (predictedY - sample.mapY) ** 2;
    });
    const rmsErrorMeters = Math.sqrt(squaredError / samples.length) / scalePxPerMeter;

    state.navigation.geoCalibration = {
      origin,
      a,
      b,
      translateX,
      translateY,
      scalePxPerMeter,
      rotationRad: Math.atan2(b, a),
      baselineMeters: longest,
      controlPointCount: points.length,
      rmsErrorMeters,
    };
    if (el.trackingModeBadge) el.trackingModeBadge.textContent = `Live GPS calibrated (${points.length} points)`;
  }

  function absoluteGeoToMapPoint(geo) {
    const calibration = state.navigation.geoCalibration;
    if (!calibration) return null;
    const offset = geoOffsetMeters(calibration.origin, geo);
    const sourceX = offset.east;
    const sourceY = -offset.north;
    return {
      x: clamp(calibration.a * sourceX - calibration.b * sourceY + calibration.translateX, 0, Number(state.data.map.width)),
      y: clamp(calibration.b * sourceX + calibration.a * sourceY + calibration.translateY, 0, Number(state.data.map.height)),
    };
  }

  function smoothMapPoint(point, accuracyMeters) {
    if (!state.navigation.smoothedRawPoint) {
      state.navigation.smoothedRawPoint = {...point};
      return {...point};
    }
    const accuracy = Number.isFinite(Number(accuracyMeters)) ? Number(accuracyMeters) : 20;
    const alpha = clamp(0.72 - accuracy / 120, 0.2, 0.68);
    const previous = state.navigation.smoothedRawPoint;
    const smoothed = {
      x: previous.x + (point.x - previous.x) * alpha,
      y: previous.y + (point.y - previous.y) * alpha,
    };
    state.navigation.smoothedRawPoint = smoothed;
    return smoothed;
  }

  function drawRoadNetwork() {
    state.layers.roads.clearLayers();
    (state.graph.edges || []).forEach(edge => {
      if (!edge.path || edge.path.length < 2) return;
      L.polyline(pointsToLatLngs(edge.path), {
        className: 'road-base-line',
        color: '#b9c8bd',
        weight: 20,
        opacity: 1,
        lineCap: 'round',
        lineJoin: 'round',
        interactive: false,
      }).addTo(state.layers.roads);
      L.polyline(pointsToLatLngs(edge.path), {
        className: 'road-top-line',
        color: '#ffffff',
        weight: 14,
        opacity: 0.96,
        lineCap: 'round',
        lineJoin: 'round',
        interactive: false,
      }).addTo(state.layers.roads);
    });
  }

  function visiblePlaces() {
    const term = (el.search.value || '').trim().toLowerCase();
    return state.places.filter(place => {
      const matchesCat = state.activeCategory === 'all' || place.category === state.activeCategory;
      const text = `${place.name} ${place.subtitle || ''} ${place.description || ''}`.toLowerCase();
      return matchesCat && (!term || text.includes(term));
    });
  }

  function renderFilters() {
    const cats = [{id: 'all', name: 'All', color: '#174a33'}, ...(state.data.categories || [])];
    el.filters.innerHTML = cats.map(cat => `<button class="chip ${cat.id === state.activeCategory ? 'active' : ''}" data-cat="${escapeHtml(cat.id)}">${escapeHtml(cat.name)}</button>`).join('');
    el.filters.querySelectorAll('button').forEach(btn => btn.addEventListener('click', () => {
      state.activeCategory = btn.dataset.cat;
      renderFilters();
      renderPlaces();
      renderPlaceMarkers();
    }));
  }

  function renderSelects() {
    const options = state.places.map(p => `<option value="${escapeHtml(p.id)}">${p.pin_number || ''}. ${escapeHtml(p.name)}</option>`).join('');
    el.fromSelect.innerHTML = '<option value="">Select start</option>' + options;
    el.toSelect.innerHTML = '<option value="">Select destination</option>' + options;
  }

  function loadUrlState() {
    const url = new URLSearchParams(window.location.search);
    const requestedFrom = url.get('from') || document.body.dataset.defaultFrom;
    const defaultFrom = findPlace(requestedFrom)?.id
      || findPlace(NAV_CONFIG.defaultStartPlaceId)?.id
      || state.places[0]?.id
      || null;
    const defaultTo = findPlace('pool')?.id
      || state.places.find(place => place.id !== defaultFrom)?.id
      || null;
    const requestedTo = url.get('to') || document.body.dataset.defaultTo || defaultTo;

    state.selectedFrom = defaultFrom;
    el.fromSelect.value = defaultFrom || '';
    if (requestedTo && findPlace(requestedTo) && requestedTo !== defaultFrom) {
      state.selectedTo = requestedTo;
      el.toSelect.value = requestedTo;
    }
  }

  function renderPlaces() {
    const places = visiblePlaces();
    el.places.innerHTML = places.map(place => `
      <div class="place-card ${place.id === state.selectedTo ? 'active' : ''}" data-id="${escapeHtml(place.id)}">
        <div class="place-ico" style="background:${place.color || '#174a33'}">${escapeHtml(place.icon || place.pin_number || '•')}</div>
        <div><h3>${escapeHtml(place.name)}</h3><p>${escapeHtml(place.subtitle || place.category_name || '')}</p></div>
        <span class="tag">${escapeHtml(place.category_name || '')}</span>
      </div>
    `).join('') || '<div class="summary"><p>No place found. Try another search.</p></div>';
    el.places.querySelectorAll('.place-card').forEach(card => card.addEventListener('click', () => {
      selectPlace(card.dataset.id, false);
      state.selectedTo = card.dataset.id;
      el.toSelect.value = card.dataset.id;
      el.side.classList.add('open');
    }));
  }

  function renderPlaceMarkers() {
    state.placeMarkers.forEach(marker => state.layers.markers.removeLayer(marker));
    state.placeMarkers.clear();

    visiblePlaces().forEach(place => {
      const icon = L.divIcon({
        className: 'pin leaf-pin' + (place.id === state.selectedTo ? ' active' : ''),
        html: `<span>${escapeHtml(place.icon || place.pin_number || '•')}</span>`,
        iconSize: [42, 42],
        iconAnchor: [21, 42],
      });
      const marker = L.marker(pointToLatLng(place), {icon, interactive: true, keyboard: false})
        .on('click', () => {
          if (state.pickingStart) {
            finishStartPicking(place.id);
            return;
          }
          selectPlace(place.id, false);
          state.selectedTo = place.id;
          el.toSelect.value = place.id;
        })
        .addTo(state.layers.markers);
      marker.bindTooltip(place.name, {permanent: true, direction: 'bottom', className: 'place-label-leaflet', offset: [0, 3]});
      state.placeMarkers.set(place.id, marker);
    });

    placeYouMarker();
  }

  function placeYouMarker() {
    if (!state.startPoint) return;
    const point = {x: Number(state.startPoint.x), y: Number(state.startPoint.y)};
    if (!state.youMarker) {
      const icon = L.divIcon({
        className: 'you leaf-you',
        html: '<span class="bearing-arrow"></span>',
        iconSize: [26, 26],
        iconAnchor: [13, 13],
      });
      state.youMarker = L.marker(pointToLatLng(point), {icon, interactive: true, keyboard: false, zIndexOffset: 1000})
        .addTo(state.layers.markers)
        .on('dragstart', () => {
          if (state.navigation.active) toast('Stop navigation before moving the marker.');
        });
    } else {
      state.youMarker.setLatLng(pointToLatLng(point));
    }
    updateHeadingDisplay();
  }

  function setStartFromPlace(id, options = {}) {
    const place = findPlace(id);
    if (!place) return false;
    if (state.navigation.active && !options.allowWhileNavigating) {
      toast('Stop navigation before changing the starting point.');
      el.fromSelect.value = state.selectedFrom || '';
      return false;
    }

    state.selectedFrom = place.id;
    state.startPoint = {x: Number(place.x), y: Number(place.y), id: place.id, name: place.name};
    el.fromSelect.value = place.id;
    placeYouMarker();

    if (state.selectedTo === place.id) {
      state.selectedTo = null;
      el.toSelect.value = '';
    }
    if (options.pan !== false && state.map) {
      state.map.panTo(pointToLatLng(place), {animate: true});
    }
    return true;
  }

  function beginStartPicking() {
    if (state.navigation.active) {
      toast('Stop navigation before changing the starting point.');
      return;
    }
    state.pickingStart = !state.pickingStart;
    el.pickStartBtn?.classList.toggle('active', state.pickingStart);
    if (state.pickingStart) {
      el.side.classList.remove('open');
      toast('Tap a place marker or anywhere on the map to choose the nearest starting place.');
    } else {
      toast('Start-point selection cancelled.');
    }
  }

  function finishStartPicking(placeId) {
    const place = findPlace(placeId);
    if (!place || !setStartFromPlace(place.id)) return;
    state.pickingStart = false;
    el.pickStartBtn?.classList.remove('active');
    clearRoute();
    el.side.classList.add('open');
    el.summaryTitle.textContent = `Starting at ${place.name}`;
    el.summaryText.textContent = 'Choose a destination, then press Directions or Start navigation.';
    toast(`Starting point changed to ${place.name}.`);
  }

  function nearestPlaceToPoint(point) {
    let best = null;
    state.places.forEach(place => {
      const distance = pixelDistance(point, place);
      if (!best || distance < best.distance) best = {place, distance};
    });
    return best?.place || null;
  }

  function selectPlace(id, shouldRoute) {
    const place = findPlace(id);
    if (!place) return;
    state.selectedTo = id;
    el.toSelect.value = id;
    el.summaryTitle.textContent = place.name;
    el.summaryText.textContent = place.description || 'Select this place and click Show route.';
    renderPlaces();
    renderPlaceMarkers();
    showPlacePopup(place);
    if (shouldRoute) showRoute(false);
  }

  function showPlacePopup(place) {
    state.map.closePopup();
    const html = `
      <div class="leaflet-place-popup">
        <h3>${escapeHtml(place.name)}</h3>
        <p>${escapeHtml(place.description || '')}</p>
        <div class="popup-actions">
          <button class="btn primary" data-action="route">Directions</button>
          <button class="btn" data-action="start">Start live navigation</button>
          <button class="btn" data-action="here">Start here</button>
        </div>
      </div>`;
    const popup = L.popup({closeButton: true, autoPan: true, className: 'guest-leaflet-popup'})
      .setLatLng(pointToLatLng(place))
      .setContent(html)
      .openOn(state.map);

    setTimeout(() => {
      const root = document.querySelector('.guest-leaflet-popup');
      if (!root) return;
      root.querySelector('[data-action="route"]')?.addEventListener('click', () => showRoute(true));
      root.querySelector('[data-action="start"]')?.addEventListener('click', startDirection);
      root.querySelector('[data-action="here"]')?.addEventListener('click', () => {
        setStartFromPlace(place.id);
        clearRoute();
        toast('Start point set to ' + place.name);
      });
    }, 0);
  }

  async function showRoute(fit = true) {
    const from = state.selectedFrom || state.startPoint?.id;
    const to = state.selectedTo;
    if (!from) {
      toast('Please choose where the guest is starting from.');
      return null;
    }
    if (!to) {
      toast('Please choose a destination.');
      return null;
    }

    const response = await fetch(appendQuery(configuredUrl('mapRouteUrl', '/api/guest-map/route'), `from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&mode=walk`), {headers: {'Accept': 'application/json'}});
    const payload = await response.json();
    if (!response.ok) {
      toast(payload.message || 'Route could not be calculated.');
      return null;
    }

    state.route = normalizeRoute(payload);
    state.navigation.remainingDistanceMeters = state.route.distance_meters;
    state.navigation.remainingPath = state.route.path.map(point => ({...point}));
    state.navigation.currentManeuvers = state.route.maneuvers || buildClientManeuvers(state.route.path, state.route.from.name, state.route.to.name);
    state.navigation.currentManeuverIndex = 0;
    drawRemainingRoute(state.route.path);
    renderSteps(payload.steps || state.navigation.currentManeuvers.map(item => item.instruction));
    renderGuidancePreview();
    updateRouteStats(state.route);
    el.summaryTitle.textContent = `Route to ${state.route.to.name}`;
    el.summaryText.textContent = `${state.route.distance_meters} m • ${state.route.walk_minutes} min walk from ${state.route.from.name}. At each junction, the phone heading selects the closest mapped branch and the blue route updates from the new road.`;
    el.bottomCard.style.display = 'block';
    el.bottomTitle.textContent = 'Route ready';
    el.bottomText.textContent = `${state.route.distance_meters} m remaining`;
    if (fit) fitToPoints(state.route.path);
    return state.route;
  }

  function normalizeRoute(payload) {
    const path = (payload.path || []).map(p => ({x: Number(p.x), y: Number(p.y)}));
    return {
      ...payload,
      from: {...payload.from, x: Number(payload.from.x), y: Number(payload.from.y)},
      to: {...payload.to, x: Number(payload.to.x), y: Number(payload.to.y)},
      path,
      maneuvers: (payload.maneuvers || []).map(item => ({
        ...item,
        distance_from_start_meters: Number(item.distance_from_start_meters || 0),
        distance_to_next_meters: Number(item.distance_to_next_meters || 0),
        bearing_after: Number.isFinite(Number(item.bearing_after)) ? Number(item.bearing_after) : null,
        point: item.point ? {x: Number(item.point.x), y: Number(item.point.y)} : null,
      })),
      distance_meters: Number(payload.distance_meters || Math.round(polylineLengthPx(path) * metersPerPixel())),
    };
  }

  function clearRoute() {
    state.route = null;
    state.layers.routes.clearLayers();
    el.steps.innerHTML = '';
    el.distanceStat.textContent = '—';
    el.walkStat.textContent = '—';
    el.buggyStat.textContent = '—';
    el.progressStat.textContent = '0%';
    el.movedStat.textContent = '0 m';
    el.bottomCard.style.display = 'none';
    el.navGuidance.classList.remove('preview');
  }

  function drawRemainingRoute(path) {
    state.layers.routes.clearLayers();
    if (!path || path.length < 2) return;
    L.polyline(pointsToLatLngs(path), {
      color: '#ffffff',
      weight: 14,
      opacity: 0.96,
      lineCap: 'round',
      lineJoin: 'round',
      interactive: false,
      className: 'remaining-route-glow',
    }).addTo(state.layers.routes);
    L.polyline(pointsToLatLngs(path), {
      color: '#2563eb',
      weight: 7,
      opacity: 0.98,
      lineCap: 'round',
      lineJoin: 'round',
      interactive: false,
      className: 'remaining-route-line',
    }).addTo(state.layers.routes);
  }

  function renderSteps(steps) {
    el.steps.innerHTML = steps.map((step, index) => `<li class="step"><i>${index + 1}</i><span>${escapeHtml(step)}</span></li>`).join('');
  }

  function updateRouteStats(route) {
    el.distanceStat.textContent = `${Math.round(route.distance_meters)} m`;
    el.walkStat.textContent = `${route.walk_minutes || Math.max(1, Math.round(route.distance_meters / 75))} min`;
    el.buggyStat.textContent = `${route.buggy_minutes || Math.max(1, Math.round(route.distance_meters / 180))} min`;
  }

  async function startDirection() {
    if (state.navigation.active) {
      toast('Navigation is already running.');
      return;
    }

    if (!window.isSecureContext && !isLikelyDesktop()) {
      el.gpsStat.textContent = 'HTTPS required';
      el.sensorStat.textContent = 'Blocked by browser';
      el.summaryTitle.textContent = 'Open this map through HTTPS';
      el.summaryText.textContent = 'Chrome only exposes phone motion and direction sensors in a secure HTTPS page. HTTP on a local network will display the map but cannot track walking.';
      if (el.trackingModeBadge) el.trackingModeBadge.textContent = 'HTTPS required for mobile sensors';
      toast('Mobile movement requires HTTPS.');
      return;
    }

    resetNavigationRuntime();
    const provisionalFrom = findPlace(state.selectedFrom);
    const provisionalTo = findPlace(state.selectedTo);
    const provisionalBearing = state.route
      ? firstPathBearing(state.route.path)
      : (provisionalFrom && provisionalTo ? bearingBetween(provisionalFrom, provisionalTo) : 0);

    // Sensor permission must be requested directly from this button click on iPhone/iPad.
    const sensorPromise = startDeviceSensors(provisionalBearing || 0);
    const route = state.route || await showRoute(true);
    if (!route) {
      stopDeviceSensors();
      return;
    }

    const selectedStart = {x: Number(route.from.x), y: Number(route.from.y)};
    const initialBearing = firstPathBearing(route.path) ?? bearingBetween(selectedStart, route.to) ?? 0;
    state.navigation.active = true;
    requestWakeLock();
    state.navigation.followMode = true;
    state.navigation.anchorMapPoint = {...selectedStart};
    state.navigation.mapPoint = {...selectedStart};
    state.navigation.lastAcceptedPoint = {...selectedStart};
    state.navigation.remainingDistanceMeters = route.distance_meters;
    state.navigation.remainingPath = route.path.map(point => ({...point}));
    state.navigation.currentRoad = nearestRoadProjection(selectedStart, null, Infinity, {movementBearing: initialBearing});
    state.navigation.routeBearing = initialBearing;
    state.navigation.currentManeuvers = route.maneuvers?.length ? route.maneuvers : buildClientManeuvers(route.path, route.from.name, route.to.name);
    state.startPoint = {x: selectedStart.x, y: selectedStart.y, name: route.from.name, id: route.from.id};
    initializeGraphWalker(initialBearing);
    placeYouMarker();
    setNavigationButtons(true);
    el.navGuidance.classList.remove('preview');
    el.side.classList.remove('open');
    el.gpsStat.textContent = 'Starting';
    el.summaryTitle.textContent = 'Starting live movement';
    el.summaryText.textContent = `The marker starts at ${route.from.name}. At every junction, your phone direction selects the matching mapped road.`;
    drawRemainingRoute(route.path);
    updateGuidance(route.path, 'navigation_start');
    state.map.setView(pointToLatLng(selectedStart), Math.max(state.map.getZoom(), 0.75), {animate: true});

    const sensorResult = await sensorPromise;
    state.navigation.motionEngine?.recalibrate(initialBearing);
    updateSensorStatus();

    if (state.navigation.motionListenerActive) {
      el.gpsStat.textContent = 'Motion';
      el.summaryTitle.textContent = 'Live movement ready';
      el.summaryText.textContent = 'Walk normally while holding the phone forward. Steps move on the mapped road and your heading chooses the road at every junction.';
      toast('Live device movement is ready. Walk with the phone facing forward.');
      state.navigation.sensorWatchdogTimer = setTimeout(() => {
        if (!state.navigation.active || state.navigation.motionDataReceived) return;
        el.summaryTitle.textContent = 'Waiting for phone motion data';
        el.summaryText.textContent = window.isSecureContext
          ? 'Move the phone and take a few steps. If nothing changes, allow Motion & Orientation access in the browser settings.'
          : 'Phone motion sensors require HTTPS. Open this project through an HTTPS address and start navigation again.';
        if (el.trackingModeBadge) el.trackingModeBadge.textContent = window.isSecureContext ? 'Waiting for motion data' : 'HTTPS required';
      }, 2200);
      return;
    }

    // GPS is only a fallback when DeviceMotion is unavailable or denied.
    if (navigator.geolocation) {
      state.navigation.watchId = navigator.geolocation.watchPosition(
        onGpsPosition,
        onGpsError,
        {enableHighAccuracy: true, maximumAge: 500, timeout: 15000}
      );
      el.gpsStat.textContent = 'GPS fallback';
      el.summaryText.textContent = 'Device motion was unavailable, so browser location is being used as a fallback.';
      return;
    }

    el.gpsStat.textContent = 'No motion';
    el.summaryTitle.textContent = 'Movement sensor unavailable';
    el.summaryText.textContent = sensorResult?.motionPermission === 'denied'
      ? 'Motion permission was denied. Reload, press Start live navigation, and allow Motion & Orientation access.'
      : 'This browser does not expose DeviceMotion. Use HTTPS on a phone, or use Simulate movement for testing.';
    toast('Motion sensor unavailable. Allow Motion & Orientation access and use HTTPS.');
  }

  function onGpsPosition(position) {
    if (!state.navigation.active || !state.route) return;
    const c = position.coords;
    const geo = {
      lat: c.latitude,
      lng: c.longitude,
      accuracy: c.accuracy,
      altitude: c.altitude,
      heading: c.heading,
      speed: c.speed,
      at: Date.now(),
    };
    state.navigation.lastAccuracy = Number.isFinite(c.accuracy) ? c.accuracy : null;
    applyHeadingFromGeo(geo);

    const calibratedPoint = absoluteGeoToMapPoint(geo);
    if (calibratedPoint) {
      const isFirstAbsoluteSample = !state.navigation.lastGeo;
      const movedMeters = state.navigation.lastGeo ? haversineMeters(state.navigation.lastGeo, geo) : Infinity;
      state.navigation.lastGeo = geo;
      if (Number.isFinite(movedMeters) && movedMeters < state.navigation.minGpsMoveMeters) {
        updateLiveText('gps_waiting', 'GPS received; waiting for meaningful movement.');
        return;
      }
      if (Number.isFinite(geo.accuracy) && geo.accuracy > 80) {
        updateLiveText('coarse_gps_waiting', 'GPS accuracy is too low; holding the last road position.');
        return;
      }
      const point = smoothMapPoint(calibratedPoint, geo.accuracy);
      const previous = state.navigation.lastRawPoint || point;
      state.navigation.lastRawPoint = point;
      const travelBearing = bearingBetween(previous, point);
      if (Number.isFinite(travelBearing) && movedMeters >= 2) {
        state.navigation.heading = travelBearing;
        state.navigation.headingSource = 'gps_travel_direction';
      }
      applyRoadConstrainedPoint(point, geo, isFirstAbsoluteSample ? 'gps_absolute_start' : 'absolute_geolocation');
      return;
    }

    if (!state.navigation.anchorGeo) {
      state.navigation.anchorGeo = geo;
      state.navigation.lastGeo = geo;
      state.navigation.lastRawPoint = {...state.navigation.anchorMapPoint};
      state.navigation.smoothedRawPoint = {...state.navigation.anchorMapPoint};
      applyRoadConstrainedPoint({...state.navigation.anchorMapPoint}, geo, 'gps_anchor');
      toast(`GPS anchor received. Your phone location is being treated as ${state.route.from.name}.`);
      return;
    }

    const movedMeters = haversineMeters(state.navigation.lastGeo, geo);
    state.navigation.lastGeo = geo;
    if (Number.isFinite(movedMeters) && movedMeters < state.navigation.minGpsMoveMeters) {
      updateLiveText('gps_waiting', 'GPS received; waiting for meaningful movement.');
      return;
    }
    if (Number.isFinite(geo.accuracy) && geo.accuracy > 70) {
      updateLiveText('coarse_gps_waiting', 'GPS accuracy is too low; holding the last road position.');
      return;
    }

    const raw = geoToRelativeMapPoint(geo);
    const point = smoothMapPoint(raw.point, geo.accuracy);
    if (Number.isFinite(raw.bearing)) {
      state.navigation.heading = raw.bearing;
      state.navigation.headingSource = 'gps_travel_direction';
    }
    applyRoadConstrainedPoint(point, geo, 'anchored_geolocation');
  }

  function onGpsError(error) {
    console.warn(error);
    if (state.navigation.motionListenerActive) {
      el.gpsStat.textContent = 'Motion';
      return;
    }
    el.gpsStat.textContent = 'Blocked';
    updateLiveText('gps_error', 'GPS fallback is blocked. Allow Location or enable Motion & Orientation access.');
    toast('Tracking permission is blocked.');
  }

  function geoToRelativeMapPoint(geo) {
    const offset = geoOffsetMeters(state.navigation.anchorGeo, geo);
    const metersPerPx = metersPerPixel();
    const baseX = offset.east / metersPerPx;
    const baseY = -offset.north / metersPerPx;
    const rad = Number(state.navigation.mapNorthRotationDeg || 0) * Math.PI / 180;
    const x = baseX * Math.cos(rad) - baseY * Math.sin(rad);
    const y = baseX * Math.sin(rad) + baseY * Math.cos(rad);
    const point = {
      x: clamp(Number(state.navigation.anchorMapPoint.x) + x, 0, Number(state.data.map.width)),
      y: clamp(Number(state.navigation.anchorMapPoint.y) + y, 0, Number(state.data.map.height)),
    };
    const previous = state.navigation.lastRawPoint || state.navigation.anchorMapPoint;
    state.navigation.lastRawPoint = point;
    return {point, bearing: bearingBetween(previous, point)};
  }

  function applyRoadConstrainedPoint(rawPoint, geo, source, options = {}) {
    const previous = state.navigation.lastAcceptedPoint || state.route.from;
    const accuracyMeters = Number.isFinite(Number(geo?.accuracy)) ? Number(geo.accuracy) : 12;
    const dynamicSnapPx = clamp(Math.max(state.navigation.snapPx, accuracyMeters / metersPerPixel() * 1.25), 18, 80);
    const projection = options.projectionOverride || nearestRoadProjection(rawPoint, previous, dynamicSnapPx, options);

    if (!projection && !options.exactGraphPoint) {
      state.navigation.offRouteSamples += 1;
      state.navigation.blockedReason = 'No valid road under movement';
      setRerouteStatus(state.navigation.offRouteSamples >= 3 ? 'off-route' : 'searching');
      updateLiveText(source, 'Location is away from the mapped resort paths. Holding the last valid road position.');
      flashMarkerBlocked();
      return;
    }

    const jumpPx = projection ? pixelDistance(previous, projection.point) : pixelDistance(previous, rawPoint);
    const rawMovePx = pixelDistance(previous, rawPoint);
    const maxJump = Math.max(24, Math.min(state.navigation.movementLimitPx + dynamicSnapPx * 0.35, rawMovePx + 20));
    if (jumpPx > maxJump && !options.exactGraphPoint && source !== 'simulated' && !source.startsWith('keyboard_') && source !== 'gps_absolute_start') {
      state.navigation.offRouteSamples += 1;
      state.navigation.blockedReason = 'Road jump rejected';
      setRerouteStatus('searching');
      updateLiveText(source, 'Possible GPS jump detected. Keeping the last trusted road position.');
      flashMarkerBlocked();
      return;
    }

    const wasRoad = state.navigation.currentRoad;
    const changedRoad = !!(wasRoad && projection?.edge && String(wasRoad.edge.id) !== String(projection.edge.id));
    state.navigation.offRouteSamples = 0;
    state.navigation.blockedReason = null;
    const accepted = (options.routeLocked || options.exactGraphPoint) ? {x: Number(rawPoint.x), y: Number(rawPoint.y)} : projection.point;
    const deltaMeters = pixelDistance(previous, accepted) * metersPerPixel();
    if (deltaMeters > 0.2 && deltaMeters < 80) state.navigation.totalMovedMeters += deltaMeters;
    state.navigation.lastAcceptedPoint = accepted;
    state.navigation.mapPoint = accepted;
    if (projection) state.navigation.currentRoad = projection;
    if (Number.isFinite(projection?.bearing)) state.navigation.routeBearing = projection.bearing;

    state.startPoint = {x: accepted.x, y: accepted.y, id: null, name: 'Live road position'};
    placeYouMarker();

    const previousRemainingMeters = state.navigation.remainingDistanceMeters;
    let remaining = null;
    if (Array.isArray(options.expectedRemainingPath) && options.expectedRemainingPath.length) {
      const lockedPath = simplifyDuplicatePoints([
        accepted,
        ...options.expectedRemainingPath.slice(1),
      ]);
      remaining = {
        path: lockedPath,
        distanceMeters: Number.isFinite(Number(options.expectedRemainingMeters))
          ? Math.max(0, Number(options.expectedRemainingMeters))
          : polylineLengthPx(lockedPath) * metersPerPixel(),
        signature: options.expectedRemainingSignature || `graph-walk:${routeSignatureForPath(lockedPath)}`,
      };
    } else {
      remaining = calculateRemainingRouteFromProjection(projection);
    }
    if (remaining && remaining.path.length >= 1) {
      state.navigation.routeBearing = remaining.path.length >= 2
        ? (firstPathBearing(remaining.path) ?? projection?.bearing ?? state.navigation.routeBearing)
        : (projection?.bearing ?? state.navigation.routeBearing);
      state.navigation.remainingDistanceMeters = options.routeLocked && !options.allowRemainingIncrease && Number.isFinite(previousRemainingMeters)
        ? Math.min(previousRemainingMeters, remaining.distanceMeters)
        : remaining.distanceMeters;
      state.navigation.remainingPath = remaining.path.map(point => ({...point}));
      state.navigation.progressPercent = calculateProgressPercent(state.navigation.remainingDistanceMeters);
      drawRemainingRoute(remaining.path);

      const now = Date.now();
      const routeChanged = remaining.signature !== state.navigation.routeSignature;
      if (routeChanged || now - state.navigation.lastRouteRefreshAt > 2500) {
        state.navigation.routeSignature = remaining.signature;
        state.navigation.lastRouteRefreshAt = now;
        state.navigation.currentManeuvers = buildClientManeuvers(remaining.path, 'your location', state.route.to.name);
        const meaningfulDetour = changedRoad && Number.isFinite(previousRemainingMeters)
          && remaining.distanceMeters > previousRemainingMeters + Math.max(12, previousRemainingMeters * 0.08);
        if (meaningfulDetour) {
          state.navigation.rerouteCount += 1;
          setRerouteStatus('rerouting');
        } else {
          setRerouteStatus('on-route');
        }
      } else {
        setRerouteStatus('on-route');
      }
      updateGuidance(remaining.path, source);
    }

    updateHeadingDisplay();
    if (state.navigation.active && state.navigation.followMode) {
      state.map.panTo(pointToLatLng(accepted), {animate: true, duration: 0.28});
    }
    maybeStoreLocation(geo, accepted, state.navigation.totalMovedMeters, state.navigation.progressPercent, source);
    updateInstruction(source, projection);

    const arrivalRadiusPx = Math.max(2, 3 / metersPerPixel());
    if (pixelDistance(accepted, state.route.to) <= arrivalRadiusPx || state.navigation.remainingDistanceMeters <= 3) {
      toast('You have arrived.');
      speakInstruction(`You have arrived at ${state.route.to.name}.`, 'arrived');
      stopDirection(true);
    }
  }

  function nearestRoadProjection(rawPoint, previousPoint = null, maxDistance = Infinity, options = {}) {
    let best = null;
    const currentEdge = state.navigation.currentRoad?.edge;
    const heading = Number.isFinite(options.movementBearing)
      ? Number(options.movementBearing)
      : (Number.isFinite(state.navigation.heading) ? state.navigation.heading : null);
    for (const edge of state.graph.edges) {
      const path = edge.path || [];
      for (let i = 1; i < path.length; i++) {
        const a = path[i - 1];
        const b = path[i];
        const proj = projectPointToSegment(rawPoint, a, b);
        if (proj.distancePx > maxDistance) continue;
        const distFromPrev = previousPoint ? pixelDistance(previousPoint, proj.point) : 0;
        const segmentBearing = bearingBetween(a, b);
        const reverseBearing = Number.isFinite(segmentBearing) ? normalizeDegrees(segmentBearing + 180) : null;
        const headingPenalty = Number.isFinite(heading) && Number.isFinite(segmentBearing)
          ? Math.min(angleDifference(heading, segmentBearing), angleDifference(heading, reverseBearing)) * (options.routeLocked ? 0.28 : 0.12)
          : 0;
        const sameEdgeBonus = currentEdge && String(currentEdge.id) === String(edge.id) ? (options.routeLocked ? -2 : -22) : 0;
        const connectedBonus = currentEdge && (currentEdge.from === edge.from || currentEdge.from === edge.to || currentEdge.to === edge.from || currentEdge.to === edge.to) ? -8 : 0;
        const score = proj.distancePx * (options.routeLocked ? 10 : 7) + distFromPrev * 1.15 + headingPenalty + sameEdgeBonus + connectedBonus;
        if (!best || score < best.score) {
          best = {
            edge,
            segmentIndex: i - 1,
            point: proj.point,
            t: proj.t,
            distancePx: proj.distancePx,
            bearing: segmentBearing,
            score,
          };
        }
      }
    }
    return best;
  }

  function calculateRemainingRouteFromProjection(projection) {
    if (!projection || !state.route) return null;
    const destNode = findPlace(state.route.to.id)?.route_node_code;
    if (!destNode) {
      const path = [projection.point, state.route.to];
      return {path, distanceMeters: pixelDistance(projection.point, state.route.to) * metersPerPixel(), signature: routeSignatureForPath(path)};
    }

    const pathToFrom = partialEdgePath(projection.edge.path, projection.segmentIndex, projection.t, true);
    const pathToTo = partialEdgePath(projection.edge.path, projection.segmentIndex, projection.t, false);
    const optionA = buildNetworkPathFromNode(projection.edge.from, destNode);
    const optionB = buildNetworkPathFromNode(projection.edge.to, destNode);

    const candidateA = optionA ? joinPaths([pathToFrom, optionA.path]) : null;
    const candidateB = optionB ? joinPaths([pathToTo, optionB.path]) : null;
    const distA = candidateA ? polylineLengthPx(candidateA) * metersPerPixel() : Infinity;
    const distB = candidateB ? polylineLengthPx(candidateB) * metersPerPixel() : Infinity;
    const useA = distA <= distB;
    const chosenPath = useA ? candidateA : candidateB;
    const chosenDistance = useA ? distA : distB;

    if (!chosenPath) return null;
    const cleanPath = simplifyDuplicatePoints(chosenPath);
    return {
      path: cleanPath,
      distanceMeters: chosenDistance,
      signature: `${projection.edge.id}:${useA ? projection.edge.from : projection.edge.to}:${destNode}:${routeSignatureForPath(cleanPath)}`,
    };
  }

  function partialEdgePath(path, segmentIndex, t, towardFrom) {
    const a = path[segmentIndex];
    const b = path[segmentIndex + 1];
    const point = {x: Number(a.x) + (Number(b.x) - Number(a.x)) * t, y: Number(a.y) + (Number(b.y) - Number(a.y)) * t};
    if (towardFrom) {
      return [point, ...path.slice(0, segmentIndex + 1).reverse()];
    }
    return [point, ...path.slice(segmentIndex + 1)];
  }

  function buildNetworkPathFromNode(startCode, endCode) {
    if (!startCode || !endCode) return null;
    if (startCode === endCode) return {path: [nodePoint(startCode)], distanceMeters: 0};
    const q = new Set(Object.keys(state.graph.nodes));
    const dist = {};
    const prev = {};
    Object.keys(state.graph.nodes).forEach(code => { dist[code] = Infinity; prev[code] = null; });
    dist[startCode] = 0;

    while (q.size) {
      let current = null;
      let best = Infinity;
      q.forEach(code => {
        if (dist[code] < best) { best = dist[code]; current = code; }
      });
      if (!current) break;
      q.delete(current);
      if (current === endCode) break;
      (state.graph.adj[current] || []).forEach(edge => {
        if (!q.has(edge.to)) return;
        const nd = dist[current] + Number(edge.distanceMeters || polylineLengthPx(edge.path) * metersPerPixel());
        if (nd < dist[edge.to]) { dist[edge.to] = nd; prev[edge.to] = edge; }
      });
    }

    if (!prev[endCode]) return null;
    const edges = [];
    let cur = endCode;
    while (cur !== startCode) {
      const edge = prev[cur];
      if (!edge) return null;
      edges.unshift(edge);
      cur = edge.from;
    }
    let path = [];
    edges.forEach(edge => { path = joinPaths([path, edge.path]); });
    return {path: simplifyDuplicatePoints(path), distanceMeters: dist[endCode], edges, firstEdgeId: edges[0]?.id ?? null};
  }

  function calculateProgressPercent(remainingMeters) {
    const total = Number(state.route.distance_meters || 0);
    if (total <= 0) return 0;
    return clamp(((total - remainingMeters) / total) * 100, 0, 100);
  }

  function updateInstruction(source, projection) {
    const remaining = Math.max(0, Math.round(state.navigation.remainingDistanceMeters || 0));
    const moved = Math.round(state.navigation.totalMovedMeters || 0);
    const progress = Math.round(state.navigation.progressPercent || 0);
    const accuracy = Number.isFinite(state.navigation.lastAccuracy) ? `${Math.round(state.navigation.lastAccuracy)} m` : source;
    el.gpsStat.textContent = source.startsWith('device_motion') ? 'Motion' : source === 'gps_anchor' ? 'Anchor' : source.startsWith('keyboard_') ? 'Key' : source === 'simulated' ? 'Sim' : accuracy;
    el.progressStat.textContent = `${progress}%`;
    el.movedStat.textContent = `${moved} m`;
    updateHeadingDisplay();

    const roadText = projection ? `Following ${formatBearing(projection.bearing)}.` : '';
    el.summaryTitle.textContent = 'Navigation running';
    el.summaryText.textContent = `${remaining} m remaining • ${progress}% complete. ${roadText}`;
    el.bottomTitle.textContent = 'Live directions';
    el.bottomText.textContent = `${formatDistance(remaining)} remaining • ${Math.max(1, Math.ceil(remaining / Number(state.data.map.walk_meters_per_minute || 75)))} min`;
  }

  function updateLiveText(source, message) {
    el.gpsStat.textContent = source === 'coarse_gps_waiting' ? 'Coarse' : source === 'gps_waiting' ? 'Waiting' : source;
    el.summaryTitle.textContent = 'Navigation holding';
    el.summaryText.textContent = message;
  }

  function flashMarkerBlocked() {
    const node = state.youMarker?.getElement();
    if (!node) return;
    node.classList.add('blocked');
    setTimeout(() => node.classList.remove('blocked'), 260);
  }

  function simulateMovement() {
    if (state.navigation.active) {
      toast('Stop current navigation first.');
      return;
    }
    Promise.resolve(state.route || showRoute(true)).then(route => {
      if (!route) return;
      resetNavigationRuntime();
      state.navigation.active = true;
      state.navigation.anchorMapPoint = {...route.from};
      state.navigation.mapPoint = {...route.from};
      state.navigation.lastAcceptedPoint = {...route.from};
      setNavigationButtons(true);
      const path = route.path;
      let ratio = 0;
      state.navigation.simulateTimer = setInterval(() => {
        ratio = Math.min(1, ratio + 0.018);
        const point = pointAlongPath(path, ratio);
        applyRoadConstrainedPoint(point, null, 'simulated');
        if (ratio >= 1) stopDirection(true);
      }, 550);
    });
  }

  function nudgeByKeyboard(directionDegOffset) {
    if (!state.navigation.active || !state.route) {
      toast('Start live navigation first. Keyboard is only for desktop road testing.');
      return;
    }
    const baseHeading = Number.isFinite(state.navigation.heading)
      ? state.navigation.heading
      : (Number.isFinite(state.navigation.routeBearing) ? state.navigation.routeBearing : 0);
    if (directionDegOffset !== 0) {
      state.navigation.heading = normalizeDegrees(baseHeading + directionDegOffset);
      state.navigation.headingSource = 'keyboard_heading';
      state.navigation.headingHistory.push({heading: state.navigation.heading, at: Date.now()});
      state.navigation.headingHistory = state.navigation.headingHistory.slice(-NAV_CONFIG.headingHistoryMax);
      updateHeadingDisplay();
      return;
    }
    advanceByGraphHeading(state.navigation.keyboardStepMeters, 'keyboard_forward_graph');
  }

  function stopDirection(arrived = false) {
    if (state.navigation.watchId !== null) navigator.geolocation.clearWatch(state.navigation.watchId);
    if (state.navigation.simulateTimer !== null) clearInterval(state.navigation.simulateTimer);
    if (state.navigation.sensorWatchdogTimer !== null) clearTimeout(state.navigation.sensorWatchdogTimer);
    stopDeviceSensors();
    releaseWakeLock();
    const routeLogId = state.route?.route_log_id;
    state.navigation.active = false;
    state.navigation.watchId = null;
    state.navigation.simulateTimer = null;
    setNavigationButtons(false);
    if (routeLogId) finishNavigation(routeLogId, arrived ? 'arrived' : 'stopped');
    if (!arrived) {
      toast('Navigation stopped.');
      el.gpsStat.textContent = 'Off';
      renderGuidancePreview();
    } else {
      el.maneuverIcon.textContent = '✓';
      el.maneuverDistance.textContent = 'Arrived';
      el.maneuverText.textContent = state.route?.to?.name || 'Destination';
      el.maneuverSubtext.textContent = 'Navigation complete';
      el.navGuidance.classList.add('preview');
    }
  }

  function resetNavigationRuntime() {
    if (state.navigation.watchId !== null) navigator.geolocation.clearWatch(state.navigation.watchId);
    if (state.navigation.simulateTimer !== null) clearInterval(state.navigation.simulateTimer);
    if (state.navigation.sensorWatchdogTimer !== null) clearTimeout(state.navigation.sensorWatchdogTimer);
    stopDeviceSensors();
    releaseWakeLock();
    const geoCalibration = state.navigation.geoCalibration;
    const motionEngine = state.navigation.motionEngine;
    const voiceEnabled = state.navigation.voiceEnabled;
    Object.assign(state.navigation, {
      active: false,
      watchId: null,
      simulateTimer: null,
      anchorGeo: null,
      anchorMapPoint: null,
      lastGeo: null,
      lastRawPoint: null,
      smoothedRawPoint: null,
      mapPoint: null,
      lastAcceptedPoint: null,
      routeBearing: null,
      heading: null,
      headingSource: null,
      motionEngine,
      graphWalker: null,
      headingHistory: [],
      lastBranchMessage: null,
      motionListenerActive: false,
      orientationListenerActive: false,
      motionPermission: 'unknown',
      orientationPermission: 'unknown',
      stepCount: 0,
      lastMotionStepAt: 0,
      lastSensorAt: 0,
      motionDataReceived: false,
      orientationDataReceived: false,
      sensorWatchdogTimer: null,
      wakeLock: null,
      lastAccuracy: null,
      lastSentAt: 0,
      totalMovedMeters: 0,
      remainingDistanceMeters: state.route?.distance_meters || 0,
      remainingPath: state.route?.path ? state.route.path.map(point => ({...point})) : [],
      progressPercent: 0,
      currentRoad: null,
      blockedReason: null,
      source: 'off',
      followMode: true,
      voiceEnabled,
      lastSpokenKey: null,
      currentManeuvers: state.route?.maneuvers || [],
      currentManeuverIndex: 0,
      routeSignature: null,
      lastRouteRefreshAt: 0,
      offRouteSamples: 0,
      rerouteCount: 0,
      geoCalibration,
    });
  }

  function setupMotionEngine() {
    if (state.navigation.motionEngine || typeof window.ResortMotionEngine !== 'function') return;
    state.navigation.motionEngine = new window.ResortMotionEngine({
      defaultStepLengthMeters: 0.72,
      onHeading: onSensorHeading,
      onStep: onSensorStep,
      onMotion: onSensorMotion,
      onStatus: onSensorStatus,
    });
  }

  async function startDeviceSensors(initialMapHeading) {
    setupMotionEngine();
    if (!state.navigation.motionEngine) {
      state.navigation.motionPermission = 'unsupported';
      state.navigation.orientationPermission = 'unsupported';
      return {motionPermission: 'unsupported', orientationPermission: 'unsupported'};
    }
    const result = await state.navigation.motionEngine.start(initialMapHeading || 0);
    state.navigation.motionListenerActive = state.navigation.motionEngine.motionActive;
    state.navigation.orientationListenerActive = state.navigation.motionEngine.orientationActive;
    state.navigation.motionPermission = result.motionPermission;
    state.navigation.orientationPermission = result.orientationPermission;
    updateSensorStatus();
    return result;
  }

  function stopDeviceSensors() {
    state.navigation.motionEngine?.stop();
    state.navigation.motionListenerActive = false;
    state.navigation.orientationListenerActive = false;
  }

  function onSensorStatus(status) {
    state.navigation.motionListenerActive = !!status.motion;
    state.navigation.orientationListenerActive = !!status.orientation;
    state.navigation.motionPermission = status.motionPermission || state.navigation.motionPermission;
    state.navigation.orientationPermission = status.orientationPermission || state.navigation.orientationPermission;
    updateSensorStatus();
  }

  function onSensorHeading(payload) {
    if (!Number.isFinite(payload?.heading)) return;
    clearSensorWatchdog();
    state.navigation.heading = normalizeDegrees(payload.heading);
    state.navigation.headingSource = payload.source || 'device_orientation';
    state.navigation.lastSensorAt = payload.at || Date.now();
    state.navigation.headingHistory.push({heading: state.navigation.heading, at: state.navigation.lastSensorAt});
    state.navigation.headingHistory = state.navigation.headingHistory.slice(-NAV_CONFIG.headingHistoryMax);
    state.navigation.orientationDataReceived = true;
    updateHeadingDisplay();
    updateSensorStatus();
  }

  function onSensorMotion(payload) {
    clearSensorWatchdog();
    state.navigation.motionDataReceived = true;
    state.navigation.lastSensorAt = payload?.at || Date.now();
    updateSensorStatus();
  }

  function onSensorStep(payload) {
    if (!state.navigation.active || !state.route) return;
    state.navigation.stepCount = Number(payload.count || state.navigation.stepCount + 1);
    state.navigation.lastMotionStepAt = payload.at || Date.now();
    state.navigation.lastSensorAt = payload.at || Date.now();
    if (Number.isFinite(payload.heading)) {
      state.navigation.heading = normalizeDegrees(payload.heading);
      state.navigation.headingSource = 'device_motion_heading';
      state.navigation.headingHistory.push({heading: state.navigation.heading, at: state.navigation.lastSensorAt});
      state.navigation.headingHistory = state.navigation.headingHistory.slice(-NAV_CONFIG.headingHistoryMax);
    }
    advanceByDeviceMotion(Number(payload.stepLengthMeters || 0.72));
    if (el.trackingModeBadge) el.trackingModeBadge.textContent = `Live device motion • ${state.navigation.stepCount} step${state.navigation.stepCount === 1 ? '' : 's'}`;
  }

  function advanceByDeviceMotion(distanceMeters) {
    advanceByGraphHeading(distanceMeters, 'device_motion_graph');
  }

  function initializeGraphWalker(initialHeading) {
    if (typeof window.ResortGraphWalker !== 'function' || !state.graph) return null;
    state.navigation.graphWalker = new window.ResortGraphWalker({
      nodes: state.graph.nodes,
      edges: state.graph.edges,
      metersPerPixel: metersPerPixel(),
      routeBiasDegrees: NAV_CONFIG.routeBiasDegrees,
      branchLockMeters: 3.2,
      reversePenaltyDegrees: 44,
      reverseAllowanceDegrees: 38,
      reverseSwitchMarginDegrees: 30,
    });
    const startPlace = findPlace(state.route?.from?.id || state.selectedFrom);
    const startPoint = state.route?.from || state.startPoint || startPlace;
    state.navigation.graphWalker.setPosition(
      startPoint,
      initialHeading,
      startPlace?.route_node_code || null
    );
    state.navigation.headingHistory = [];
    return state.navigation.graphWalker;
  }

  function stableDeviceHeading() {
    const now = Date.now();
    const history = (state.navigation.headingHistory || []).filter(sample => now - sample.at <= NAV_CONFIG.headingHistoryMs);
    state.navigation.headingHistory = history.slice(-NAV_CONFIG.headingHistoryMax);
    if (!history.length) return Number.isFinite(state.navigation.heading) ? state.navigation.heading : state.navigation.routeBearing;
    let x = 0;
    let y = 0;
    history.forEach((sample, index) => {
      const rad = Number(sample.heading) * Math.PI / 180;
      const weight = index + 1;
      x += Math.cos(rad) * weight;
      y += Math.sin(rad) * weight;
    });
    if (Math.hypot(x, y) < 0.01) return history[history.length - 1].heading;
    return normalizeDegrees(Math.atan2(y, x) * 180 / Math.PI);
  }

  function preferredEdgeIdFromNode(nodeCode) {
    const destinationNode = findPlace(state.route?.to?.id)?.route_node_code;
    if (!nodeCode || !destinationNode) return null;
    return buildNetworkPathFromNode(nodeCode, destinationNode)?.firstEdgeId ?? null;
  }

  function calculateRemainingRouteFromWalker(result) {
    if (!result || !state.route) return null;
    const destinationNode = findPlace(state.route.to.id)?.route_node_code;
    if (!destinationNode) return null;
    if (result.kind === 'node') {
      const network = buildNetworkPathFromNode(result.nodeCode, destinationNode);
      if (!network) return null;
      const path = simplifyDuplicatePoints([result.point, ...(network.path || [])]);
      return {
        path,
        distanceMeters: polylineLengthPx(path) * metersPerPixel(),
        signature: `node:${result.nodeCode}:${destinationNode}:${routeSignatureForPath(path)}`,
      };
    }
    return calculateRemainingRouteFromProjection(result.projection);
  }

  function advanceByGraphHeading(distanceMeters, source = 'graph_walk') {
    const walker = state.navigation.graphWalker || initializeGraphWalker(state.navigation.routeBearing || 0);
    if (!walker || !state.route) return;
    const heading = stableDeviceHeading();
    const before = walker.snapshot();
    const preferredEdge = before?.kind === 'node' ? preferredEdgeIdFromNode(before.nodeCode) : null;
    const result = walker.step(clamp(distanceMeters, 0.35, 1.15), heading, preferredEdge);
    if (!result || !result.point || result.traveledMeters <= 0) return;

    const remaining = calculateRemainingRouteFromWalker(result);
    const projection = result.projection || nearestRoadProjection(result.point, state.navigation.lastAcceptedPoint, Infinity, {movementBearing: result.bearing});
    if (Number.isFinite(result.bearing)) state.navigation.routeBearing = result.bearing;

    applyRoadConstrainedPoint(result.point, null, source, {
      exactGraphPoint: true,
      projectionOverride: projection,
      movementBearing: result.bearing,
      expectedRemainingPath: remaining?.path,
      expectedRemainingMeters: remaining?.distanceMeters,
      expectedRemainingSignature: remaining?.signature,
      allowRemainingIncrease: true,
    });

    if (result.selectedBranch) {
      const key = `${result.selectedBranch.fromNode}:${result.selectedBranch.edgeId}:${result.selectedBranch.toNode}`;
      if (state.navigation.lastBranchMessage !== key) {
        state.navigation.lastBranchMessage = key;
        const direction = formatBearing(result.selectedBranch.bearing);
        if (el.rerouteBadge) el.rerouteBadge.textContent = `Junction: ${direction}`;
      }
    }

    const markerElement = state.youMarker?.getElement();
    markerElement?.classList.add('sensor-step');
    setTimeout(() => markerElement?.classList.remove('sensor-step'), 130);
  }

  // Kept for desktop forward-key compatibility. It now uses the same
  // heading-aware graph walker as real phone steps.
  function advanceAlongActiveRoute(distanceMeters, source = 'route_follow') {
    advanceByGraphHeading(distanceMeters, source);
  }

  function recalibrateDeviceDirection() {
    if (!state.navigation.active) {
      toast('Start live navigation first.');
      return;
    }
    const targetBearing = Number.isFinite(state.navigation.routeBearing)
      ? state.navigation.routeBearing
      : firstPathBearing(state.route?.path || []) || 0;
    state.navigation.motionEngine?.recalibrate(targetBearing);
    state.navigation.heading = targetBearing;
    state.navigation.headingSource = 'manual_direction_alignment';
    updateHeadingDisplay();
    toast('Direction aligned. Point the phone forward and continue walking.');
  }

  function applyHeadingFromGeo(geo) {
    if (state.navigation.orientationListenerActive) return;
    if (!geo || !Number.isFinite(geo.heading)) return;
    state.navigation.heading = normalizeDegrees(geo.heading);
    state.navigation.headingSource = 'gps_heading';
    updateHeadingDisplay();
  }

  function updateHeadingDisplay() {
    const heading = state.navigation.heading;
    const fallback = state.navigation.routeBearing;
    if (Number.isFinite(heading)) el.headingStat.textContent = formatBearing(heading);
    else if (Number.isFinite(fallback)) el.headingStat.textContent = 'Next ' + formatBearing(fallback);
    else if (state.navigation.headingSource === 'blocked') el.headingStat.textContent = 'Blocked';
    else if (state.navigation.headingSource === 'waiting') el.headingStat.textContent = 'Waiting';
    else el.headingStat.textContent = '—';

    const markerElement = state.youMarker?.getElement();
    if (!markerElement) return;
    const bearing = Number.isFinite(heading) ? heading : fallback;
    if (Number.isFinite(bearing)) {
      markerElement.style.setProperty('--heading', normalizeDegrees(bearing) + 'deg');
      markerElement.classList.toggle('heading-known', Number.isFinite(heading));
      markerElement.classList.toggle('route-bearing-only', !Number.isFinite(heading));
    } else {
      markerElement.classList.remove('heading-known', 'route-bearing-only');
    }
  }

  function updateSensorStatus() {
    const hasGeo = !!navigator.geolocation;
    const hasOrientation = typeof window.DeviceOrientationEvent !== 'undefined';
    const hasMotion = typeof window.DeviceMotionEvent !== 'undefined';
    let label = 'No motion sensor';
    if (!window.isSecureContext && !isLikelyDesktop()) label = 'HTTPS required';
    else if (state.navigation.motionDataReceived && state.navigation.orientationDataReceived) label = 'Motion + direction live';
    else if (state.navigation.motionDataReceived) label = 'Motion live';
    else if (state.navigation.motionListenerActive && state.navigation.orientationListenerActive) label = 'Sensors connected';
    else if (state.navigation.motionListenerActive) label = 'Motion connected';
    else if (state.navigation.orientationListenerActive) label = 'Direction only';
    else if (!isLikelyDesktop() && hasMotion && hasOrientation) label = 'Phone sensors ready';
    else if (!isLikelyDesktop() && hasMotion) label = 'Motion ready';
    else if (hasGeo) label = 'GPS fallback';
    else if (isLikelyDesktop()) label = 'Desktop test';

    state.navigation.sensorStatus = label;
    el.sensorStat.textContent = label;
    if (el.trackingModeBadge) {
      if (state.navigation.motionDataReceived) {
        el.trackingModeBadge.textContent = `Live device motion • ${state.navigation.stepCount} steps`;
      } else if (state.navigation.motionListenerActive) {
        el.trackingModeBadge.textContent = 'Motion sensor connected';
      } else if (state.navigation.motionPermission === 'denied') {
        el.trackingModeBadge.textContent = 'Motion permission denied';
      } else if (state.navigation.orientationPermission === 'denied') {
        el.trackingModeBadge.textContent = 'Direction permission denied';
      } else {
        el.trackingModeBadge.textContent = !window.isSecureContext && !isLikelyDesktop()
          ? 'HTTPS required for mobile sensors'
          : (isAndroidChrome() && hasMotion ? 'Android Chrome sensors ready' : (hasMotion ? 'Device motion ready' : 'GPS/desktop fallback'));
      }
    }
  }

  function clearSensorWatchdog() {
    if (state.navigation.sensorWatchdogTimer !== null) {
      clearTimeout(state.navigation.sensorWatchdogTimer);
      state.navigation.sensorWatchdogTimer = null;
    }
  }

  async function requestWakeLock() {
    if (!state.navigation.active || document.visibilityState !== 'visible') return;
    if (!window.isSecureContext || !('wakeLock' in navigator)) return;
    if (state.navigation.wakeLock) return;
    try {
      const lock = await navigator.wakeLock.request('screen');
      state.navigation.wakeLock = lock;
      lock.addEventListener('release', () => {
        if (state.navigation.wakeLock === lock) state.navigation.wakeLock = null;
      });
    } catch (error) {
      console.debug('Screen wake lock unavailable:', error);
    }
  }

  async function releaseWakeLock() {
    const lock = state.navigation.wakeLock;
    state.navigation.wakeLock = null;
    if (!lock) return;
    try {
      await lock.release();
    } catch (_) {
      // It may already have been released automatically by Chrome.
    }
  }

  function wireMobileLifecycle() {
    if (state.lifecycleWired) return;
    state.lifecycleWired = true;

    document.addEventListener('visibilitychange', () => {
      if (!state.navigation.active) return;
      if (document.visibilityState === 'visible') {
        state.navigation.motionEngine?.resume();
        requestWakeLock();
        updateSensorStatus();
      } else {
        releaseWakeLock();
      }
    });

    window.addEventListener('pageshow', () => {
      if (!state.navigation.active) return;
      state.navigation.motionEngine?.resume();
      requestWakeLock();
    });
    window.addEventListener('pagehide', releaseWakeLock);
  }

  function setNavigationButtons(active) {
    document.body.classList.toggle('navigation-running', active);
    document.getElementById('startNavBtn').classList.toggle('active', active);
    document.getElementById('bottomStartBtn').classList.toggle('active', active);
  }

  function wireControls() {
    el.search.addEventListener('input', () => { renderPlaces(); renderPlaceMarkers(); });
    el.fromSelect.addEventListener('change', () => {
      if (setStartFromPlace(el.fromSelect.value)) {
        clearRoute();
        const place = findPlace(el.fromSelect.value);
        el.summaryTitle.textContent = `Starting at ${place?.name || 'selected place'}`;
        el.summaryText.textContent = 'Choose a destination, then request directions.';
      }
    });
    el.toSelect.addEventListener('change', () => {
      if (el.toSelect.value === state.selectedFrom) {
        toast('Start and destination must be different.');
        el.toSelect.value = state.selectedTo || '';
        return;
      }
      selectPlace(el.toSelect.value, false);
      clearRoute();
    });
    document.getElementById('routeBtn').addEventListener('click', () => showRoute(true));
    document.getElementById('startNavBtn').addEventListener('click', startDirection);
    document.getElementById('bottomStartBtn').addEventListener('click', startDirection);
    document.getElementById('stopNavBtn').addEventListener('click', () => stopDirection(false));
    document.getElementById('bottomStopBtn').addEventListener('click', () => stopDirection(false));
    document.getElementById('simulateBtn').addEventListener('click', simulateMovement);
    document.getElementById('centerBtn').addEventListener('click', centerMap);
    document.getElementById('resetView').addEventListener('click', centerMap);
    document.getElementById('zoomIn').addEventListener('click', () => state.map.zoomIn(0.5));
    document.getElementById('zoomOut').addEventListener('click', () => state.map.zoomOut(0.5));
    document.getElementById('mobileToggle').addEventListener('click', () => el.side.classList.add('open'));
    el.panelCloseBtn?.addEventListener('click', () => el.side.classList.remove('open'));
    el.pickStartBtn?.addEventListener('click', beginStartPicking);
    document.getElementById('copyBtn').addEventListener('click', copyLink);
    document.getElementById('swapBtn').addEventListener('click', swapPlaces);
    document.getElementById('keyboardHintBtn').addEventListener('click', () => toast('Keyboard test: A/D rotate, S turns around, W advances on the road matching the heading.'));
    document.getElementById('graphBtn').addEventListener('click', toggleGraph);
    document.getElementById('setStartBtn').addEventListener('click', recalibrateDeviceDirection);

    el.voiceBtn?.addEventListener('click', () => {
      state.navigation.voiceEnabled = !state.navigation.voiceEnabled;
      el.voiceBtn.classList.toggle('muted', !state.navigation.voiceEnabled);
      el.voiceBtn.textContent = state.navigation.voiceEnabled ? '🔊' : '🔇';
      toast(state.navigation.voiceEnabled ? 'Spoken directions enabled.' : 'Spoken directions muted.');
    });
    el.followBtn?.addEventListener('click', () => {
      state.navigation.followMode = true;
      el.followBtn.classList.remove('inactive');
      if (state.navigation.lastAcceptedPoint) state.map.panTo(pointToLatLng(state.navigation.lastAcceptedPoint), {animate: true});
    });
    state.map.on('dragstart', () => {
      if (!state.navigation.active) return;
      state.navigation.followMode = false;
      el.followBtn?.classList.add('inactive');
    });
    state.map.on('click', event => {
      if (!state.pickingStart) return;
      const point = latLngToPoint(event.latlng);
      const nearest = nearestPlaceToPoint(point);
      if (nearest) finishStartPicking(nearest.id);
    });

    window.addEventListener('keydown', (event) => {
      if (['ArrowUp', 'w', 'W'].includes(event.key)) { event.preventDefault(); nudgeByKeyboard(0); }
      if (['ArrowDown', 's', 'S'].includes(event.key)) { event.preventDefault(); nudgeByKeyboard(180); }
      if (['ArrowLeft', 'a', 'A'].includes(event.key)) { event.preventDefault(); nudgeByKeyboard(-90); }
      if (['ArrowRight', 'd', 'D'].includes(event.key)) { event.preventDefault(); nudgeByKeyboard(90); }
    });
  }

  function toggleGraph() {
    const hasLayers = state.layers.graph.getLayers().length > 0;
    state.layers.graph.clearLayers();
    if (hasLayers) return;
    state.graph.edges.forEach(edge => {
      L.polyline(pointsToLatLngs(edge.path), {color: '#173a2c', weight: 3, opacity: 0.45, dashArray: '6 8', interactive: false}).addTo(state.layers.graph);
    });
    Object.entries(state.graph.nodes).forEach(([code, node]) => {
      L.circleMarker(pointToLatLng(node), {radius: 5, color: '#fff', fillColor: '#173a2c', fillOpacity: 1, weight: 2, interactive: false}).addTo(state.layers.graph);
    });
  }

  function swapPlaces() {
    if (!state.selectedFrom || !state.selectedTo) { toast('Select both start and destination first.'); return; }
    if (state.navigation.active) stopDirection(false);
    const oldFrom = state.selectedFrom;
    setStartFromPlace(state.selectedTo);
    state.selectedTo = oldFrom;
    el.toSelect.value = oldFrom;
    selectPlace(oldFrom, false);
    showRoute(true);
  }

  function copyLink() {
    const url = new URL(location.href);
    if (state.selectedFrom) url.searchParams.set('from', state.selectedFrom);
    if (state.selectedTo) url.searchParams.set('to', state.selectedTo);
    navigator.clipboard?.writeText(url.toString()).then(() => toast('QR link copied.')).catch(() => toast('Could not copy link.'));
  }

  async function maybeStoreLocation(geo, point, moved, progress, source) {
    if (!state.route?.route_log_id) return;
    const now = Date.now();
    if (now - state.navigation.lastSentAt < 1200 && source !== 'gps_anchor') return;
    state.navigation.lastSentAt = now;
    try {
      await fetch(configuredUrl('mapLocationUrl', '/api/guest-map/location'), {
        method: 'POST',
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken()},
        body: JSON.stringify({
          route_log_id: state.route.route_log_id,
          session_uuid: state.route.session_uuid,
          lat: geo?.lat ?? null,
          lng: geo?.lng ?? null,
          accuracy_meters: geo?.accuracy ?? null,
          altitude: geo?.altitude ?? null,
          heading: Number.isFinite(state.navigation.heading) ? state.navigation.heading : null,
          speed_meters_per_second: geo?.speed ?? null,
          map_x: point.x,
          map_y: point.y,
          gps_distance_meters: moved,
          route_progress_percent: progress,
          source,
        }),
      });
    } catch (error) {
      console.warn(error);
    }
  }

  async function finishNavigation(routeLogId, status) {
    try {
      await fetch(configuredUrl('mapFinishUrl', '/api/guest-map/navigation/finish'), {
        method: 'POST',
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken()},
        body: JSON.stringify({route_log_id: routeLogId, status}),
      });
    } catch (error) {
      console.warn(error);
    }
  }

  function renderGuidancePreview() {
    if (!state.route) return;
    const maneuvers = state.route.maneuvers?.length ? state.route.maneuvers : buildClientManeuvers(state.route.path, state.route.from.name, state.route.to.name);
    const next = maneuvers.find(item => !['depart'].includes(item.type)) || maneuvers[0];
    el.navGuidance.classList.add('preview');
    el.maneuverIcon.textContent = maneuverIcon(next?.type || 'depart');
    el.maneuverDistance.textContent = 'Route ready';
    el.maneuverText.textContent = next?.instruction || `Head to ${state.route.to.name}`;
    el.maneuverSubtext.textContent = `${formatDistance(state.route.distance_meters)} • ${state.route.walk_minutes} min walk`;
  }

  function buildClientManeuvers(path, fromName, toName) {
    const points = simplifyDuplicatePoints(path || []);
    if (points.length < 2) return [{type: 'arrive', instruction: `Arrive at ${toName}.`, distance_from_start_meters: 0, point: points[0] || null}];
    const cumulative = [0];
    for (let i = 1; i < points.length; i++) cumulative[i] = cumulative[i - 1] + pixelDistance(points[i - 1], points[i]) * metersPerPixel();
    const maneuvers = [{
      type: 'depart',
      instruction: `Head ${bearingWord(bearingBetween(points[0], points[1]))}${fromName ? ` from ${fromName}` : ''}.`,
      distance_from_start_meters: 0,
      point: points[0],
      bearing_after: bearingBetween(points[0], points[1]),
      turn_degrees: 0,
    }];
    const lookAhead = 7;
    for (let i = 1; i < points.length - 1; i++) {
      let before = i - 1;
      while (before > 0 && cumulative[i] - cumulative[before] < lookAhead) before--;
      let after = i + 1;
      while (after < points.length - 1 && cumulative[after] - cumulative[i] < lookAhead) after++;
      const incoming = bearingBetween(points[before], points[i]);
      const outgoing = bearingBetween(points[i], points[after]);
      if (!Number.isFinite(incoming) || !Number.isFinite(outgoing)) continue;
      const turn = signedAngle(outgoing - incoming);
      const abs = Math.abs(turn);
      if (abs < 28) continue;
      const type = abs >= 150 ? 'uturn' : turn >= 70 ? 'turn-right' : turn >= 28 ? 'slight-right' : turn <= -70 ? 'turn-left' : 'slight-left';
      const instruction = type === 'uturn' ? 'Make a U-turn.' : type === 'turn-right' ? 'Turn right.' : type === 'slight-right' ? 'Keep slightly right.' : type === 'turn-left' ? 'Turn left.' : 'Keep slightly left.';
      const candidate = {type, instruction, distance_from_start_meters: cumulative[i], point: points[i], bearing_after: outgoing, turn_degrees: turn};
      const last = maneuvers[maneuvers.length - 1];
      if (maneuvers.length > 1 && candidate.distance_from_start_meters - last.distance_from_start_meters < 10) {
        if (Math.abs(candidate.turn_degrees) > Math.abs(last.turn_degrees || 0)) maneuvers[maneuvers.length - 1] = candidate;
      } else maneuvers.push(candidate);
    }
    maneuvers.push({type: 'arrive', instruction: `Arrive at ${toName}.`, distance_from_start_meters: cumulative[cumulative.length - 1], point: points[points.length - 1], bearing_after: null, turn_degrees: 0});
    return maneuvers;
  }

  function updateGuidance(path, source) {
    const maneuvers = state.navigation.currentManeuvers?.length
      ? state.navigation.currentManeuvers
      : buildClientManeuvers(path, 'your location', state.route?.to?.name || 'destination');
    const next = maneuvers.find(item => item.type !== 'depart' && Number(item.distance_from_start_meters) > 2) || maneuvers[maneuvers.length - 1];
    if (!next) return;
    const distance = Math.max(0, Number(next.distance_from_start_meters || 0));
    el.maneuverIcon.textContent = maneuverIcon(next.type);
    el.maneuverDistance.textContent = next.type === 'arrive' && distance <= 5 ? 'Arriving' : `${formatDistance(distance)} ahead`;
    el.maneuverText.textContent = next.instruction;
    el.maneuverSubtext.textContent = `${formatDistance(state.navigation.remainingDistanceMeters || 0)} remaining to ${state.route.to.name}`;

    const threshold = distance <= 5 ? 'now' : distance <= 12 ? '12m' : distance <= 30 ? '30m' : 'far';
    if (threshold !== 'far') {
      const spoken = threshold === 'now' ? next.instruction : `In ${formatDistance(distance)}, ${next.instruction.toLowerCase()}`;
      speakInstruction(spoken, `${next.type}:${threshold}:${Math.round(next.point?.x || 0)}:${Math.round(next.point?.y || 0)}`);
    }
  }

  function maneuverIcon(type) {
    return ({depart: '↑', 'turn-left': '↰', 'turn-right': '↱', 'slight-left': '↖', 'slight-right': '↗', uturn: '⤵', arrive: '✓'}[type] || '↑');
  }

  function speakInstruction(text, key) {
    if (!state.navigation.voiceEnabled || !('speechSynthesis' in window) || !text || state.navigation.lastSpokenKey === key) return;
    state.navigation.lastSpokenKey = key;
    try {
      window.speechSynthesis.cancel();
      const utterance = new SpeechSynthesisUtterance(text);
      utterance.rate = 1;
      utterance.pitch = 1;
      window.speechSynthesis.speak(utterance);
    } catch (_) {}
  }

  function setRerouteStatus(status) {
    if (!el.rerouteBadge) return;
    el.rerouteBadge.classList.remove('rerouting', 'off-route');
    if (status === 'rerouting') {
      el.rerouteBadge.textContent = 'Rerouting';
      el.rerouteBadge.classList.add('rerouting');
      setTimeout(() => setRerouteStatus('on-route'), 1600);
    } else if (status === 'off-route') {
      el.rerouteBadge.textContent = 'Off mapped path';
      el.rerouteBadge.classList.add('off-route');
    } else if (status === 'searching') {
      el.rerouteBadge.textContent = 'Matching location';
      el.rerouteBadge.classList.add('rerouting');
    } else {
      el.rerouteBadge.textContent = state.navigation.rerouteCount ? `On route • ${state.navigation.rerouteCount} reroute` : 'On route';
    }
  }

  function routeSignatureForPath(path) {
    const points = path || [];
    if (!points.length) return 'empty';
    const indexes = [0, Math.floor(points.length / 2), points.length - 1];
    return indexes.map(index => `${Math.round(points[index].x / 4)}:${Math.round(points[index].y / 4)}`).join('|');
  }

  function signedAngle(value) {
    return ((Number(value) + 540) % 360) - 180;
  }

  function angleDifference(a, b) {
    return Math.abs(signedAngle(Number(a) - Number(b)));
  }

  function bearingWord(value) {
    if (!Number.isFinite(value)) return 'forward';
    const labels = ['north', 'northeast', 'east', 'southeast', 'south', 'southwest', 'west', 'northwest'];
    return labels[Math.round(normalizeDegrees(value) / 45) % 8];
  }

  function formatDistance(value) {
    const meters = Math.max(0, Number(value || 0));
    if (meters >= 1000) return `${(meters / 1000).toFixed(meters >= 10000 ? 0 : 1)} km`;
    if (meters >= 100) return `${Math.round(meters / 10) * 10} m`;
    return `${Math.max(0, Math.round(meters))} m`;
  }

  function findPlace(id) {
    return state.places.find(p => p.id === id);
  }

  function nodePoint(code) {
    const n = state.graph.nodes[code];
    return n ? {x: Number(n.x), y: Number(n.y)} : null;
  }

  function projectPointToSegment(point, a, b) {
    const apx = Number(point.x) - Number(a.x);
    const apy = Number(point.y) - Number(a.y);
    const abx = Number(b.x) - Number(a.x);
    const aby = Number(b.y) - Number(a.y);
    const ab2 = abx * abx + aby * aby;
    const t = clamp(ab2 > 0 ? ((apx * abx) + (apy * aby)) / ab2 : 0, 0, 1);
    const projected = {x: Number(a.x) + abx * t, y: Number(a.y) + aby * t};
    return {point: projected, t, distancePx: pixelDistance(point, projected)};
  }

  function pointAlongPath(path, ratio) {
    if (!path || !path.length) return null;
    const total = polylineLengthPx(path);
    let target = total * clamp(ratio, 0, 1);
    for (let i = 1; i < path.length; i++) {
      const a = path[i - 1];
      const b = path[i];
      const len = pixelDistance(a, b);
      if (target <= len) {
        const t = len > 0 ? target / len : 0;
        return {x: Number(a.x) + (Number(b.x) - Number(a.x)) * t, y: Number(a.y) + (Number(b.y) - Number(a.y)) * t};
      }
      target -= len;
    }
    return path[path.length - 1];
  }

  function joinPaths(paths) {
    let out = [];
    paths.forEach(path => {
      if (!path || !path.length) return;
      if (!out.length) out = [...path];
      else {
        const first = path[0];
        const last = out[out.length - 1];
        out.push(...(pixelDistance(first, last) < 0.001 ? path.slice(1) : path));
      }
    });
    return out;
  }

  function simplifyDuplicatePoints(points) {
    const out = [];
    (points || []).forEach(p => {
      if (!out.length || pixelDistance(out[out.length - 1], p) > 0.001) out.push({x: Number(p.x), y: Number(p.y)});
    });
    return out;
  }

  function polylineLengthPx(points) {
    let total = 0;
    for (let i = 1; i < (points || []).length; i++) total += pixelDistance(points[i - 1], points[i]);
    return total;
  }

  function pixelDistance(a, b) {
    if (!a || !b) return Infinity;
    return Math.hypot(Number(a.x) - Number(b.x), Number(a.y) - Number(b.y));
  }

  function metersPerPixel() {
    return Math.max(0.1, Number(state.data?.map?.meters_per_pixel || 1.45));
  }

  function geoOffsetMeters(origin, current) {
    if (!origin || !current) return {east: 0, north: 0};
    const R = 6378137;
    const dLat = (Number(current.lat) - Number(origin.lat)) * Math.PI / 180;
    const dLng = (Number(current.lng) - Number(origin.lng)) * Math.PI / 180;
    const avgLat = ((Number(origin.lat) + Number(current.lat)) / 2) * Math.PI / 180;
    return {east: R * dLng * Math.cos(avgLat), north: R * dLat};
  }

  function haversineMeters(a, b) {
    if (!a || !b || !Number.isFinite(a.lat) || !Number.isFinite(a.lng) || !Number.isFinite(b.lat) || !Number.isFinite(b.lng)) return 0;
    const R = 6371000;
    const p1 = a.lat * Math.PI / 180;
    const p2 = b.lat * Math.PI / 180;
    const dp = (b.lat - a.lat) * Math.PI / 180;
    const dl = (b.lng - a.lng) * Math.PI / 180;
    const h = Math.sin(dp / 2) ** 2 + Math.cos(p1) * Math.cos(p2) * Math.sin(dl / 2) ** 2;
    return 2 * R * Math.atan2(Math.sqrt(h), Math.sqrt(1 - h));
  }

  function firstPathBearing(path) {
    const points = path || [];
    for (let i = 1; i < points.length; i++) {
      if (pixelDistance(points[i - 1], points[i]) > 0.01) return bearingBetween(points[i - 1], points[i]);
    }
    return null;
  }

  function bearingBetween(a, b) {
    if (!a || !b) return null;
    const dx = Number(b.x) - Number(a.x);
    const dy = Number(b.y) - Number(a.y);
    if (Math.abs(dx) < 0.0001 && Math.abs(dy) < 0.0001) return null;
    return normalizeDegrees(Math.atan2(dx, -dy) * 180 / Math.PI);
  }

  function normalizeDegrees(value) {
    let v = Number(value) % 360;
    if (v < 0) v += 360;
    return v;
  }

  function formatBearing(value) {
    if (!Number.isFinite(value)) return '—';
    const labels = ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'];
    const index = Math.round(normalizeDegrees(value) / 45) % 8;
    return labels[index] + ' ' + Math.round(normalizeDegrees(value)) + '°';
  }

  function clamp(value, min, max) {
    return Math.max(min, Math.min(max, Number(value)));
  }

  function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, ch => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;'}[ch]));
  }

  function isAndroidChrome() {
    const ua = navigator.userAgent || '';
    return /Android/i.test(ua) && /Chrome\//i.test(ua) && !/EdgA|OPR\//i.test(ua);
  }

  function isLikelyDesktop() {
    const ua = navigator.userAgent || '';
    return !/Android|iPhone|iPad|iPod|Mobile/i.test(ua) && (navigator.maxTouchPoints || 0) < 2;
  }

  function toast(message) {
    el.toast.textContent = message;
    el.toast.classList.add('show');
    clearTimeout(el.toast._t);
    el.toast._t = setTimeout(() => el.toast.classList.remove('show'), 2600);
  }

  init();
})();
