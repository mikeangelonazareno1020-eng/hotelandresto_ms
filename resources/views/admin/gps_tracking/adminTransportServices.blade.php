@extends('layout.app')

@section('title', 'Transportation Services | Admin')

@section('content')
<div 
    x-data="adminTransportPage({{ json_encode($services) }})" 
    x-init="init()" 
    class="min-h-[calc(100vh-2rem)] bg-linear-to-b from-[#FFF9E6] to-[#FEE934]/40 
           rounded-2xl shadow-lg border border-amber-200 
           p-6 text-sm font-[Poppins] text-[#333]"
>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <i data-lucide="map-pin" class="w-8 h-8 text-[#B0452D]"></i>
            <h1 class="text-3xl font-bold text-[#B0452D]">Transportation Services</h1>
        </div>

        <div class="flex items-center gap-2">
            <input x-model="search" placeholder="Search type, driver, plate" class="border rounded-lg px-3 py-2 text-sm"/>
            <select x-model="status" class="border rounded-lg px-3 py-2 text-sm">
              <option value="all">All Status</option>
              <option>Pending</option>
              <option>Confirmed</option>
              <option>In Transit</option>
              <option>Completed</option>
              <option>Cancelled</option>
            </select>
            <a href="{{ route('admin.gps.services.create') }}" class="ml-2 inline-flex items-center gap-2 bg-[#D56B2E] hover:bg-[#B0452D] text-white px-3 py-2 rounded-lg text-sm">
              <i data-lucide="plus-circle" class="w-4 h-4"></i> Book Transport
            </a>
        </div>
    </div>

    @if(session('success'))
      <div class="mb-4 text-green-700 bg-green-50 border border-green-200 px-3 py-2 rounded">{{ session('success') }}</div>
    @endif

    <!-- List of Services -->
    <template x-if="filtered.length > 0">
        <div class="grid lg:grid-cols-2 gap-5">
            <template x-for="service in filtered" :key="service.id">
                <div class="bg-white rounded-xl shadow border border-amber-100 overflow-hidden transition hover:shadow-md">
                    <div class="bg-[#FEE934]/50 px-4 py-3 flex justify-between items-center border-b border-amber-100">
                        <h3 class="font-semibold text-[#A8432B] flex items-center gap-2">
                            <i data-lucide="car" class="w-5 h-5"></i> 
                            <span x-text="service.transport_type ?? 'N/A'"></span>
                        </h3>
                        <span 
                            class="text-xs font-semibold px-3 py-1 rounded-full"
                            :class="statusColor(service.service_status)">
                            <span x-text="service.service_status"></span>
                        </span>
                    </div>

                    <div class="p-4 space-y-2">
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Reservation:</span><span x-text="service.reservation_id ?? 'Walk-in'"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Passenger:</span><span x-text="service.passenger_type"></span></div>
                        <template x-if="service.passenger_type === 'Group'">
                          <div class="flex justify-between"><span class="font-medium text-gray-700">Group Quantity:</span><span x-text="service.group_quantity"></span></div>
                        </template>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Pickup Location:</span><span x-text="service.pickup_location ?? 'N/A'"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Pickup ETA:</span><span x-text="formatDate(service.pickup_eta)"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Address:</span><span class="text-right text-[13px]" x-text="formatAddress(service)"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Rate:</span><span class="font-semibold text-[#B0452D]" x-text="'₱' + (parseFloat(service.transport_rate||0)).toFixed(2)"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Driver:</span><span x-text="service.driver_name ?? '—'"></span></div>
                        <div class="flex justify-between"><span class="font-medium text-gray-700">Vehicle:</span><span x-text="`${service.vehicle_type ?? '—'} (${service.vehicle_plate ?? '—'})`"></span></div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-amber-100 bg-gray-50 px-4 py-3">
                        <button @click="viewDetails(service)" class="text-[#B0452D] hover:text-[#D56B2E] text-xs font-semibold flex items-center gap-1">
                            <i data-lucide="eye" class="w-4 h-4"></i> View
                        </button>
                        <button @click="markStatus(service, 'Cancelled')" x-show="['Pending','Confirmed'].includes(service.service_status)" class="text-red-500 hover:text-red-700 text-xs font-semibold flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-4 h-4"></i> Cancel
                        </button>
                        <button @click="markStatus(service, 'Completed')" x-show="['In Transit','Confirmed'].includes(service.service_status)" class="text-green-600 hover:text-green-700 text-xs font-semibold flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Complete
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </template>

    <template x-if="filtered.length === 0">
        <div class="text-center text-gray-500 py-12">
            <i data-lucide="car" class="mx-auto w-12 h-12 text-gray-400 mb-3"></i>
            <p>No transportation services found.</p>
        </div>
    </template>
</div>

<!-- Alpine Script -->
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('adminTransportPage', (services) => ({
    services,
    search: '',
    status: 'all',
    get filtered(){
      const q = this.search.trim().toLowerCase();
      return (this.services || []).filter(s => {
        const stOk = this.status === 'all' || s.service_status === this.status;
        if (!q) return stOk;
        const hay = [s.transport_type, s.driver_name, s.vehicle_plate, s.vehicle_type, s.pickup_location, s.reservation_id]
          .map(x=>String(x||''))
          .join(' ')
          .toLowerCase();
        return stOk && hay.includes(q);
      });
    },
    init(){ lucide.createIcons(); },
    formatDate(d){ if(!d) return '—'; const dt = new Date(d); return dt.toLocaleString('en-PH', { dateStyle:'medium', timeStyle:'short' }); },
    formatAddress(s){ return [s.street, s.barangay, s.city, s.province].filter(Boolean).join(', '); },
    statusColor(st){ return { 'Pending':'bg-yellow-100 text-yellow-700', 'Confirmed':'bg-blue-100 text-blue-700', 'In Transit':'bg-amber-200 text-amber-800', 'Completed':'bg-green-100 text-green-700', 'Cancelled':'bg-red-100 text-red-700' }[st] || 'bg-gray-100 text-gray-600'; },
    viewDetails(s){
      Swal.fire({
        title: 'Service Details',
        html: `
          <b>Type:</b> ${s.transport_type}<br>
          <b>Pickup:</b> ${s.pickup_location}<br>
          <b>Driver:</b> ${s.driver_name ?? 'N/A'}<br>
          <b>Status:</b> ${s.service_status}<br>
          <b>Address:</b> ${this.formatAddress(s)}
        `,
        icon: 'info',
        confirmButtonColor: '#B0452D'
      });
    },
    async markStatus(s, to){
      // Placeholder: implement API to persist status if needed
      s.service_status = to;
      lucide.createIcons();
      if (window.toast) window.toast({ icon:'success', title:`Marked as ${to}` });
    }
  }))
});
</script>
@endsection
