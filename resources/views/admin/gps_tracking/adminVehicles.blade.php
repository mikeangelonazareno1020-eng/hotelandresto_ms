@extends('layout.app')

@section('title', 'Manage Vehicles')

@section('content')
<main class="p-6">
  <div class="bg-white border rounded-xl shadow p-4">
    @if(session('success'))
      <div class="mb-3 text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">{{ session('success') }}</div>
    @endif
    <div class="mb-4 flex justify-end">
      <button id="btnOpenAddVehicle" class="bg-[#B0452D] hover:bg-[#913a25] text-white text-sm px-3 py-2 rounded">Add Vehicle</button>
    </div>
    <div class="flex flex-wrap gap-3 mb-4 text-sm">
      <input id="searchVehicle" class="border rounded px-3 py-1.5" placeholder="Search plate, make, model">
      <select id="statusVehicle" class="border rounded px-3 py-1.5">
        <option value="all">All Status</option>
        <option>Active</option>
        <option>Inactive</option>
        <option>Out of Service</option>
      </select>
      <select id="typeVehicle" class="border rounded px-3 py-1.5">
        <option value="all">All Types</option>
        <option>Sedan</option>
        <option>Van</option>
        <option>SUV</option>
        <option>Bus</option>
      </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" id="vehicleCards">
      @foreach($vehicles as $v)
        <div class="border rounded-lg shadow-sm p-4">
          <div class="flex items-center justify-between mb-2">
            <div>
              <div class="text-sm text-gray-500">Plate</div>
              <div class="text-lg font-semibold">{{ $v->plate_no }}</div>
            </div>
            <div>
              @php $active = ($v->status === 'Active'); @endphp
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs {{ $active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-600' : 'bg-gray-400' }}"></span>
                {{ $v->status ?? 'Unknown' }}
              </span>
            </div>
          </div>
          <div class="mb-2 flex justify-between items-center">
            <div class="text-xs text-gray-500">Path tools</div>
            <div class="flex items-center gap-2">
              <button data-action="toggle-record" data-record-id="{{ $v->id }}" class="px-2 py-0.5 border rounded text-xs text-green-700 hover:bg-green-50">Start Trip</button>
              <button data-action="save-path" data-save-id="{{ $v->id }}" class="px-2 py-0.5 border rounded text-xs text-blue-700 hover:bg-blue-50">Save path</button>
              <button data-action="reset-line" data-reset-id="{{ $v->id }}" class="px-2 py-0.5 border rounded text-xs text-gray-700 hover:bg-gray-50">Reset line</button>
            </div>
          </div>
          <div id="card-map-{{ $v->id }}" class="veh-map h-48 w-full rounded border border-amber-200"
               data-vehicle-id="{{ $v->id }}"
               data-lat="{{ $v->last_latitude }}"
               data-lng="{{ $v->last_longitude }}"></div>
          <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
            <div>
              <div class="text-gray-500">Driver</div>
              <div>{{ optional($v->driver)->firstName }} {{ optional($v->driver)->lastName }}</div>
            </div>
            <div>
              <div class="text-gray-500">GPS UID</div>
              <div class="font-mono">{{ $v->gps_device_id ?? '—' }}</div>
            </div>
            <div>
              <div class="text-gray-500">Last Coord</div>
              <div id="coord-{{ $v->id }}">@if($v->last_coordinates){{ $v->last_coordinates }}@else — @endif</div>
            </div>
            <div>
              <div class="text-gray-500">Last Seen</div>
              <div id="seen-{{ $v->id }}">{{ optional($v->last_seen_at)->diffForHumans() }}</div>
            </div>
          </div>
          <div class="mt-3 flex items-center gap-3">
            <button class="text-blue-700 underline" data-action="edit-vehicle" data-id="{{ $v->id }}" data-plate="{{ $v->plate_no }}" data-uid="{{ $v->gps_device_id }}">Edit</button>
            <form method="POST" action="{{ route('admin.gps.vehicles.destroy', $v->id) }}" onsubmit="return confirm('Delete vehicle {{ $v->plate_no }}?')">
              @csrf
              @method('DELETE')
              <button class="text-red-700 underline">Delete</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>

    <div class="overflow-x-auto hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left">Plate</th>
            <th class="px-3 py-2 text-left">Type</th>
            <th class="px-3 py-2 text-left">Driver</th>
            <th class="px-3 py-2 text-left">Make / Model</th>
            <th class="px-3 py-2 text-left">Capacity</th>
            <th class="px-3 py-2 text-left">Status</th>
            <th class="px-3 py-2 text-left">Last GPS</th>
            <th class="px-3 py-2 text-left">Last Seen</th>
            <th class="px-3 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody id="vehicleBody">
          @foreach($vehicles as $v)
            <tr class="border-t">
              <td class="px-3 py-2 font-semibold">{{ $v->plate_no }}</td>
              <td class="px-3 py-2">{{ $v->vehicle_type ?? '—' }}</td>
              <td class="px-3 py-2">{{ optional($v->driver)->firstName }} {{ optional($v->driver)->lastName }}</td>
              <td class="px-3 py-2">{{ $v->make }} {{ $v->model }}</td>
              <td class="px-3 py-2">{{ $v->capacity }}</td>
              <td class="px-3 py-2">{{ $v->status }}</td>
              <td class="px-3 py-2">{{ $v->last_coordinates ?? '—' }} @if($v->last_coordinates) <a class="text-amber-700 underline" target="_blank" href="https://www.openstreetmap.org/?mlat={{ $v->last_latitude }}&mlon={{ $v->last_longitude }}#map=17/{{ $v->last_latitude }}/{{ $v->last_longitude }}">Map</a>@endif</td>
              <td class="px-3 py-2">{{ optional($v->last_seen_at)->diffForHumans() }}</td>
              <td class="px-3 py-2">
                <div class="flex items-center gap-3">
                  <a class="text-amber-700 underline" target="_blank" href="https://www.openstreetmap.org/?mlat={{ $v->last_latitude }}&mlon={{ $v->last_longitude }}#map=17/{{ $v->last_latitude }}/{{ $v->last_longitude }}">View</a>
                  <button class="text-blue-700 underline" data-action="edit-vehicle" data-id="{{ $v->id }}" data-plate="{{ $v->plate_no }}" data-uid="{{ $v->gps_device_id }}">Edit</button>
                  <form method="POST" action="{{ route('admin.gps.vehicles.destroy', $v->id) }}" onsubmit="return confirm('Delete vehicle {{ $v->plate_no }}?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-700 underline">Delete</button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const rows = Array.from(document.querySelectorAll('#vehicleBody tr'));
      const search = document.getElementById('searchVehicle');
      const status = document.getElementById('statusVehicle');
      const type = document.getElementById('typeVehicle');
      function apply(){
        const q = (search.value||'').toLowerCase();
        const st = status.value; const tp = type.value;
        rows.forEach(tr => {
          const tds = tr.querySelectorAll('td');
          const plate = tds[0].innerText.toLowerCase();
          const vtype = tds[1].innerText; const makeModel = tds[2].innerText.toLowerCase();
          const vstatus = tds[4].innerText;
          const ok = (st==='all'||vstatus===st) && (tp==='all'||vtype===tp) && (plate.includes(q)||makeModel.includes(q));
          tr.style.display = ok ? '' : 'none';
        });
      }
      [search,status,type].forEach(el => el.addEventListener('input', apply));
      apply();

      // Modal UI
      function show(el){ el.classList.remove('hidden'); document.body.style.overflow='hidden'; }
      function hide(el){ el.classList.add('hidden'); document.body.style.overflow=''; }

      const addBtn = document.getElementById('btnOpenAddVehicle');
      const addModal = document.getElementById('modalAddVehicle');
      const editModal = document.getElementById('modalEditVehicle');
      const overlayAdd = document.getElementById('overlayAdd');
      const overlayEdit = document.getElementById('overlayEdit');
      const closeBtns = document.querySelectorAll('[data-modal-close]');

      if(addBtn){ addBtn.addEventListener('click', ()=>show(addModal)); }
      if(overlayAdd){ overlayAdd.addEventListener('click', ()=>hide(addModal)); }
      if(overlayEdit){ overlayEdit.addEventListener('click', ()=>hide(editModal)); }
      closeBtns.forEach(btn=>{
        btn.addEventListener('click', (e)=>{
          e.preventDefault();
          const target = btn.getAttribute('data-target');
          if(target==='add') hide(addModal); else hide(editModal);
        });
      });

      // Wire edit buttons
      document.querySelectorAll('[data-action="edit-vehicle"]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const id = btn.getAttribute('data-id');
          const plate = btn.getAttribute('data-plate') || '';
          const uid = btn.getAttribute('data-uid') || '';
          const form = document.getElementById('formEditVehicle');
          const actionBase = form.getAttribute('data-action-base'); // ends with /0
          form.action = actionBase.replace(/\/(?:0|$)/, '/'+id);
          document.getElementById('edit_plate_no').value = plate;
          document.getElementById('edit_gps_device_id').value = uid;
          show(editModal);
        });
      });
    });
  </script>

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
        // global holders for lines, times and recording flags across cards
        window.vehicleLines = window.vehicleLines || new Map(); // id -> L.Polyline
        window.vehicleTimes = window.vehicleTimes || new Map(); // id -> { started_at: Date, ended_at: Date }
        window.vehicleRecording = window.vehicleRecording || new Map(); // id -> boolean

        // localStorage persistence keys
        const REC_KEY = 'veh_rec_flags_v1';
        const PATH_KEY = 'veh_rec_paths_v1';
        const TIME_KEY = 'veh_rec_times_v1';

        // helpers to load/save JSON safely
        function loadJson(key){ try{ return JSON.parse(localStorage.getItem(key) || '{}') || {}; }catch(_){ return {}; } }
        function saveJson(key,obj){ try{ localStorage.setItem(key, JSON.stringify(obj)); }catch(_){} }

        let recFlags = loadJson(REC_KEY);      // { [id]: true|false }
        let storedPaths = loadJson(PATH_KEY);  // { [id]: [[lat,lng], ...] }
        let storedTimes = loadJson(TIME_KEY);  // { [id]: { started_at: ISO|null, ended_at: ISO|null } }
        const startIcon = L.icon({
          iconUrl: 'https://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
          iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -28],
        });
        const mapEls = document.querySelectorAll('.veh-map');
        mapEls.forEach(el => {
          const id = el.getAttribute('data-vehicle-id');
          const lat = parseFloat(el.getAttribute('data-lat'));
          const lng = parseFloat(el.getAttribute('data-lng'));
          const hasCoords = !isNaN(lat) && !isNaN(lng);

          const map = L.map(el, { zoomControl: false, attributionControl: false });
          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

          let marker = null;
          let line = null;
          let startMarker = null;
          if (hasCoords) {
            // Center map but don't place marker until recording starts
            map.setView([lat, lng], 17);
          } else {
            map.setView([15.5, 121.0], 8);
          }

          // restore persisted recording state and path if any
          const recOn = !!recFlags[id];
          window.vehicleRecording.set(Number(id), recOn);
          const recordBtn = document.querySelector(`[data-record-id="${id}"]`);
          if (recordBtn && recOn) {
            recordBtn.textContent = 'End Trip';
            recordBtn.classList.remove('text-green-700');
            recordBtn.classList.add('text-red-700');
          }
          // restore stored path onto the map if recording is on (or even if off but path exists)
          const stored = Array.isArray(storedPaths[id]) ? storedPaths[id] : null;
          if (stored && stored.length) {
            const latlngs = stored.filter(p=>Array.isArray(p) && isFinite(p[0]) && isFinite(p[1]));
            if (latlngs.length){
              line = L.polyline(latlngs, { color: '#2563eb', weight: 3, opacity: 0.8 }).addTo(map);
              const first = latlngs[0];
              startMarker = L.marker(first, { icon: startIcon, interactive: false }).addTo(map);
              window.vehicleLines.set(Number(id), line);
            }
          }
          // restore stored time bounds
          if (storedTimes[id]) {
            const t = storedTimes[id];
            const meta = { started_at: t.started_at ? new Date(t.started_at) : null, ended_at: t.ended_at ? new Date(t.ended_at) : null };
            window.vehicleTimes.set(Number(id), meta);
          }

          async function loadHistory(){
            try{
              const r = await fetch(`/api/vehicle-locations/history/${id}?limit=200`);
              if(!r.ok) return;
              const pts = await r.json();
              const latlngs = pts.map(p => [parseFloat(p.latitude), parseFloat(p.longitude)]).filter(x=>!isNaN(x[0]) && !isNaN(x[1]));
              if(latlngs.length>=2){
                if(!line){ line = L.polyline(latlngs, { color: '#2563eb', weight: 3, opacity: 0.8 }).addTo(map); }
                else { line.setLatLngs(latlngs); }
                const first = latlngs[0];
                if (!startMarker) { startMarker = L.marker(first, { icon: startIcon, interactive: false }).addTo(map); }
                else { startMarker.setLatLng(first); }
                // initialize started/ended times from history
                try {
                  const firstTs = pts[0]?.created_at || pts[0]?.recorded_at;
                  const lastTs = pts[pts.length-1]?.created_at || pts[pts.length-1]?.recorded_at;
                  if (firstTs && lastTs) {
                    window.vehicleTimes.set(Number(id), { started_at: new Date(firstTs), ended_at: new Date(lastTs) });
                  }
                } catch(_) {}
              }
              if (line) { window.vehicleLines.set(Number(id), line); }
            }catch(e){}
          }

          // draw initial history line
          loadHistory();

          async function refresh(){
            try{
              const r = await fetch(`/api/vehicle-locations/latest/${id}`);
              if (!r.ok) return;
              const row = await r.json();
              const la = parseFloat(row.latitude);
              const lo = parseFloat(row.longitude);
              const when = row.created_at ? new Date(row.created_at) : (row.recorded_at ? new Date(row.recorded_at) : null);
              // Only update map when recording is ON
              const isRecording = !!window.vehicleRecording.get(Number(id));
              if (!isRecording) { return; }
              if (!isNaN(la) && !isNaN(lo)){
                const ll = [la, lo];
                if (!marker){ marker = L.marker(ll).addTo(map); }
                else { marker.setLatLng(ll); }
                if (line){
                  const pts = line.getLatLngs();
                  const last = pts[pts.length-1];
                  if (!last || last.lat !== la || last.lng !== lo){
                    pts.push(ll);
                    line.setLatLngs(pts);
                  }
                } else {
                  // create a fresh line starting at this point and set a new start marker
                  line = L.polyline([ll], { color: '#2563eb', weight: 3, opacity: 0.8 }).addTo(map);
                  if (!startMarker) {
                    startMarker = L.marker(ll, { icon: startIcon, interactive: false }).addTo(map);
                  } else {
                    startMarker.setLatLng(ll);
                  }
                }
                // keep references updated
                if (line) { window.vehicleLines.set(Number(id), line); }
                if (when instanceof Date && !isNaN(when)) {
                  const meta = window.vehicleTimes.get(Number(id)) || {};
                  if (!meta.started_at) meta.started_at = when;
                  meta.ended_at = when;
                  window.vehicleTimes.set(Number(id), meta);
                  // persist times
                  storedTimes[id] = { started_at: meta.started_at ? new Date(meta.started_at).toISOString() : null, ended_at: meta.ended_at ? new Date(meta.ended_at).toISOString() : null };
                  saveJson(TIME_KEY, storedTimes);
                }
                // persist path
                try{
                  const pts = line.getLatLngs();
                  storedPaths[id] = pts.map(p => [Number(p.lat), Number(p.lng)]).slice(-1000);
                  saveJson(PATH_KEY, storedPaths);
                }catch(_){ }
                // keep a tight zoom on the last point
                map.setView(ll, Math.max(map.getZoom(), 17));
                document.getElementById(`coord-${id}`).textContent = `${la.toFixed(6)}, ${lo.toFixed(6)}`;
                if (row.created_at){ document.getElementById(`seen-${id}`).textContent = row.created_at; }
              }
            }catch(e){ /* ignore */ }
          }

          setInterval(refresh, 10000);
          // trigger an immediate fetch so recording resumes without waiting
          refresh();

          // per-card reset
          const resetBtn = document.querySelector(`[data-reset-id="${id}"]`);
          if (resetBtn) {
            resetBtn.addEventListener('click', () => {
              if (line) { map.removeLayer(line); line = null; }
              if (startMarker) { map.removeLayer(startMarker); startMarker = null; }
              // Do NOT reload history; start a new line from next live point
            });
          }

          // per-card recording toggle
          if (recordBtn) {
            recordBtn.addEventListener('click', () => {
              const vid = Number(id);
              const isRecording = !!window.vehicleRecording.get(vid);
              if (!isRecording) {
                // start recording: clear any existing path and time bounds
                if (line) { map.removeLayer(line); line = null; }
                if (startMarker) { map.removeLayer(startMarker); startMarker = null; }
                window.vehicleLines.delete(vid);
                window.vehicleTimes.set(vid, { started_at: null, ended_at: null });
                window.vehicleRecording.set(vid, true);
                // persist flags and clear stored data
                recFlags[id] = true; saveJson(REC_KEY, recFlags);
                delete storedPaths[id]; saveJson(PATH_KEY, storedPaths);
                delete storedTimes[id]; saveJson(TIME_KEY, storedTimes);
                recordBtn.textContent = 'End Trip';
                recordBtn.classList.remove('text-green-700');
                recordBtn.classList.add('text-red-700');
              } else {
                // stop recording
                window.vehicleRecording.set(vid, false);
                const meta = window.vehicleTimes.get(vid) || {};
                if (!meta.ended_at) meta.ended_at = new Date();
                window.vehicleTimes.set(vid, meta);
                // persist flags and times
                recFlags[id] = false; saveJson(REC_KEY, recFlags);
                storedTimes[id] = { started_at: meta.started_at ? new Date(meta.started_at).toISOString() : null, ended_at: meta.ended_at ? new Date(meta.ended_at).toISOString() : null };
                saveJson(TIME_KEY, storedTimes);
                recordBtn.textContent = 'Start Trip';
                recordBtn.classList.remove('text-red-700');
                recordBtn.classList.add('text-green-700');
              }
            });
          }

          // save current path (open naming modal)
          const saveBtn = document.querySelector(`[data-save-id="${id}"]`);
          if (saveBtn) {
            saveBtn.addEventListener('click', () => {
              const modal = document.getElementById('modalVehicleSavePath');
              const input = document.getElementById('vehicleSavePathName');
              const form = document.getElementById('formVehicleSavePath');
              form.setAttribute('data-vehicle', String(id));
              input.value = '';
              modal.classList.remove('hidden');
            });
          }
        });
      });
    });
  </script>
  <!-- Save Path Modal (per vehicle cards) -->
  <div id="modalVehicleSavePath" class="fixed inset-0 z-[1000] hidden">
    <div class="absolute inset-0 bg-black/40" data-modal-close></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold">Save Vehicle Path</h3>
          <button data-modal-close class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form id="formVehicleSavePath" data-vehicle="0" class="space-y-3">
          <div>
            <label class="block text-xs text-gray-600 mb-1">Name</label>
            <input id="vehicleSavePathName" class="w-full border rounded px-2 py-1" maxlength="120" placeholder="e.g. Route A 11/11" />
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" data-modal-close class="px-3 py-1.5 rounded border">Cancel</button>
            <button type="submit" class="px-3 py-1.5 rounded bg-[#B0452D] text-white">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script>
    // Modal interactions + submit for vehicle path save
    document.addEventListener('click', (e) => {
      const t = e.target;
      if (!(t instanceof HTMLElement)) return;
      if (t.matches('[data-modal-close]')) {
        const modal = t.closest('#modalVehicleSavePath') || document.getElementById('modalVehicleSavePath');
        modal?.classList.add('hidden');
      }
    });

    const formVehicleSave = document.getElementById('formVehicleSavePath');
    if (formVehicleSave) {
      formVehicleSave.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('vehicleSavePathName').value.trim();
        const vid = Number(formVehicleSave.getAttribute('data-vehicle'));
        if (!name) { alert('Please enter a name'); return; }
        const line = (window.vehicleLines && window.vehicleLines.get(vid)) || null;
        if (!line) { alert('No path to save for this vehicle'); return; }
        const pts = line.getLatLngs();
        if (!pts || pts.length < 2) { alert('Need at least 2 points'); return; }
        const meta = (window.vehicleTimes && window.vehicleTimes.get(vid)) || {};
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
          const res = await fetch(`/gps/admin/tracker/vehicles/${vid}/save-path`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
            body: JSON.stringify({
              name,
              points: pts.map(p => [p.lat, p.lng]),
              started_at: meta.started_at ? new Date(meta.started_at).toISOString() : null,
              ended_at: meta.ended_at ? new Date(meta.ended_at).toISOString() : null,
            })
          });
          if (!res.ok) throw new Error('Request failed');
          if (window.toast) window.toast({ icon: 'success', title: 'Path saved' });
          else alert('Saved');
          document.getElementById('modalVehicleSavePath').classList.add('hidden');
        } catch (err) {
          if (window.toast) window.toast({ icon: 'error', title: 'Failed to save' });
          else alert('Failed');
        }
      });
    }
  </script>
  <!-- Add Vehicle Modal -->
  <div id="modalAddVehicle" class="fixed inset-0 z-[1000] hidden">
    <div id="overlayAdd" class="absolute inset-0 bg-black/40"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold">Add Vehicle</h3>
          <button data-modal-close data-target="add" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.gps.vehicles.store') }}" class="space-y-3">
          @csrf
          <div>
            <label class="block text-xs text-gray-600 mb-1">Plate No</label>
            <input name="plate_no" required class="w-full border rounded px-2 py-1" placeholder="ABC-1234">
          </div>
          <div>
            <label class="block text-xs text-gray-600 mb-1">GPS Device UID</label>
            <input name="gps_device_id" class="w-full border rounded px-2 py-1" placeholder="esp32-shuttle-1">
          </div>
          <div class="flex justify-end gap-2">
            <button data-modal-close data-target="add" class="px-3 py-1.5 rounded border">Cancel</button>
            <button class="px-3 py-1.5 rounded bg-[#B0452D] text-white">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Vehicle Modal -->
  <div id="modalEditVehicle" class="fixed inset-0 z-[1000] hidden">
    <div id="overlayEdit" class="absolute inset-0 bg-black/40"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
      <div class="w-full max-w-md bg-white rounded-lg shadow p-4">
        <div class="flex items-center justify-between mb-3">
          <h3 class="text-lg font-semibold">Edit Vehicle</h3>
          <button data-modal-close data-target="edit" class="text-gray-500 hover:text-gray-700">✕</button>
        </div>
        <form id="formEditVehicle" data-action-base="{{ route('admin.gps.vehicles.update', 0) }}" method="POST" action="#" class="space-y-3">
          @csrf
          @method('PUT')
          <div>
            <label class="block text-xs text-gray-600 mb-1">Plate No</label>
            <input id="edit_plate_no" name="plate_no" required class="w-full border rounded px-2 py-1">
          </div>
          <div>
            <label class="block text-xs text-gray-600 mb-1">GPS Device UID</label>
            <input id="edit_gps_device_id" name="gps_device_id" class="w-full border rounded px-2 py-1">
          </div>
          <div class="flex justify-end gap-2">
            <button data-modal-close data-target="edit" class="px-3 py-1.5 rounded border">Cancel</button>
            <button class="px-3 py-1.5 rounded bg-blue-600 text-white">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>
@endsection

