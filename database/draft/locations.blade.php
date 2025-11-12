@extends('layout.app')

@section('title', 'GPS Locations')

@section('content')
<main class="p-6">
  <div class="bg-white rounded-xl shadow border p-4">
    <div class="flex flex-wrap items-end gap-3 mb-3 text-sm">
      <div class="flex-1 min-w-60">
        <label class="block text-xs text-gray-600">Search</label>
        <input type="text" id="search" class="w-full border rounded px-3 py-1.5" placeholder="Search driver, vehicle or plate...">
      </div>
      <div>
        <label class="block text-xs text-gray-600">Status</label>
        <select id="status" class="border rounded px-3 py-1.5">
          <option value="all">All</option>
          <option>Active</option>
          <option>Inactive</option>
          <option>Out of Service</option>
        </select>
      </div>
      <div>
        <label class="block text-xs text-gray-600">Type</label>
        <select id="type" class="border rounded px-3 py-1.5">
          <option value="all">All</option>
          <option>Sedan</option>
          <option>Van</option>
          <option>SUV</option>
          <option>Bus</option>
        </select>
      </div>
    </div>
    <div id="map" class="h-[600px] w-full rounded-lg border"></div>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const map = L.map('map').setView([14.5995, 120.9842], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  // Demo dataset. Replace with API data later.
  const items = [
    { id: 1, name: 'Driver 1', plate: 'ABC-123', type: 'Sedan', status: 'Active', lat: 14.6, lng: 120.985, color: 'red' },
    { id: 2, name: 'Driver 2', plate: 'XYZ-789', type: 'Van', status: 'Inactive', lat: 14.61, lng: 120.99, color: 'blue' },
  ];

  const markers = [];
  function render()
  {
    const q = document.getElementById('search').value.toLowerCase();
    const st = document.getElementById('status').value;
    const tp = document.getElementById('type').value;

    markers.forEach(m => map.removeLayer(m));
    markers.length = 0;

    items.filter(it =>
      (st === 'all' || it.status === st) &&
      (tp === 'all' || it.type === tp) &&
      (it.name.toLowerCase().includes(q) || it.plate.toLowerCase().includes(q))
    ).forEach(it => {
      const m = L.marker([it.lat, it.lng], { icon: L.icon({ iconUrl: `https://maps.google.com/mapfiles/ms/icons/${it.color}-dot.png`, iconSize: [32,32], iconAnchor:[16,32] }) })
        .addTo(map)
        .bindPopup(`<strong>${it.name}</strong><br>${it.plate}<br>${it.type} • ${it.status}`);
      markers.push(m);
    });
  }

  ['search','status','type'].forEach(id => document.getElementById(id).addEventListener('input', render));
  render();
});
</script>
@endsection

