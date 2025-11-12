@extends('layout.app')

@section('title', 'GPS Tracker')

@section('content')

<main class="flex-1 flex flex-col items-center justify-center p-6">
  <div class="w-full max-w-6xl bg-white shadow-lg rounded-2xl border border-amber-100 p-6">
    <h2 class="text-lg font-semibold mb-4 flex items-center gap-2 text-amber-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 .828-.672 1.5-1.5 1.5S9 11.828 9 11s.672-1.5 1.5-1.5S12 10.172 12 11z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3C7.03 3 3 7.03 3 12c0 3.07 1.64 5.97 4.13 7.5L12 21l4.87-1.5A8.999 8.999 0 0021 12c0-4.97-4.03-9-9-9z" />
      </svg>
      Live Vehicle Locations
    </h2>

    <div id="map" class="h-[600px] w-full rounded-lg border border-amber-200"></div>

    <div class="mt-6">
      <div class="flex items-center justify-between mb-3">
        <h3 class="text-sm font-semibold text-amber-800">Live Vehicles</h3>
        <div class="flex items-center gap-3">
          <input id="veh-search" type="text" placeholder="Search plate, UID, make, model" class="border rounded px-2 py-1 text-sm" />
          <select id="veh-type" class="border rounded px-2 py-1 text-sm">
            <option value="all">All types</option>
            @foreach(($vehicleTypes ?? []) as $t)
              @if($t)
                <option value="{{ $t }}">{{ $t }}</option>
              @endif
            @endforeach
          </select>
          <div class="text-xs text-gray-500">Last updated: <span id="last-updated">-</span></div>
        </div>
      </div>
      <div class="overflow-auto rounded-lg border border-amber-200">
        <table class="min-w-full text-sm">
          <thead class="bg-amber-50 text-amber-900">
            <tr>
              <th class="px-3 py-2 text-left font-semibold">Vehicle</th>
              <th class="px-3 py-2 text-left font-semibold">Plate</th>
              <th class="px-3 py-2 text-left font-semibold">GPS UID</th>
              <th class="px-3 py-2 text-left font-semibold">Latitude</th>
              <th class="px-3 py-2 text-left font-semibold">Longitude</th>
              <th class="px-3 py-2 text-left font-semibold">Address</th>
              <th class="px-3 py-2 text-left font-semibold">When</th>
              <th class="px-3 py-2 text-left font-semibold">Status</th>
            </tr>
          </thead>
          <tbody id="gps-table-body" class="divide-y divide-amber-100 bg-white"></tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const map = L.map('map', { zoomControl: true }).setView([15.5, 121.0], 11);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const markers = new Map(); // vehicle_id -> marker
  const group = L.layerGroup().addTo(map);
  let firstFit = false;
  const lastAddressByVehicle = new Map(); // vehicle_id -> last address string
  const lastShownCoords = new Map(); // vehicle_id -> { latText, lngText }
  const AUTO_RADIUS_M = 16093; // ~10 miles in meters
  let userMoved = false;
  map.on('movestart', () => { userMoved = true; });
  map.on('zoomstart', () => { userMoved = true; });

  // search + filter state
  let searchQuery = '';
  let typeFilter = 'all';
  let lastRows = [];
  let tableInitialized = false;

  // vehicle index passed from server
  const vehicleIndex = (() => {
    try { return JSON.parse(document.getElementById('vehicle-index').textContent || '{}'); } catch(e) { return {}; }
  })();

  const pin = (color) => L.icon({
    iconUrl: `https://maps.google.com/mapfiles/ms/icons/${color}-dot.png`,
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -28],
  });

  // Reverse geocoding (optional) with light rate limiting
  const addrCache = new Map();
  let geocodeInFlight = 0;
  const GEOCODE_LIMIT = 3; // per refresh burst
  async function reverseGeocode(lat, lng) {
    const key = `${lat.toFixed(5)},${lng.toFixed(5)}`;
    if (addrCache.has(key)) return addrCache.get(key);
    if (geocodeInFlight >= GEOCODE_LIMIT) return '';
    geocodeInFlight++;
    try {
      const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=16&addressdetails=1`;
      const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('geocode http');
      const data = await res.json();
      const addr = data.display_name || '';
      addrCache.set(key, addr);
      return addr;
    } catch(_) { return ''; }
    finally { geocodeInFlight = Math.max(0, geocodeInFlight-1); }
  }

  function distMeters(lat1, lon1, lat2, lon2) {
    const R = 6371000; // meters
    const toRad = x => x * Math.PI / 180;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }

  function matchesFilter(row) {
    const v = vehicleIndex[row.vehicle_id] || {};
    if (typeFilter !== 'all' && (v.vehicle_type || '') !== typeFilter) return false;
    const q = searchQuery.trim().toLowerCase();
    if (!q) return true;
    const hay = [
      String(row.vehicle_id || ''),
      v.plate_no || '',
      v.gps_device_id || '',
      v.make || '',
      v.model || ''
    ].join(' ').toLowerCase();
    return hay.includes(q);
  }

  function upsertMarker(row) {
    const id = row.vehicle_id;
    const lat = parseFloat(row.latitude);
    const lng = parseFloat(row.longitude);
    const when = row.recorded_at ? new Date(row.recorded_at) : (row.created_at ? new Date(row.created_at) : null);
    const status = (row.status || '').toString().toLowerCase();
    const color = status === 'online' ? 'green' : 'red';

    const v = vehicleIndex[id] || {};
    const plate = v.plate_no || '';
    const uid = v.gps_device_id || '';

    let m = markers.get(id);
    const html = `<div class="text-sm">
      <div class="font-semibold">Vehicle #${id} ${plate ? '('+plate+')' : ''}</div>
      <div>GPS UID: <span class="font-mono">${uid}</span></div>
      <div>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</div>
      <div>Status: <span class="${status === 'online' ? 'text-green-600' : 'text-red-600'}">${status || 'unknown'}</span></div>
      <div class="text-gray-500">${when ? when.toLocaleString() : ''}</div>
    </div>`;

    const visible = matchesFilter(row);
    if (!m) {
      m = L.marker([lat, lng], { icon: pin(color) }).bindPopup(html);
      m.addTo(group);
      markers.set(id, m);
    } else {
      m.setLatLng([lat, lng]).bindPopup(html);
      m.setIcon(pin(color));
    }
    // toggle visibility by opacity; we'll compute bounds only on visible markers later
    m.setOpacity(visible ? 1 : 0);
  }

  async function refresh() {
    try {
      geocodeInFlight = 0;
      const res = await fetch(`/api/vehicle-locations/latest?ts=${Date.now()}` , { cache: 'no-store' });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const rows = await res.json();

      if (!Array.isArray(rows)) return;
      lastRows = rows;
      rows.forEach(upsertMarker);

      // Render table rows
      renderTable(rows);

      const visibleMarkers = [];
      group.getLayers().forEach(l => { if (l.getOpacity && l.getOpacity() > 0) visibleMarkers.push(l); });
      if (visibleMarkers.length > 0) {
        const fg = L.featureGroup(visibleMarkers);
        const center = fg.getBounds().getCenter();
        if (!userMoved) {
          const bounds = L.circle(center, { radius: AUTO_RADIUS_M }).getBounds();
          map.fitBounds(bounds, { animate: true, padding: [20,20] });
          firstFit = true;
        }
      }

      const el = document.getElementById('last-updated');
      if (el) el.textContent = new Date().toLocaleTimeString();
    } catch (err) {
      console.error('Failed to refresh vehicle locations:', err);
    }
  }

  function renderTable(rows) {
    const tbody = document.getElementById('gps-table-body');
    if (!tbody) return;

    const sorted = [...rows].filter(matchesFilter).sort((a, b) => (a.vehicle_id || 0) - (b.vehicle_id || 0));
    const html = sorted.map(r => {
      const lat = parseFloat(r.latitude);
      const lng = parseFloat(r.longitude);
      const when = r.recorded_at || r.created_at || '';
      const status = (r.status || '').toString().toLowerCase();
      const statusClass = status === 'online' ? 'text-green-600' : 'text-red-600';
      const v = vehicleIndex[r.vehicle_id] || {};
      const plate = v.plate_no || '';
      const uid = v.gps_device_id || '';
      return `
        <tr data-vehicle="${r.vehicle_id}">
          <td class="px-3 py-2 font-medium text-gray-800">#${r.vehicle_id}</td>
          <td class="px-3 py-2">${plate}</td>
          <td class="px-3 py-2 font-mono text-xs">${uid}</td>
          <td class="px-3 py-2 tabular-nums" data-latcell="${r.vehicle_id}"></td>
          <td class="px-3 py-2 tabular-nums" data-lngcell="${r.vehicle_id}"></td>
          <td class="px-3 py-2 text-gray-600" data-addr="${r.vehicle_id}"></td>
          <td class="px-3 py-2 text-gray-600">${when}</td>
          <td class="px-3 py-2"><span class="${statusClass}">${status || ''}</span></td>
        </tr>
      `;
    }).join('');

    if (!tableInitialized) {
      tbody.innerHTML = html;
      tableInitialized = true;
    }

    // Coords update policy: only refresh coords immediately if address remains the same
    // AND movement since last shown is >= 50m.
    sorted.forEach(r => {
      const id = r.vehicle_id;
      const lat = parseFloat(r.latitude);
      const lng = parseFloat(r.longitude);
      const latCell = tbody.querySelector(`[data-latcell="${id}"]`);
      const lngCell = tbody.querySelector(`[data-lngcell="${id}"]`);
      if (!latCell || !lngCell || !isFinite(lat) || !isFinite(lng)) return;
      const key = `${lat.toFixed(5)},${lng.toFixed(5)}`; // for cache lookup
      const newLatText = lat.toFixed(6);
      const newLngText = lng.toFixed(6);
      const prevShown = lastShownCoords.get(id);
      let within50m = false;
      if (prevShown && isFinite(parseFloat(prevShown.latText)) && isFinite(parseFloat(prevShown.lngText))) {
        const d = distMeters(parseFloat(prevShown.latText), parseFloat(prevShown.lngText), lat, lng);
        within50m = d < 50;
      }

      // Try to get new address from cache to compare with last shown address
      const cachedAddr = addrCache.get(key);
      const lastAddr = lastAddressByVehicle.get(id);
      if (!lastAddr) {
        // First time: show coords, actual address will fill asynchronously
        latCell.textContent = newLatText;
        lngCell.textContent = newLngText;
        lastShownCoords.set(id, { latText: newLatText, lngText: newLngText });
      } else if (cachedAddr && cachedAddr === lastAddr) {
        // Address unchanged: refresh coords smoothly
        if (!within50m) {
          latCell.textContent = newLatText;
          lngCell.textContent = newLngText;
          lastShownCoords.set(id, { latText: newLatText, lngText: newLngText });
        }
      } else if (prevShown) {
        // Address likely changed or unknown: keep previous coords until new address resolves
        latCell.textContent = prevShown.latText;
        lngCell.textContent = prevShown.lngText;
      }
    });

    // Resolve addresses asynchronously. Once resolved, update address and coords together.
    sorted.forEach(async r => {
      const id = r.vehicle_id;
      const lat = parseFloat(r.latitude);
      const lng = parseFloat(r.longitude);
      const addrCell = tbody.querySelector(`[data-addr="${id}"]`);
      const latCell = tbody.querySelector(`[data-latcell="${id}"]`);
      const lngCell = tbody.querySelector(`[data-lngcell="${id}"]`);
      if (!addrCell || !isFinite(lat) || !isFinite(lng)) return;
      const key = `${lat.toFixed(5)},${lng.toFixed(5)}`;
      const lastAddr = lastAddressByVehicle.get(id) || '';
      const addr = await reverseGeocode(lat, lng);
      if (!addr) return;
      if (addr === lastAddr && (addrCell.textContent || '').trim() !== '') {
        // Address is same; optionally update coords if moved >= 50m
        const newLatText = lat.toFixed(6);
        const newLngText = lng.toFixed(6);
        const prevShown = lastShownCoords.get(id);
        let within50m = false;
        if (prevShown && isFinite(parseFloat(prevShown.latText)) && isFinite(parseFloat(prevShown.lngText))) {
          const d = distMeters(parseFloat(prevShown.latText), parseFloat(prevShown.lngText), lat, lng);
          within50m = d < 50;
        }
        if (!within50m && latCell && lngCell) {
          latCell.textContent = newLatText;
          lngCell.textContent = newLngText;
          lastShownCoords.set(id, { latText: newLatText, lngText: newLngText });
        }
        return;
      }
      // Address changed or was empty: update address and coords together
      addrCell.textContent = addr;
      lastAddressByVehicle.set(id, addr);
      const newLatText = lat.toFixed(6);
      const newLngText = lng.toFixed(6);
      if (latCell && lngCell) {
        latCell.textContent = newLatText;
        lngCell.textContent = newLngText;
      }
      lastShownCoords.set(id, { latText: newLatText, lngText: newLngText });
    });
  }

  // search + filter listeners
  const searchEl = document.getElementById('veh-search');
  const typeEl = document.getElementById('veh-type');
  if (searchEl) searchEl.addEventListener('input', () => {
    searchQuery = searchEl.value || '';
    if (Array.isArray(lastRows)) {
      // update markers and table immediately
      lastRows.forEach(upsertMarker);
      renderTable(lastRows);
    }
  });
  if (typeEl) typeEl.addEventListener('change', () => {
    typeFilter = typeEl.value || 'all';
    if (Array.isArray(lastRows)) {
      lastRows.forEach(upsertMarker);
      renderTable(lastRows);
    }
  });

  refresh();
  setInterval(refresh, 5000);
});
</script>

<script type="application/json" id="vehicle-index">@json($vehicleIndex)</script>

@endsection
