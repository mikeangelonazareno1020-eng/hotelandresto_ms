@extends('layout.app')

@section('title', 'Hotel Room Status')

@section('content')
<main 
    x-data="roomStatus({{ $rooms->toJson() }})"
    x-init="init()"
    class="mt-0 p-6 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] 
           rounded-lg shadow-lg border border-amber-200 transition-all duration-300 text-xs font-[Poppins]"
>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-5">
        <div>
            <h1 class="text-xl font-bold text-[#B0452D]">Room Status</h1>
            <p class="text-[11px] text-gray-600">Monitor rooms by floor and status</p>
        </div>
        <p class="text-[11px] text-gray-500 mt-1 md:mt-0">
            Showing <span x-text="filteredRooms.length"></span> of {{ $rooms->count() }} rooms
        </p>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col md:flex-row md:items-center md:space-x-3 mb-5 space-y-2 md:space-y-0">
        <div class="relative w-full md:w-1/3">
            <i data-lucide="search" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
            <input type="text" x-model="search"
                placeholder="Search room number or type..."
                class="pl-9 pr-3 py-1.5 border border-gray-300 rounded-lg 
                       focus:ring-2 focus:ring-amber-400 w-full text-xs">
        </div>

        <select x-model="filterFloor"
                class="border border-gray-300 rounded-lg py-1.5 px-3 text-xs focus:ring-amber-400 focus:border-amber-400">
            <option value="All">All Floors</option>
            <template x-for="f in floors" :key="f">
                <option :value="f" x-text="'Floor ' + f"></option>
            </template>
        </select>

        <select x-model="filterType"
                class="border border-gray-300 rounded-lg py-1.5 px-3 text-xs focus:ring-amber-400 focus:border-amber-400">
            <option value="All">All Types</option>
            <template x-for="t in types" :key="t">
                <option :value="t" x-text="t"></option>
            </template>
        </select>
    </div>

    <!-- Tabs -->
    <div class="flex flex-wrap items-center gap-1 md:gap-2 mb-6 text-[11px]">
        <template x-for="tab in tabs" :key="tab">
            <button 
                @click="activeTab = tab"
                :class="{
                    'bg-[#B0452D] text-white': activeTab === tab,
                    'bg-white text-gray-700 hover:bg-gray-100': activeTab !== tab
                }"
                class="px-3 py-1.5 rounded-lg border border-gray-200 font-medium transition"
                x-text="tab"
            ></button>
        </template>
    </div>

    <!-- 🏨 Room Grid -->
    <template x-for="floor in groupedFloors()" :key="floor">
        <div>
            <h2 class="font-semibold text-gray-700 mb-2 text-sm">Floor <span x-text="floor"></span></h2>
            <hr class="border-gray-300 mb-4">

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="room in filteredRooms.filter(r => r.room_floor == floor)" :key="room.room_number">
                    <div 
                        @click="selectRoom(room)"
                        class="cursor-pointer bg-white p-4 rounded-lg shadow-sm border-l-4 hover:shadow-md transition"
                        :class="borderColor(room.room_status)"
                    >
                        <div class="flex items-center space-x-2">
                            <i data-lucide="bed" class="w-4 h-4 text-gray-600"></i>
                            <h2 class="font-semibold text-sm text-gray-800" 
                                x-text="'Room ' + room.room_number"></h2>
                        </div>
                        <p class="text-[11px] text-gray-500" 
                           x-text="room.room_type + ' • Floor ' + room.room_floor"></p>
                        <div class="text-[11px] mt-1">
                            <span :class="textColor(room.room_status)" 
                                  x-text="formatStatus(room.room_status)"></span>
                            <span class="ml-1 text-gray-400" 
                                  x-text="room.room_operation ? '(' + capitalize(room.room_operation) + ')' : ''"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- 🧾 Room Detail Modal -->
<div 
    x-cloak 
    x-show="showModal"
    x-transition.opacity
    class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center"
>
    <div 
        x-show="selectedRoom"
        x-transition.scale
        @click.stop
        class="relative bg-white rounded-xl shadow-xl w-full max-w-lg p-6 text-[13px] font-[Poppins] overflow-y-auto max-h-[90vh]"
    >
        <!-- ❌ Close -->
        <button @click="closeModal"
            class="absolute top-3 right-3 text-gray-500 hover:text-gray-700 transition">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>

        <!-- 🏷 Header -->
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i data-lucide="door-open" class="w-5 h-5 text-[#B0452D]"></i>
                    <span x-text="'Room ' + (selectedRoom.room_number ?? 'N/A')"></span>
                </h2>
                <p class="text-[12px] text-gray-500" 
                   x-text="selectedRoom.room_type ?? 'None'"></p>
            </div>

            <span class="px-2 py-1 text-[10px] rounded-full font-medium"
                  :class="{
                    'bg-green-100 text-green-700': formatStatus(selectedRoom.room_status) === 'Vacant',
                    'bg-amber-100 text-amber-700': formatStatus(selectedRoom.room_status) === 'Booked',
                    'bg-blue-100 text-blue-700': formatStatus(selectedRoom.room_status) === 'Checked In',
                    'bg-gray-100 text-gray-700': formatStatus(selectedRoom.room_status) === 'Checked Out',
                    'bg-red-100 text-red-700': formatStatus(selectedRoom.room_status) === 'Out of Service'
                  }"
                  x-text="formatStatus(selectedRoom.room_status) || 'None'">
            </span>
        </div>

        <!-- 🖼 Room Image -->
        <div class="w-full h-40 bg-gray-100 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
            <img x-show="selectedRoom.room_image" :src="selectedRoom.room_image" 
                 alt="Room Image" class="w-full h-full object-cover">
            <div x-show="!selectedRoom.room_image" class="flex flex-col items-center text-gray-400">
                <i data-lucide="image" class="w-10 h-10"></i>
                <p class="text-[11px] mt-1">No image</p>
            </div>
        </div>

        <!-- 🛏 Info -->
        <div>
            <h3 class="flex items-center gap-2 text-base font-semibold text-[#B0452D] mb-3">
                <i data-lucide="bed" class="w-5 h-5 text-amber-500"></i> Room Information
            </h3>
            <div class="grid grid-cols-2 gap-2 text-[12px] text-gray-700">
                <template x-for="[label, value] in Object.entries({
                    'Type': selectedRoom.room_type,
                    'Floor': selectedRoom.room_floor,
                    'Capacity': selectedRoom.max_occupancy,
                    'Beds': selectedRoom.bed_type,
                    'Dining': selectedRoom.dining_table,
                    'Bathroom': selectedRoom.bathroom,
                    'Kitchen': selectedRoom.kitchen,
                    'Amenities': selectedRoom.room_amenities
                })" :key="label">
                    <p>
                        <strong x-text="label + ':'"></strong>
                        <span x-text="value ?? 'None'"></span>
                    </p>
                </template>
            </div>
        </div>

        <!-- 📅 Reservation -->
        <div class="mt-6 border-t pt-4">
            <h3 class="flex items-center gap-2 text-base font-semibold text-[#B0452D] mb-2">
                <i data-lucide="calendar-check" class="w-5 h-5 text-green-600"></i>
                Current Reservation
            </h3>

            <template x-if="selectedRoom.current_reservation">
                <div class="space-y-1 text-[12px] text-gray-700">
                    <p><strong>ID:</strong> <span x-text="selectedRoom.current_reservation.reservation_id"></span></p>
                    <p><strong>Status:</strong> <span x-text="selectedRoom.current_reservation.status"></span></p>
                    <p><strong>Guest:</strong> <span x-text="selectedRoom.current_reservation.guest_name"></span></p>
                    <p><strong>Check-In:</strong> <span x-text="formatDate(selectedRoom.current_reservation.checkin_date)"></span></p>
                    <p><strong>Check-Out:</strong> <span x-text="formatDate(selectedRoom.current_reservation.checkout_date)"></span></p>
                </div>
            </template>

            <template x-if="!selectedRoom.current_reservation">
                <p class="text-[12px] text-gray-400 italic">No active reservation.</p>
            </template>
        </div>

        <!-- ✅ Make Vacant -->
        <template x-if="['Checked Out'].includes(formatStatus(selectedRoom.room_status))">
            <button 
                @click="markRoom('Vacant')" 
                class="px-4 py-2 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-[12px] font-medium flex items-center gap-1 transition">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                Make Vacant
            </button>
        </template>
    </div>
</div>

</main>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('roomStatus', (rooms) => ({
        rooms,
        selectedRoom: null,
        showModal: false,
        search: '',
        activeTab: 'All',
        filterFloor: 'All',
        filterType: 'All',
        tabs: ['All', 'Vacant', 'Booked', 'Checked In', 'Checked Out', 'Out of Service'],

        init() { if (window.lucide) lucide.createIcons(); },

        selectRoom(room) {
            this.selectedRoom = room;
            this.showModal = true;
            this.$nextTick(() => { if (window.lucide) lucide.createIcons(); });
        },
        closeModal() {
            this.showModal = false;
            this.selectedRoom = null;
        },
        markRoom(status) {
            if (!this.selectedRoom) return;

            Swal.fire({
                title: 'Confirm Action',
                text: `Are you sure you want to mark Room ${this.selectedRoom.room_number} as ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#B0452D',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, update it!'
            }).then(result => {
                if (result.isConfirmed) {
                    const url = `{{ route('frontdesk.rooms.updateStatus', ':room_number') }}`.replace(':room_number', this.selectedRoom.room_number);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ room_status: status })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Updated!', data.message, 'success');
                            this.selectedRoom.room_status = status;
                            this.showModal = false;
                            if (window.lucide) lucide.createIcons();
                        } else {
                            Swal.fire('Error', data.message || 'Failed to update room.', 'error');
                        }
                    })
                    .catch(() => Swal.fire('Error', 'Something went wrong.', 'error'));
                }
            });
        },

        formatDate(date) {
            if (!date) return 'None';
            const d = new Date(date);
            return isNaN(d) ? 'None' : d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        formatStatus(s) {
            if (!s) return 'Unknown';
            s = s.toLowerCase();
            if (s === 'checked in' || s === 'checkin') return 'Checked In';
            if (s === 'checked out' || s === 'checkout') return 'Checked Out';
            return s.charAt(0).toUpperCase() + s.slice(1);
        },

        borderColor(status) {
            const map = {
                'Vacant': 'border-green-500',
                'Checked In': 'border-blue-500',
                'Checked Out': 'border-gray-500',
                'Booked': 'border-amber-500',
                'Out of Service': 'border-red-500'
            };
            return map[this.formatStatus(status)] || 'border-gray-300';
        },

        textColor(status) {
            const map = {
                'Vacant': 'text-green-600',
                'Checked In': 'text-blue-600',
                'Checked Out': 'text-gray-600',
                'Booked': 'text-amber-600',
                'Out of Service': 'text-red-600'
            };
            return map[this.formatStatus(status)] || 'text-gray-500';
        },

        capitalize(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; },

        get floors() { return [...new Set(this.rooms.map(r => r.room_floor))].sort((a, b) => a - b); },

        get types() { return [...new Set(this.rooms.map(r => r.room_type))].filter(Boolean); },

        get filteredRooms() {
            return this.rooms.filter(r => {
                const matchStatus = this.activeTab === 'All' || this.formatStatus(r.room_status) === this.activeTab;
                const matchFloor = this.filterFloor === 'All' || r.room_floor == this.filterFloor;
                const matchType = this.filterType === 'All' || r.room_type === this.filterType;
                const term = this.search.toLowerCase();
                const matchSearch = r.room_number.toString().includes(term) ||
                    (r.room_type && r.room_type.toLowerCase().includes(term));
                return matchStatus && matchFloor && matchType && matchSearch;
            });
        },

        groupedFloors() {
            return this.floors.filter(f => this.filteredRooms.some(r => r.room_floor == f));
        }
    }));
});
</script>
@endsection
