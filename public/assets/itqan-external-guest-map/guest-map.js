(function () {
  'use strict';

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
      lastAccuracy: null,
      lastSentAt: 0,
      totalMovedMeters: 0,
      remainingDistanceMeters: 0,
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
    },
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
  };

  function apiUrl(path) {
    return document.body.dataset.baseUrl + path;
  }

  function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  }

  function mapFileUrl(path) {
    return document.body.dataset.baseUrl + '/' + String(path || '').replace(/^\/+/, '');
  }

  async function init() {
    try {
      await loadData();
      setupLeafletMap();
      buildGraph();
      drawRoadNetwork();
      renderFilters();
      renderSelects();
      loadUrlState();
      renderPlaces();
      renderPlaceMarkers();
      wireControls();
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
    const response = await fetch(apiUrl('/external-guest-map/api/data'), {headers: {'Accept': 'application/json'}});
    if (!response.ok) throw new Error('Could not load map data.');
    state.data = await response.json();
    state.places = state.data.places || [];
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

    L.imageOverlay(mapFileUrl('assets/itqan-external-guest-map/template-map.svg'), state.bounds, {interactive: false}).addTo(state.map);
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
    const defaultFrom = findPlace('villas') ? 'villas' : (findPlace('reception') ? 'reception' : state.places[0]?.id);
    const defaultTo = findPlace('pool') ? 'pool' : (state.places.find(p => p.id !== defaultFrom)?.id || state.places[0]?.id);
    const from = url.get('from') || url.get('here') || document.body.dataset.defaultFrom || defaultFrom;
    const to = url.get('to') || document.body.dataset.defaultTo || defaultTo;

    if (from && findPlace(from)) {
      state.selectedFrom = from;
      el.fromSelect.value = from;
    }
    if (to && findPlace(to)) {
      state.selectedTo = to;
      el.toSelect.value = to;
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

  function setStartFromPlace(id) {
    const place = findPlace(id);
    if (!place) return;
    state.selectedFrom = id;
    state.startPoint = {x: Number(place.x), y: Number(place.y), id: place.id, name: place.name};
    el.fromSelect.value = id;
    placeYouMarker();
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
          <button class="btn" data-action="start">Start direction</button>
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

    const response = await fetch(apiUrl(`/external-guest-map/api/route?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&mode=walk`), {headers: {'Accept': 'application/json'}});
    const payload = await response.json();
    if (!response.ok) {
      toast(payload.message || 'Route could not be calculated.');
      return null;
    }

    state.route = normalizeRoute(payload);
    state.navigation.remainingDistanceMeters = state.route.distance_meters;
    drawRemainingRoute(state.route.path);
    renderSteps(payload.steps || []);
    updateRouteStats(state.route);
    el.summaryTitle.textContent = `Route to ${state.route.to.name}`;
    el.summaryText.textContent = `${state.route.distance_meters} m • ${state.route.walk_minutes} min walk. Start Direction uses Leaflet CRS.Simple and road-constrained movement. Left/right only works when your movement lands on a valid connected road.`;
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
    const route = state.route || await showRoute(true);
    if (!route) return;

    resetNavigationRuntime();
    state.navigation.active = true;
    state.navigation.anchorMapPoint = {...route.from};
    state.navigation.mapPoint = {...route.from};
    state.navigation.lastAcceptedPoint = {...route.from};
    state.navigation.remainingDistanceMeters = route.distance_meters;
    state.navigation.currentRoad = nearestRoadProjection(route.from, null, Infinity);
    state.startPoint = {x: route.from.x, y: route.from.y, name: 'You', id: null};
    placeYouMarker();
    setNavigationButtons(true);
    el.side.classList.remove('open');
    el.gpsStat.textContent = 'Starting';
    el.summaryTitle.textContent = 'Starting direction';
    el.summaryText.textContent = `First GPS sample is anchored to ${route.from.name}. The marker will only move along valid road graph segments. Movement into non-road area is blocked.`;
    startHeadingTracking();
    updateSensorStatus();
    drawRemainingRoute(route.path);

    if (!navigator.geolocation) {
      toast('No browser GPS. Use keyboard/simulate for desktop testing or open on a phone with HTTPS.');
      el.gpsStat.textContent = 'No GPS';
      return;
    }

    state.navigation.watchId = navigator.geolocation.watchPosition(
      onGpsPosition,
      onGpsError,
      {enableHighAccuracy: true, maximumAge: 1000, timeout: 15000}
    );
  }

  function onGpsPosition(position) {
    if (!state.navigation.active || !state.route) return;
    const c = position.coords;
    const now = Date.now();
    const geo = {
      lat: c.latitude,
      lng: c.longitude,
      accuracy: c.accuracy,
      altitude: c.altitude,
      heading: c.heading,
      speed: c.speed,
      at: now,
    };
    state.navigation.lastAccuracy = Number.isFinite(c.accuracy) ? c.accuracy : null;
    applyHeadingFromGeo(geo);

    if (!state.navigation.anchorGeo) {
      state.navigation.anchorGeo = geo;
      state.navigation.lastGeo = geo;
      state.navigation.lastRawPoint = {...state.navigation.anchorMapPoint};
      applyRoadConstrainedPoint({...state.navigation.anchorMapPoint}, geo, 'gps_anchor');
      toast(`GPS anchor received. Your phone location is now treated as ${state.route.from.name}.`);
      return;
    }

    const movedMeters = haversineMeters(state.navigation.lastGeo, geo);
    state.navigation.lastGeo = geo;
    if (Number.isFinite(movedMeters) && movedMeters < state.navigation.minGpsMoveMeters) {
      updateLiveText('gps_waiting', 'GPS received, waiting for real movement.');
      return;
    }
    if (Number.isFinite(geo.accuracy) && geo.accuracy > 70) {
      updateLiveText('coarse_gps_waiting', 'GPS accuracy is too low; holding marker on road.');
      return;
    }

    const raw = geoToRelativeMapPoint(geo);
    if (Number.isFinite(raw.bearing)) {
      state.navigation.heading = raw.bearing;
      state.navigation.headingSource = 'gps_travel_direction';
    }
    applyRoadConstrainedPoint(raw.point, geo, 'browser_geolocation');
  }

  function onGpsError(error) {
    console.warn(error);
    el.gpsStat.textContent = 'Blocked';
    updateLiveText('gps_error', 'GPS blocked/unavailable. Use phone HTTPS and allow Location.');
    toast('GPS blocked/unavailable.');
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

  function applyRoadConstrainedPoint(rawPoint, geo, source) {
    const previous = state.navigation.lastAcceptedPoint || state.route.from;
    const projection = nearestRoadProjection(rawPoint, previous, state.navigation.snapPx);

    if (!projection) {
      state.navigation.blockedReason = 'No valid road under movement';
      updateLiveText(source, 'Movement blocked: no valid road at that position.');
      flashMarkerBlocked();
      return;
    }

    const jumpPx = pixelDistance(previous, projection.point);
    const rawMovePx = pixelDistance(previous, rawPoint);
    const maxJump = Math.max(24, Math.min(state.navigation.movementLimitPx, rawMovePx + 18));
    if (jumpPx > maxJump && source !== 'simulated' && !source.startsWith('keyboard_')) {
      state.navigation.blockedReason = 'Road jump rejected';
      updateLiveText(source, 'Movement held: closest road is too far from current road position.');
      flashMarkerBlocked();
      return;
    }

    state.navigation.blockedReason = null;
    const accepted = projection.point;
    const deltaMeters = pixelDistance(previous, accepted) * metersPerPixel();
    if (deltaMeters > 0.2) state.navigation.totalMovedMeters += deltaMeters;
    state.navigation.lastAcceptedPoint = accepted;
    state.navigation.mapPoint = accepted;
    state.navigation.currentRoad = projection;
    state.navigation.routeBearing = projection.bearing;

    state.startPoint = {x: accepted.x, y: accepted.y, id: null, name: 'Live road position'};
    placeYouMarker();

    const remaining = calculateRemainingRouteFromProjection(projection);
    if (remaining && remaining.path.length >= 2) {
      state.navigation.remainingDistanceMeters = remaining.distanceMeters;
      state.navigation.progressPercent = calculateProgressPercent(remaining.distanceMeters);
      drawRemainingRoute(remaining.path);
    }

    updateHeadingDisplay();
    if (state.navigation.active) state.map.panTo(pointToLatLng(accepted), {animate: true, duration: 0.3});
    maybeStoreLocation(geo, accepted, state.navigation.totalMovedMeters, state.navigation.progressPercent, source);
    updateInstruction(source, projection);

    if (pixelDistance(accepted, state.route.to) <= 18 || state.navigation.remainingDistanceMeters <= 3) {
      toast('Arrived at destination.');
      stopDirection(true);
    }
  }

  function nearestRoadProjection(rawPoint, previousPoint = null, maxDistance = Infinity) {
    let best = null;
    for (const edge of state.graph.edges) {
      const path = edge.path || [];
      for (let i = 1; i < path.length; i++) {
        const a = path[i - 1];
        const b = path[i];
        const proj = projectPointToSegment(rawPoint, a, b);
        if (proj.distancePx > maxDistance) continue;
        const distFromPrev = previousPoint ? pixelDistance(previousPoint, proj.point) : 0;
        const score = proj.distancePx * 8 + distFromPrev;
        if (!best || score < best.score) {
          best = {
            edge,
            segmentIndex: i - 1,
            point: proj.point,
            t: proj.t,
            distancePx: proj.distancePx,
            bearing: bearingBetween(a, b),
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
    if (!destNode) return {path: [projection.point, state.route.to], distanceMeters: pixelDistance(projection.point, state.route.to) * metersPerPixel()};

    const pathToFrom = partialEdgePath(projection.edge.path, projection.segmentIndex, projection.t, true);
    const pathToTo = partialEdgePath(projection.edge.path, projection.segmentIndex, projection.t, false);
    const optionA = buildNetworkPathFromNode(projection.edge.from, destNode);
    const optionB = buildNetworkPathFromNode(projection.edge.to, destNode);

    const candidateA = optionA ? joinPaths([pathToFrom, optionA.path]) : null;
    const candidateB = optionB ? joinPaths([pathToTo, optionB.path]) : null;

    const distA = candidateA ? polylineLengthPx(candidateA) * metersPerPixel() : Infinity;
    const distB = candidateB ? polylineLengthPx(candidateB) * metersPerPixel() : Infinity;
    const chosenPath = distA <= distB ? candidateA : candidateB;
    const chosenDistance = Math.min(distA, distB);

    if (!chosenPath) return null;
    return {path: simplifyDuplicatePoints(chosenPath), distanceMeters: chosenDistance};
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
    return {path: simplifyDuplicatePoints(path), distanceMeters: dist[endCode]};
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
    el.gpsStat.textContent = source === 'gps_anchor' ? 'Anchor' : source.startsWith('keyboard_') ? 'Key' : source === 'simulated' ? 'Sim' : accuracy;
    el.progressStat.textContent = `${progress}%`;
    el.movedStat.textContent = `${moved} m`;
    updateHeadingDisplay();

    const roadText = projection ? `Current road direction: ${formatBearing(projection.bearing)}.` : '';
    el.summaryTitle.textContent = 'Navigation running';
    el.summaryText.textContent = `${moved} m moved • ${remaining} m remaining. Marker is constrained to the road graph; left/right only works when a valid road exists there. ${roadText}`;
    el.bottomTitle.textContent = 'Direction running';
    el.bottomText.textContent = `${progress}% complete • ${remaining} m remaining`;
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
      toast('Start Direction first. Keyboard is only for desktop road testing.');
      return;
    }
    const base = state.navigation.lastAcceptedPoint || state.route.from;
    const bearing = Number.isFinite(state.navigation.routeBearing) ? state.navigation.routeBearing : bearingBetween(base, state.route.to) || 0;
    const finalBearing = normalizeDegrees(bearing + directionDegOffset);
    const stepPx = state.navigation.keyboardStepMeters / metersPerPixel();
    const point = {
      x: clamp(base.x + Math.sin(finalBearing * Math.PI / 180) * stepPx, 0, Number(state.data.map.width)),
      y: clamp(base.y - Math.cos(finalBearing * Math.PI / 180) * stepPx, 0, Number(state.data.map.height)),
    };
    state.navigation.heading = finalBearing;
    state.navigation.headingSource = 'keyboard_heading';
    applyRoadConstrainedPoint(point, state.navigation.lastGeo, directionDegOffset === 0 ? 'keyboard_forward' : directionDegOffset === 180 ? 'keyboard_backward' : directionDegOffset > 0 ? 'keyboard_right' : 'keyboard_left');
  }

  function stopDirection(arrived = false) {
    if (state.navigation.watchId !== null) navigator.geolocation.clearWatch(state.navigation.watchId);
    if (state.navigation.simulateTimer !== null) clearInterval(state.navigation.simulateTimer);
    stopHeadingTracking();
    const routeLogId = state.route?.route_log_id;
    state.navigation.active = false;
    state.navigation.watchId = null;
    state.navigation.simulateTimer = null;
    setNavigationButtons(false);
    if (routeLogId) finishNavigation(routeLogId, arrived ? 'arrived' : 'stopped');
    if (!arrived) toast('Navigation stopped.');
    if (!arrived) el.gpsStat.textContent = 'Off';
  }

  function resetNavigationRuntime() {
    if (state.navigation.watchId !== null) navigator.geolocation.clearWatch(state.navigation.watchId);
    if (state.navigation.simulateTimer !== null) clearInterval(state.navigation.simulateTimer);
    stopHeadingTracking();
    Object.assign(state.navigation, {
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
      lastAccuracy: null,
      lastSentAt: 0,
      totalMovedMeters: 0,
      remainingDistanceMeters: state.route?.distance_meters || 0,
      progressPercent: 0,
      currentRoad: null,
      blockedReason: null,
      source: 'off',
    });
  }

  async function startHeadingTracking() {
    if (state.navigation.headingListenerActive || typeof window.DeviceOrientationEvent === 'undefined') {
      updateHeadingDisplay();
      return;
    }
    const enable = () => {
      window.addEventListener('deviceorientationabsolute', onDeviceOrientation, true);
      window.addEventListener('deviceorientation', onDeviceOrientation, true);
      state.navigation.headingListenerActive = true;
      state.navigation.headingSource = state.navigation.headingSource || 'waiting';
      updateHeadingDisplay();
    };
    try {
      if (typeof DeviceOrientationEvent.requestPermission === 'function') {
        const permission = await DeviceOrientationEvent.requestPermission();
        if (permission === 'granted') enable();
        else { state.navigation.headingSource = 'blocked'; updateHeadingDisplay(); }
      } else enable();
    } catch (_) {
      state.navigation.headingSource = 'unavailable';
      updateHeadingDisplay();
    }
  }

  function stopHeadingTracking() {
    if (!state.navigation.headingListenerActive) return;
    window.removeEventListener('deviceorientationabsolute', onDeviceOrientation, true);
    window.removeEventListener('deviceorientation', onDeviceOrientation, true);
    state.navigation.headingListenerActive = false;
  }

  function onDeviceOrientation(event) {
    let heading = null;
    if (Number.isFinite(event.webkitCompassHeading)) heading = event.webkitCompassHeading;
    else if (event.absolute === true && Number.isFinite(event.alpha)) heading = 360 - event.alpha;
    else if (Number.isFinite(event.alpha) && !isLikelyDesktop()) heading = 360 - event.alpha;
    if (!Number.isFinite(heading)) return;
    const screenAngle = screen.orientation && Number.isFinite(screen.orientation.angle) ? screen.orientation.angle : (Number(window.orientation) || 0);
    state.navigation.heading = normalizeDegrees(heading + screenAngle);
    state.navigation.headingSource = event.absolute ? 'device_compass' : 'device_orientation';
    updateHeadingDisplay();
  }

  function applyHeadingFromGeo(geo) {
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
    const label = isLikelyDesktop() ? (hasGeo ? 'Laptop GPS' : 'Laptop') : (hasGeo && hasOrientation ? 'Phone ready' : hasGeo ? 'GPS only' : 'No GPS');
    state.navigation.sensorStatus = label;
    el.sensorStat.textContent = label;
  }

  function setNavigationButtons(active) {
    document.body.classList.toggle('navigation-running', active);
    document.getElementById('startNavBtn').classList.toggle('active', active);
    document.getElementById('bottomStartBtn').classList.toggle('active', active);
  }

  function wireControls() {
    el.search.addEventListener('input', () => { renderPlaces(); renderPlaceMarkers(); });
    el.fromSelect.addEventListener('change', () => { setStartFromPlace(el.fromSelect.value); clearRoute(); });
    el.toSelect.addEventListener('change', () => { selectPlace(el.toSelect.value, false); clearRoute(); });
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
    document.getElementById('copyBtn').addEventListener('click', copyLink);
    document.getElementById('swapBtn').addEventListener('click', swapPlaces);
    document.getElementById('keyboardHintBtn').addEventListener('click', () => toast('Keyboard test: W/↑ forward, S/↓ backward, A/← left, D/→ right. Left/right moves only when a valid road exists.'));
    document.getElementById('graphBtn').addEventListener('click', toggleGraph);
    document.getElementById('setStartBtn').addEventListener('click', () => toast('With Leaflet engine, select a named start from the Guest is here dropdown for road routing.'));

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
      await fetch(apiUrl('/external-guest-map/api/location'), {
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
      await fetch(apiUrl(`/external-guest-map/api/route-log/${routeLogId}/finish`), {
        method: 'POST',
        headers: {'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken()},
        body: JSON.stringify({status}),
      });
    } catch (error) {
      console.warn(error);
    }
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
