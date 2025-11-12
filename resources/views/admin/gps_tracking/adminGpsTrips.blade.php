@extends('layout.app')

@section('title', 'Saved Trips')

@section('content')
<main class="p-6">
  <div class="bg-white border rounded-2xl shadow p-5">
    <div class="flex items-center justify-between mb-4">
      <h2 class="text-lg font-semibold text-amber-800">Saved Trips</h2>
      <a href="{{ route('admin.gps.locations') }}" class="text-sm text-blue-700 hover:underline">Back to Tracker</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @forelse($paths as $sp)
        <div class="border rounded-xl p-3 bg-white shadow-sm">
          <div class="flex items-center justify-between mb-2">
            <div>
              <div class="text-sm font-semibold">{{ $sp->name }}</div>
              <div class="text-xs text-gray-600">Vehicle #{{ $sp->vehicle_id }} — {{ data_get($sp->vehicle_snapshot, 'plate_no') }}</div>
            </div>
            <div class="text-xs text-gray-500">{{ optional($sp->created_at)->diffForHumans() }}</div>
          </div>
          <div class="text-xs text-gray-600 mb-2">
            @php $started = $sp->started_at; $ended = $sp->ended_at; @endphp
            @if($started && $ended)
              <span>Duration: {{ $started->shortAbsoluteDiffForHumans($ended) }}</span>
            @elseif($started)
              <span>Started: {{ $started->toDayDateTimeString() }}</span>
            @endif
          </div>
          <div id="trip-map-{{ $sp->id }}" class="trip-map h-48 w-full rounded border border-amber-200 mb-2" data-id="{{ $sp->id }}"></div>
          <script type="application/json" id="trip-data-{{ $sp->id }}">@json(collect($sp->path ?? [])->map(fn($p)=>[$p['lat'] ?? null, $p['lng'] ?? null])->values())</script>
          <details>
            <summary class="text-xs text-blue-700 cursor-pointer">View JSON</summary>
            <pre class="text-[10px] bg-gray-50 p-2 border rounded overflow-auto max-h-40">@json($sp->path)</pre>
          </details>
        </div>
      @empty
        <div class="text-sm text-gray-600">No trips saved yet.</div>
      @endforelse
    </div>

    <div class="mt-4">
      {{ $paths->links() }}
    </div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function(){
  function ensureLeaflet(cb){
    if (window.L) return cb();
    const css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(css);
    const js = document.createElement('script');
    js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    js.onload = () => cb();
    document.head.appendChild(js);
  }

  ensureLeaflet(()=>{
    const startPin = L.icon({ iconUrl: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png', iconSize: [24,24], iconAnchor: [12,24], popupAnchor: [0,-22] });
    const endPin = L.icon({ iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png', iconSize: [24,24], iconAnchor: [12,24], popupAnchor: [0,-22] });
    document.querySelectorAll('.trip-map').forEach(el => {
      const id = el.getAttribute('data-id');
      const dataEl = document.getElementById('trip-data-' + id);
      let pts = [];
      try { pts = JSON.parse(dataEl?.textContent || '[]'); } catch(_) {}
      const latlngs = (pts||[]).filter(p => Array.isArray(p) && isFinite(p[0]) && isFinite(p[1]));
      const map = L.map(el, { zoomControl: false, attributionControl: false });
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
      if (latlngs.length >= 1) {
        const line = L.polyline(latlngs, { color: '#2563eb', weight: 3, opacity: 0.8 }).addTo(map);
        if (latlngs.length > 1) {
          L.marker(latlngs[0], { icon: startPin, interactive: false }).addTo(map);
          L.marker(latlngs[latlngs.length-1], { icon: endPin, interactive: false }).addTo(map);
          map.fitBounds(line.getBounds().pad(0.2));
        } else {
          map.setView(latlngs[0], 15);
        }
      } else {
        map.setView([15.5, 121.0], 8);
      }
    });
  });
});
</script>
@endsection

