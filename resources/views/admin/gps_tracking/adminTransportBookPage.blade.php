@extends('layout.app')

@section('title', 'Book Transport | Admin')

@section('content')
<div class="min-h-[calc(100vh-2rem)] bg-linear-to-b from-[#FFF9E6] to-[#FEE934]/40 rounded-2xl shadow-lg border border-amber-200 p-6 text-sm font-[Poppins] text-[#333] max-w-3xl mx-auto">

  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
      <i data-lucide="plus-circle" class="w-8 h-8 text-[#B0452D]"></i>
      <h1 class="text-3xl font-bold text-[#B0452D]">Book Transportation</h1>
    </div>
    <a href="{{ route('admin.gps.services') }}" class="text-[#B0452D] hover:underline">Back to list</a>
  </div>

  <form id="admin-transport-book-form" action="{{ route('admin.gps.services.store') }}" method="POST" class="space-y-4">
    @csrf

    <div>
      <label class="font-semibold">Reservation ID</label>
      <input type="text" name="reservation_id" value="{{ request('reservation_id') }}" placeholder="e.g., HRES100001"
             class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="font-semibold">Transport Type</label>
        <select name="transport_type" class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-300">
          <option value="">Select Type</option>
          <option>Shuttle</option>
          <option>Pickup</option>
          <option>Rent Car</option>
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
        <label class="font-semibold">Pickup Date & Time</label>
        <input type="datetime-local" name="pickup_eta" class="w-full border rounded-lg px-3 py-2" />
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
        <input type="text" name="building_name" placeholder="e.g., SM City Cabanatuan, Robinsons Place" class="inputField w-full border rounded-lg px-3 py-2" />
      </div>

      <div x-show="locationType === 'specific'" x-transition>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
          <div><label class="block text-gray-600 font-medium">Region</label><select id="region" name="region" class="inputField w-full border rounded-lg px-3 py-2"></select></div>
          <div><label class="block text-gray-600 font-medium">Province</label><select id="province" name="province" class="inputField w-full border rounded-lg px-3 py-2"></select></div>
          <div><label class="block text-gray-600 font-medium">City / Municipality</label><select id="city" name="city" class="inputField w-full border rounded-lg px-3 py-2"></select></div>
          <div><label class="block text-gray-600 font-medium">Barangay</label><select id="barangay" name="barangay" class="inputField w-full border rounded-lg px-3 py-2"></select></div>
        </div>
        <div class="mt-2"><label class="block text-gray-600 font-medium">Street / Landmark</label><input type="text" id="street" name="street" placeholder="e.g., Poblacion St., near Plaza Lucero" class="inputField w-full border rounded-lg px-3 py-2" /></div>
      </div>

      <div x-show="locationType === 'mylocation'" x-transition>
        <div class="text-sm text-gray-700 mt-2">
          Coordinates: <span id="coordDisplay" class="font-semibold text-[#B0452D]">None</span>
          <button type="button" id="useMyLocation" class="mt-2 px-4 py-2 bg-[#FEE934] border border-amber-300 rounded-lg hover:bg-[#FFD600] transition">
            <i data-lucide="map-pin" class="inline w-4 h-4 mr-1"></i> Use My Current Location
          </button>
        </div>
        <input type="hidden" name="pickup_latitude" id="pickup_lat">
        <input type="hidden" name="pickup_longitude" id="pickup_lng">
      </div>
    </div>

    <div class="mt-2">
      <div id="transportMap" class="w-full rounded-xl border border-amber-200 mt-3" style="height: 320px;"></div>
    </div>

    <div>
      <label class="font-semibold">Estimated Rate (₱)</label>
      <input type="number" step="0.01" name="transport_rate" class="w-full border rounded-lg px-3 py-2" />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div><label class="font-semibold">Driver Name</label><input type="text" name="driver_name" class="w-full border rounded-lg px-3 py-2" /></div>
      <div><label class="font-semibold">Vehicle Type</label><input type="text" name="vehicle_type" class="w-full border rounded-lg px-3 py-2" /></div>
      <div><label class="font-semibold">Vehicle Plate</label><input type="text" name="vehicle_plate" class="w-full border rounded-lg px-3 py-2" /></div>
    </div>

    <div class="flex justify-end gap-3 pt-2">
      <button type="submit" class="px-4 py-2 bg-[#D56B2E] text-white rounded-lg hover:bg-[#B0452D] transition">Create Service</button>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('admin-transport-book-form');
  const sel = document.getElementById('passenger_type');
  const wrap = document.getElementById('group_qty_wrap');
  const toggle = () => wrap.classList.toggle('hidden', sel.value !== 'Group');
  sel.addEventListener('change', toggle); toggle();

  form.addEventListener('submit', function(e){
    const t = form.transport_type.value.trim();
    const p = form.passenger_type.value.trim();
    const d = form.pickup_eta.value.trim();
    if (!t || !p || !d) {
      e.preventDefault();
      window.toast && window.toast({ icon: 'error', title: 'Please complete all required fields.' });
    }
  });

  const defaultCenter = [15.4264, 120.9383];
  const map = L.map('transportMap').setView(defaultCenter, 7);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
  let marker = null;
  const latInput = document.getElementById('pickup_lat');
  const lngInput = document.getElementById('pickup_lng');
  const coordDisplay = document.getElementById('coordDisplay');
  const setPoint = (latlng) => {
    if (!marker) { marker = L.marker(latlng, { draggable: true }).addTo(map); marker.on('dragend', () => setPoint(marker.getLatLng())); }
    else marker.setLatLng(latlng);
    latInput.value = latlng.lat.toFixed(6);
    lngInput.value = latlng.lng.toFixed(6);
    if (coordDisplay) coordDisplay.textContent = `${latInput.value}, ${lngInput.value}`;
  };
  map.on('click', (e) => setPoint(e.latlng));

  const useBtn = document.getElementById('useMyLocation');
  if (useBtn && navigator.geolocation) {
    useBtn.addEventListener('click', () => {
      navigator.geolocation.getCurrentPosition(
        (pos) => { const latlng = { lat: pos.coords.latitude, lng: pos.coords.longitude }; map.setView(latlng, 14); setPoint(latlng); window.toast && window.toast({ icon: 'success', title: 'Location set' }); },
        () => window.toast && window.toast({ icon: 'error', title: 'Cannot get location' }),
        { enableHighAccuracy: true, timeout: 8000 }
      );
    });
  }

  const airports = { naia:[14.5097,121.0198], clark:[15.1856,120.5600], laoag:[18.1781,120.5310], cauayan:[16.9299,121.7533], tuguegarao:[17.6434,121.7332] };
  const pickupSel = document.getElementById('pickup_location');
  if (pickupSel) {
    pickupSel.addEventListener('change', (e) => {
      const key = e.target.value; if (airports[key]) { const latlng = { lat: airports[key][0], lng: airports[key][1] }; map.setView(latlng, 13); setPoint(latlng); }
    });
  }

  const region = document.getElementById('region');
  const province = document.getElementById('province');
  const city = document.getElementById('city');
  const barangay = document.getElementById('barangay');
  function setOptions(select, placeholder, data, mapfn) {
    select.innerHTML = `<option value="">${placeholder}</option>`;
    data.forEach(item => { const opt = document.createElement('option'); const o = mapfn(item); opt.value = o.value; opt.textContent = o.label; Object.entries(o.dataset || {}).forEach(([k,v]) => opt.dataset[k]=v); select.appendChild(opt); });
  }
  // Use the same address API as staff management (/staff/api/...)
  fetch("{{ url('staff/api/regions') }}")
    .then(r=>r.json())
    .then(data=>setOptions(region,'Select Region',data,r=>({value:r.region_name,label:r.region_name,dataset:{regioncode:r.region_code}})))
    .catch(()=>{ window.toast && window.toast({ icon:'error', title:'Regions load failed' }); });

  region?.addEventListener('change',()=>{
    const code = region.options[region.selectedIndex]?.dataset.regioncode;
    if(!code) return;
    fetch(`{{ url('staff/api/provinces') }}/${code}`)
      .then(r=>r.json())
      .then(data=>setOptions(province,'Select Province',data,p=>({value:p.province_name,label:p.province_name,dataset:{provincecode:p.province_code}})))
      .catch(()=>{ window.toast && window.toast({ icon:'error', title:'Provinces load failed' }); });
  });

  province?.addEventListener('change',()=>{
    const code = province.options[province.selectedIndex]?.dataset.provincecode;
    if(!code) return;
    fetch(`{{ url('staff/api/cities') }}/${code}`)
      .then(r=>r.json())
      .then(data=>setOptions(city,'Select City / Municipality',data,c=>({value:c.city_name,label:c.city_name,dataset:{citycode:c.city_code}})))
      .catch(()=>{ window.toast && window.toast({ icon:'error', title:'Cities load failed' }); });
  });

  city?.addEventListener('change',()=>{
    const code = city.options[city.selectedIndex]?.dataset.citycode;
    if(!code) return;
    fetch(`{{ url('staff/api/barangays') }}/${code}`)
      .then(r=>r.json())
      .then(data=>setOptions(barangay,'Select Barangay',data,b=>({value:b.brgy_name,label:b.brgy_name,dataset:{brgycode:b.brgy_code}})))
      .catch(()=>{ window.toast && window.toast({ icon:'error', title:'Barangays load failed' }); });
  });

  const street = document.getElementById('street');
  function geocodeAddress(){ const query = [street?.value, barangay?.value, city?.value, province?.value, region?.value, 'Philippines'].filter(Boolean).join(', '); if(!query) return; fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`).then(r=>r.json()).then(data=>{ if(data.length>0){ const {lat,lon}=data[0]; const latlng={lat:parseFloat(lat),lng:parseFloat(lon)}; map.setView(latlng,15); setPoint(latlng); } }); }
  ;[region,province,city,barangay,street].forEach(el=>{ el?.addEventListener('change',geocodeAddress); el?.addEventListener('blur',geocodeAddress); });
});
function pickupForm(){ return { locationType:'', pickup_lat:'', pickup_lng:'', coordsDisplay:'None', } }
</script>
@endsection
