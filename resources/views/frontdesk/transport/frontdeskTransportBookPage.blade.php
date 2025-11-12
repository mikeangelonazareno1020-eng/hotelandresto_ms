@extends('layout.app')

@section('title', 'Book Transport | Frontdesk')

@section('content')
<div class="min-h-[calc(100vh-2rem)] bg-linear-to-b from-[#FFF9E6] to-[#FEE934]/40 rounded-2xl shadow-lg border border-amber-200 p-6 text-sm font-[Poppins] text-[#333] max-w-3xl mx-auto">

  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <i data-lucide="plus-circle" class="w-8 h-8 text-[#B0452D]"></i>
      <h1 class="text-3xl font-bold text-[#B0452D]">Book Transportation</h1>
    </div>
    <a href="{{ route('frontdesk.transport.index') }}" class="text-[#B0452D] hover:underline">Back to list</a>
  </div>

  <form id="fd-transport-book-form" action="{{ route('frontdesk.transport.store') }}" method="POST" class="space-y-4">
    @csrf

    <div class="relative">
      <label class="font-semibold">Reservation ID</label>
      <input type="text" name="reservation_id" id="fd_reservation_id" value="{{ request('reservation_id') }}" placeholder="e.g., HRES100001"
             class="w-full border rounded-lg px-3 py-2" autocomplete="off" />
      <div id="resvStatus" class="text-xs mt-1"></div>
      <div id="resvPreview" class="text-[12px] mt-1 hidden"></div>
      <div id="resvSuggestions" class="absolute z-10 mt-1 w-full bg-white border border-amber-200 rounded-lg shadow hidden max-h-56 overflow-auto"></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="font-semibold">Transport Type</label>
        <select name="transport_type" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-300">
          <option value="">Select Type</option>
          <option>Shuttle</option>
          <option>Pickup</option>
        </select>
      </div>

      <div>
        <label class="font-semibold">Passenger Type</label>
        <select name="passenger_type" id="passenger_type" class="w-full border rounded-lg px-3 py-2">
          <option value="">Select</option>
          <option>Single</option>
          <option>Double</option>
          <option>Group</option>
        </select>
      </div>

      <div id="group_qty_wrap" class="hidden">
        <label class="font-semibold">Group Quantity</label>
        <input type="number" name="group_quantity" min="1" class="w-full border rounded-lg px-3 py-2" />
      </div>

      <div>
        <label class="font-semibold">Pickup Date</label>
        <input type="date" id="pickup_date" name="pickup_date" class="w-full border rounded-lg px-3 py-2" />
      </div>

      <div>
        <label class="font-semibold">Pickup Time</label>
        <input type="time" id="pickup_time" name="pickup_time" class="w-full border rounded-lg px-3 py-2" />
        <input type="hidden" name="pickup_eta" id="pickup_eta" />
      </div>
    </div>

    <div x-data="{ luggage: { suitcase:{selected:false,qty:1}, backpack:{selected:false,qty:1}, duffel:{selected:false,qty:1}, handcarry:{selected:false,qty:1} } }">
      <label class="font-semibold block mb-3 text-[#B0452D]">Luggage Details</label>
      <div class="space-y-2">
        <template x-for="item in ['suitcase','backpack','duffel','handcarry']" :key="item">
          <div class="flex items-center justify-between border border-amber-200 rounded-lg px-3 py-2 bg-white/70">
            <label class="flex items-center gap-2">
              <input type="checkbox" :name="`luggage[${item}][selected]`" x-model="luggage[item].selected" class="w-4 h-4 text-[#B0452D] rounded" />
              <span class="text-sm font-medium" x-text="item.charAt(0).toUpperCase()+item.slice(1)"></span>
            </label>
            <div class="flex items-center gap-1">
              <input type="number" min="1" class="w-20 border rounded-lg px-2 py-1 text-center" :disabled="!luggage[item].selected" :name="`luggage[${item}][qty]`" x-model="luggage[item].qty" />
              <span class="text-xs text-gray-500">pcs</span>
            </div>
          </div>
        </template>
      </div>
    </div>

    <div x-data="pickupForm()" class="space-y-4 mt-3">
      <div>
        <label class="block text-gray-600 font-medium">Location Type</label>
        <select x-model="locationType" name="location_type" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-300">
          <option value="">Select Type</option>
          <option value="airport">Airport</option>
          <option value="building">Building / Establishment</option>
          <option value="specific">Specific Address</option>
          <option value="mylocation">Use My Location</option>
        </select>
      </div>

      <div x-show="locationType === 'airport'" x-transition>
        <label class="font-semibold block mb-2">Pickup Location (Airport)</label>
        <select id="pickup_location" name="pickup_location" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-300">
          <option value="">Select Airport</option>
          <option value="naia">NAIA Terminal 3 (Manila)</option>
          <option value="clark">Clark International Airport (Pampanga)</option>
          <option value="laoag">Laoag International Airport (Ilocos Norte)</option>
          <option value="cauayan">Cauayan Airport (Isabela)</option>
          <option value="tuguegarao">Tuguegarao Airport (Cagayan)</option>
        </select>
      </div>

      <div x-show="locationType === 'building'" x-transition>
        <label class="block text-gray-600 font-medium">Building / Establishment</label>
        <input type="text" name="building_name" placeholder="e.g., SM City Cabanatuan" class="w-full border rounded-lg px-3 py-2" />
      </div>

      <div x-show="locationType === 'specific'" x-transition class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div><label class="text-sm text-gray-600">Street</label><input type="text" name="street" class="w-full border rounded-lg px-3 py-2" /></div>
        <div><label class="text-sm text-gray-600">Barangay</label><input type="text" name="barangay" class="w-full border rounded-lg px-3 py-2" /></div>
        <div><label class="text-sm text-gray-600">City</label><input type="text" name="city" class="w-full border rounded-lg px-3 py-2" /></div>
        <div><label class="text-sm text-gray-600">Province</label><input type="text" name="province" class="w-full border rounded-lg px-3 py-2" /></div>
        <div><label class="text-sm text-gray-600">Region</label><input type="text" name="region" class="w-full border rounded-lg px-3 py-2" /></div>
      </div>

      <div>
        <label class="font-semibold block mb-1">Pick Location on Map</label>
        <div id="transportMap" class="h-64 rounded-lg border border-amber-200 overflow-hidden"></div>
        <div class="text-xs text-gray-600 mt-1">Lat, Lng: <span id="coordDisplay">-</span></div>
      </div>
    </div>

    <!-- ✅ Hidden inputs for map coordinates -->
    <input type="hidden" name="pickup_lat" id="pickup_lat">
    <input type="hidden" name="pickup_lng" id="pickup_lng">

    <div class="flex justify-end gap-3 pt-2">
      <button type="submit" class="px-4 py-2 bg-[#D56B2E] text-white rounded-lg hover:bg-[#B0452D] transition">
        Create Service
      </button>
    </div>
  </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('fd-transport-book-form');
  const resvInput = document.getElementById('fd_reservation_id');
  const sugg = document.getElementById('resvSuggestions');
  const status = document.getElementById('resvStatus');
  const preview = document.getElementById('resvPreview');
  const searchUrl = '{{ route('frontdesk.reservations.search') }}';
  const dateInput = document.getElementById('pickup_date');
  const timeInput = document.getElementById('pickup_time');
  const pickupEta = document.getElementById('pickup_eta');
  let tmr;

  const clearSuggest = () => { if (sugg){ sugg.innerHTML = ''; sugg.classList.add('hidden'); } };
  const setStatus = (ok, text) => {
    if (!status) return;
    status.textContent = text || (ok ? 'Reservation found' : 'Reservation not found');
    status.className = 'text-xs mt-1 ' + (ok ? 'text-green-700' : 'text-red-600');
  };
  const hidePreview = () => { if (preview){ preview.classList.add('hidden'); preview.innerHTML=''; } };
  const formatDate = (d) => {
    if (!d) return '';
    try { const dt = new Date(d); return dt.toLocaleDateString('en-PH', { month:'short', day:'numeric' }); } catch { return d; }
  };
  const shortName = (first, last) => {
    const f = (first||'').trim(); const l = (last||'').trim();
    if (!f && !l) return 'Guest';
    const li = l ? (l[0].toUpperCase()+l.slice(1)) : '';
    const fi = f ? (f[0].toUpperCase()+'.') : '';
    return li + (fi ? ', '+fi : '');
  };
  const showPreview = (item) => {
    if (!preview) return;
    const dateStr = [formatDate(item.checkin_date), formatDate(item.checkout_date)].filter(Boolean).join('–');
    preview.innerHTML = `
      <span class="font-semibold text-[#B0452D]">${item.reservation_id}</span>
      <span class="mx-1 text-gray-400">•</span>
      <span>${shortName(item.first_name, item.last_name)}</span>
      <span class="mx-1 text-gray-400">•</span>
      <span>Rm ${item.room_number ?? 'N/A'}</span>
      ${dateStr ? `<span class="mx-1 text-gray-400">•</span><span>${dateStr}</span>` : ''}
    `;
    preview.classList.remove('hidden');
  };
  const pick = (item) => {
    if (!resvInput) return;
    resvInput.value = item.reservation_id;
    clearSuggest();
    setStatus(true, 'Reservation found');
    showPreview(item);
    if (dateInput && item.checkin_date) {
      try { dateInput.value = String(item.checkin_date).slice(0,10); } catch {}
      updatePickup();
    }
  };
  const render = (items) => {
    if (!sugg) return;
    if (!items || items.length === 0) { clearSuggest(); setStatus(false, 'Reservation not found'); hidePreview(); return; }
    sugg.innerHTML = items.map(i => `
      <button type="button" class="w-full text-left px-3 py-2 hover:bg-amber-50 border-b last:border-b-0 flex justify-between items-center">
        <span class="font-medium">${i.reservation_id}</span>
        <span class="text-xs text-gray-600">${shortName(i.first_name,i.last_name)} • Rm ${(i.room_number||'N/A')}</span>
      </button>
    `).join('');
    Array.from(sugg.querySelectorAll('button')).forEach((btn, idx) => {
      btn.addEventListener('click', () => pick(items[idx]));
    });
    sugg.classList.remove('hidden');
    hidePreview();
  };
  const fetchSearch = async (q) => {
    try {
      const resp = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } });
      if (!resp.ok) throw new Error('Network error');
      const data = await resp.json();
      render(Array.isArray(data) ? data : []);
    } catch(_e){ clearSuggest(); }
  };
  if (resvInput) {
    resvInput.addEventListener('input', () => {
      clearTimeout(tmr);
      const q = resvInput.value.trim();
      if (q.length < 3) { clearSuggest(); if(status) status.textContent=''; return; }
      tmr = setTimeout(() => fetchSearch(q), 250);
    });
    resvInput.addEventListener('blur', () => setTimeout(clearSuggest, 200));
  }

  const updatePickup = () => {
    if (!dateInput || !timeInput || !pickupEta) return;
    const d = (dateInput.value || '').trim();
    const t = (timeInput.value || '').trim();
    pickupEta.value = (d && t) ? `${d}T${t}` : '';
  };
  if (dateInput) dateInput.addEventListener('change', updatePickup);
  if (timeInput) timeInput.addEventListener('change', updatePickup);

  const sel = document.getElementById('passenger_type');
  const wrap = document.getElementById('group_qty_wrap');
  const toggle = () => wrap.classList.toggle('hidden', sel.value !== 'Group');
  sel.addEventListener('change', toggle); toggle();

  form.addEventListener('submit', function(e){
    updatePickup();
    const t = (form.transport_type?.value || '').trim();
    const p = (form.passenger_type?.value || '').trim();
    const d = (form.pickup_eta?.value || '').trim();
    const dd = (form.pickup_date?.value || '').trim();
    const tt = (form.pickup_time?.value || '').trim();
    if (!t || !p || !(d || (dd && tt))) {
      e.preventDefault();
      window.toast && window.toast({ icon: 'error', title: 'Please complete required fields (date and time).' });
    }
  });

  // ============================
  // LEAFLET MAP + COORDINATES
  // ============================
  const defaultCenter = [15.4264, 120.9383];
  const map = L.map('transportMap').setView(defaultCenter, 7);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

  const latInput = document.getElementById('pickup_lat');
  const lngInput = document.getElementById('pickup_lng');
  const coordDisplay = document.getElementById('coordDisplay');
  let marker = null;

  const setPoint = (latlng) => {
    if (!marker) {
      marker = L.marker(latlng, { draggable: true }).addTo(map);
      marker.on('dragend', () => setPoint(marker.getLatLng()));
    } else {
      marker.setLatLng(latlng);
    }
    latInput.value = latlng.lat.toFixed(6);
    lngInput.value = latlng.lng.toFixed(6);
    coordDisplay.textContent = `${latInput.value}, ${lngInput.value}`;
  };

  map.on('click', (e) => setPoint(e.latlng));

  const airports = { naia:[14.5097,121.0198], clark:[15.1856,120.5600], laoag:[18.1781,120.5310], cauayan:[16.9299,121.7533], tuguegarao:[17.6434,121.7332] };
  const pickupSel = document.getElementById('pickup_location');
  if (pickupSel) {
    pickupSel.addEventListener('change', (e) => {
      const key = e.target.value;
      if (airports[key]) {
        const latlng = { lat: airports[key][0], lng: airports[key][1] };
        map.setView(latlng, 13);
        setPoint(latlng);
      }
    });
  }

  // Geolocation (optional)
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude };
        map.setView(latlng, 14);
        setPoint(latlng);
      },
      () => console.warn('Cannot get current location'),
      { enableHighAccuracy: true, timeout: 8000 }
    );
  }
});

function pickupForm(){ return { locationType: '' }; }
</script>
@endsection
