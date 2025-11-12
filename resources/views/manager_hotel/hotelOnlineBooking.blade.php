@extends('layout.app')

@section('title', 'Online Bookings')

@section('content')
<main 
    x-data="bookingList()"
    x-init="init()"
    class="p-5 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-lg shadow border border-amber-200 text-xs font-[Poppins] text-[#333]"
>
    <!-- 🔹 Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-bold text-[#B0452D] flex items-center gap-2">
                <i data-lucide="calendar-check" class="w-5 h-5 text-[#B0452D]"></i>
                Online Bookings
            </h1>
            <p class="text-[11px] text-gray-600">Monitor and manage online booking requests</p>
        </div>
        <a href="{{ route('hotelmanager.booking.create') }}"
           class="flex items-center gap-2 bg-[#315D43] text-white px-3 py-1.5 rounded-lg shadow hover:bg-[#264C36] transition text-[12px]">
           <i data-lucide="plus" class="w-4 h-4"></i> Create Reservation
        </a>
    </div>

    <!-- 🔹 Status Summary Cards -->
    @php
        $total = $reservations->count();
        $pending = $reservations->where('reservation_status', 'Pending')->count();
        $confirmed = $reservations->whereIn('reservation_status', ['Booked', 'Checked In'])->count();
        $rejected = $reservations->whereIn('reservation_status', ['Cancelled', 'No Show'])->count();
        $completed = $reservations->where('reservation_status', 'Checked Out')->count();
    @endphp

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 mb-4 text-center text-[11px] font-[Poppins]">
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 shadow-sm flex flex-col items-center">
            <i data-lucide="list-checks" class="w-4 h-4 text-gray-600 mb-1"></i>
            <p class="text-gray-500 text-[10px] font-medium">Total</p>
            <h2 class="text-base font-bold text-gray-800">{{ $total }}</h2>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 shadow-sm flex flex-col items-center">
            <i data-lucide="clock" class="w-4 h-4 text-amber-600 mb-1"></i>
            <p class="text-gray-500 text-[10px] font-medium">Pending</p>
            <h2 class="text-base font-bold text-amber-700">{{ $pending }}</h2>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 shadow-sm flex flex-col items-center">
            <i data-lucide="badge-check" class="w-4 h-4 text-green-600 mb-1"></i>
            <p class="text-gray-500 text-[10px] font-medium">Confirmed</p>
            <h2 class="text-base font-bold text-green-700">{{ $confirmed }}</h2>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 shadow-sm flex flex-col items-center">
            <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mb-1"></i>
            <p class="text-gray-500 text-[10px] font-medium">Rejected</p>
            <h2 class="text-base font-bold text-red-700">{{ $rejected }}</h2>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-2.5 shadow-sm flex flex-col items-center">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-blue-600 mb-1"></i>
            <p class="text-gray-500 text-[10px] font-medium">Completed</p>
            <h2 class="text-base font-bold text-blue-700">{{ $completed }}</h2>
        </div>
    </div>

    <!-- 🔹 Navigation Tabs -->
    <div class="flex gap-4 border-b border-gray-200 text-[12px] font-medium text-gray-600 mb-3">
        <template x-for="tab in tabs" :key="tab">
            <button
                @click="activeTab = tab"
                class="pb-1.5 relative transition"
                :class="{ 'text-[#B0452D] font-semibold': activeTab === tab }"
            >
                <span x-text="tab"></span>
                <span 
                    x-show="activeTab === tab"
                    x-transition
                    class="absolute bottom-0 left-0 w-full h-0.5 bg-[#B0452D] rounded-t-md"
                ></span>
            </button>
        </template>
    </div>

    <!-- 🔹 Search & Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-4">
        <div class="flex items-center gap-2 w-full md:w-1/2">
            <i data-lucide="search" class="w-4 h-4 text-gray-500"></i>
            <input 
                x-model="search"
                type="text" 
                placeholder="Search by Reservation ID or Guest Name..."
                class="w-full border border-gray-300 rounded-lg px-3 py-1.5 text-[12px] focus:ring-amber-400 focus:border-amber-400"
            >
        </div>

        <div class="flex items-center gap-2">
            <select 
                x-model="roomTypeFilter"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-[12px] focus:ring-amber-400 focus:border-amber-400"
            >
                <option value="">All Room Types</option>
                <option value="Standard">Standard</option>
                <option value="Deluxe">Deluxe</option>
                <option value="Suite">Suite</option>
                <option value="Family Suite">Family Suite</option>
                <option value="Presidential Suite">Presidential Suite</option>
            </select>
        </div>
    </div>

    <!-- 🔹 Reservations Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full text-[12px] font-[Poppins]">
            <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 font-semibold">
                <tr>
                    <th class="px-3 py-2 text-left">Reservation ID</th>
                    <th class="px-3 py-2 text-left">Guest</th>
                    <th class="px-3 py-2 text-left">Room</th>
                    <th class="px-3 py-2 text-center">Days</th>
                    <th class="px-3 py-2 text-left">Stay</th>
                    <th class="px-3 py-2 text-left">Status</th>
                    <th class="px-3 py-2 text-left">Payment</th>
                    <th class="px-1 py-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-gray-700">
                @forelse($reservations as $reservation)
                    @php
                        $checkin = \Carbon\Carbon::parse($reservation->checkin_date);
                        $checkout = \Carbon\Carbon::parse($reservation->checkout_date);
                        $days = $checkin->diffInDays($checkout);

                        // Map to UI display statuses for online bookings
                        $displayStatus = match($reservation->reservation_status) {
                            'Pending' => 'Pending',
                            'Booked', 'Checked In' => 'Confirmed',
                            'Checked Out' => 'Completed',
                            'Cancelled', 'No Show' => 'Rejected',
                            default => 'Pending',
                        };
                        $statusColor = match($displayStatus) {
                            'Pending' => 'text-amber-600',
                            'Confirmed' => 'text-green-600',
                            'Rejected' => 'text-red-600',
                            'Completed' => 'text-blue-600',
                            default => 'text-gray-600',
                        };
                    @endphp

                    <tr class="hover:bg-gray-50 transition"
                        x-show="matchesFilter(
                            '{{ $displayStatus }}',
                            '{{ strtolower($reservation->reservation_id . ' ' . $reservation->first_name . ' ' . $reservation->middle_name . ' ' . $reservation->last_name . ' ' . ($reservation->room->room_type ?? '')) }}',
                            '{{ $reservation->room->room_type ?? '' }}'
                        )">

                        <td class="px-3 py-2 font-semibold text-[#315D43]">
                            #{{ $reservation->reservation_id }}
                        </td>

                        <td class="px-3 py-2">
                            {{ $reservation->first_name }} {{ $reservation->middle_name }} {{ $reservation->last_name }}
                        </td>

                        <td class="px-3 py-2">
                            <p class="font-medium text-gray-800">{{ $reservation->room_number }}</p>
                            <p class="text-[11px] text-gray-500">{{ $reservation->room->room_type }}</p>
                        </td>

                        <td class="px-3 py-2 text-center">{{ $days }}</td>

                        <td class="px-3 py-2">
                            {{ $checkin->format('d M') }} → {{ $checkout->format('d M, Y') }}
                        </td>

                        <td class="px-3 py-2 font-semibold {{ $statusColor }}">
                            {{ $displayStatus }}
                        </td>

                        <td class="px-3 py-2">
                            <div class="flex items-center gap-1.5">
                                <div class="w-4 h-4 flex items-center justify-center rounded-full bg-yellow-100 border border-yellow-300 text-yellow-700 text-[11px] font-semibold">
                                    ₱
                                </div>
                                @if(strtolower($reservation->payment_status) === 'full paid')
                                    <span class="text-green-600 font-semibold flex items-center gap-1">
                                        <i data-lucide='arrow-right' class='w-3 h-3'></i> Full Paid
                                    </span>
                                @elseif(strtolower($reservation->payment_status) === 'not full paid')
                                    <span class="text-amber-600 font-semibold flex items-center gap-1">
                                        <i data-lucide='arrow-right' class='w-3 h-3'></i> Not Full Paid
                                    </span>
                                @else
                                    <span class="text-gray-600 font-medium">{{ ucfirst($reservation->payment_status ?? 'N/A') }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-3 py-2">
                            <div class="flex flex-wrap gap-1.5">
                                <a href="{{ route('hotelmanager.booking.detailspage', $reservation->reservation_id) }}"
                               class="inline-flex items-center justify-center text-gray-600 hover:text-[#B0452D] transition">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                                @if($reservation->reservation_status === 'Booked')
                                    <a href="{{ route('hotelmanager.booking.checkinpage', $reservation->reservation_id) }}"
                                       class="px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md text-[11px] flex items-center gap-1.5 transition">
                                        <i data-lucide='log-in' class='w-3.5 h-3.5'></i> Check In
                                    </a>
                                    <a href="{{ route('hotelmanager.booking.cancelpage', $reservation->reservation_id) }}"
                                       class="px-2 py-1 bg-red-400 hover:bg-red-500 text-white rounded-md text-[11px] flex items-center gap-1.5 transition">
                                        <i data-lucide='x-circle' class='w-3.5 h-3.5'></i> Cancel
                                    </a>
                                @elseif($reservation->reservation_status === 'Checked In')
                                    <a href="{{ route('hotelmanager.booking.checkoutpage', $reservation->reservation_id) }}"
                                       class="px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-md text-[11px] flex items-center gap-1.5 transition">
                                        <i data-lucide='log-out' class='w-3.5 h-3.5'></i> Check Out
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-gray-500 py-6 text-sm">No reservations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>

<!-- ✅ Alpine.js Logic -->
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bookingList', () => ({
        search: '',
        roomTypeFilter: '',
        activeTab: 'All',
        tabs: ['All', 'Pending', 'Confirmed', 'Rejected', 'Completed'],

        init() {
            if (window.lucide) lucide.createIcons();
        },

        matchesFilter(status, text, roomType) {
            const term = this.search.toLowerCase();
            const matchesTab = this.activeTab === 'All' || status === this.activeTab;
            const matchesSearch = text.includes(term);
            const matchesRoom = this.roomTypeFilter === '' || roomType === this.roomTypeFilter;
            return matchesTab && matchesSearch && matchesRoom;
        },
    }));
});
</script>
@endsection
