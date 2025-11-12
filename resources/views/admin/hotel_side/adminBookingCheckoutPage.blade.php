@extends('layout.app')

@section('title', 'Check Out - Reservation Details')

@section('content')

@if($alertMessage)
    <div class="flex items-center gap-2 mb-4 
        {{ $alertType === 'error' ? 'text-red-600 bg-red-50 border-red-200' : 'text-amber-600 bg-amber-50 border-amber-200' }} 
        border px-3 py-2 rounded-md text-[12px]">
        <i data-lucide="{{ $alertType === 'error' ? 'alert-triangle' : 'alert-circle' }}" class="w-4 h-4"></i>
        <span>{{ $alertMessage }}</span>
    </div>
@endif

<main class="mt-0 p-5 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-lg shadow-lg border border-amber-200 text-[13px] font-[Poppins] text-[#333]">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i data-lucide="log-out" class="w-5 h-5 text-amber-600"></i>
            Check-Out — {{ $reservation->guest_name }}
        </h1>
        <a href="{{ route('hotelmanager.booking') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition text-[12px] font-medium">
            Back to Reservations
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <!-- Warning -->
    @if($checkoutExceeded ?? false)
        <div class="flex items-center gap-2 mb-3 text-yellow-800 bg-yellow-50 border border-yellow-300 px-3 py-2 rounded-md text-[12px]">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i>
            <span>Checkout date has passed by {{ $nightsExceeded }} night(s). Extra charges apply: ₱{{ number_format($extraCharge, 2) }}</span>
        </div>
    @endif

    <!-- Main Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start md:items-stretch">

        <!-- LEFT SIDE -->
        <div class="space-y-5">
            <!-- Reservation Details -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-amber-500"></i>
                    Reservation Details
                </h2>

                <div class="grid grid-cols-2 gap-y-2 text-[12px] text-gray-700">
                    <p><strong>ID:</strong> {{ $reservation->reservation_id }}</p>
                    <p><strong>Room #:</strong> {{ $reservation->room_number }}</p>
                    <p><i data-lucide="calendar-plus" class="inline w-3.5 h-3.5 mr-1 text-amber-600"></i><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->checkin_date)->format('M d, Y') }}</p>
                    <p><i data-lucide="calendar-minus" class="inline w-3.5 h-3.5 mr-1 text-amber-600"></i><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->checkout_date)->format('M d, Y') }}</p>
                    <p><strong>Nights:</strong> {{ $reservation->total_nights }}</p>
                    <p><strong>Guests:</strong> {{ $reservation->guest_quantity }} Adult(s), {{ $reservation->children }} Child(ren)</p>
                </div>

                <div class="mt-3 border-t pt-3 text-[12px] text-gray-700 space-y-1">
                    <p><strong>Added Amenities:</strong> {{ !empty($addedAmenityNames) ? implode(', ', $addedAmenityNames) : 'None' }}</p>
                    <p><strong>Special Request:</strong> {{ $reservation->special_request ?? 'None' }}</p>
                </div>
            </section>

            <!-- Guest Info -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="user-round" class="w-5 h-5 text-amber-500"></i>
                    Guest Information
                </h2>
                <div class="space-y-1 text-[12px] text-gray-700">
                    <p><strong>Full Name:</strong> {{ $reservation->guest_name }}</p>
                    <p><strong>Email:</strong> {{ $reservation->email }}</p>
                    <p><strong>Phone:</strong> {{ $reservation->phone }}</p>
                    <p><strong>Address:</strong> {{ $reservation->address }}</p>
                </div>
            </section>

            <!-- Billing & Receipts -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="wallet" class="w-5 h-5 text-amber-500"></i>
                    Billing & Payments
                </h2>

                <div class="text-[12px] text-gray-700 space-y-2">
                    <p><i data-lucide="plus-square" class="inline w-3.5 h-3.5 mr-1 text-amber-600"></i><strong>Added Amenities Fee:</strong> ₱{{ number_format($reservation->added_amenities_fee ?? 0, 2) }}</p>
                    <p><i data-lucide="ticket" class="inline w-3.5 h-3.5 mr-1 text-amber-600"></i><strong>Reservation Fee:</strong> ₱{{ number_format($reservation->reservation_fee ?? 0, 2) }}</p>

                    <hr class="my-2 border-amber-200">

                    <div class="space-y-2">
                        @forelse($receipts as $receipt)
                            <div class="border border-gray-100 rounded-md p-3 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-center">
                                    <p class="font-semibold text-gray-800">
                                        <i data-lucide="receipt" class="inline w-4 h-4 mr-1 text-amber-600"></i>
                                        Receipt: {{ $receipt->receipt_id }}
                                    </p>
                                    <span class="text-[11px] text-gray-500">{{ \Carbon\Carbon::parse($receipt->issued_at)->format('M d, Y h:i A') }}</span>
                                </div>

                                <div class="text-[11px] mt-1.5 space-y-1 text-gray-700">
                                    <p><strong>Payment Method:</strong> {{ $receipt->payment_method ?? 'N/A' }}</p>
                                    <p><strong>Amount Tendered:</strong> ₱{{ number_format($receipt->amount_tendered, 2) }}</p>
                                    <p><strong>Change Due:</strong> ₱{{ number_format($receipt->change_due, 2) }}</p>
                                    <p><strong>Total Billed:</strong> ₱{{ number_format($receipt->total, 2) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 italic">No payments recorded yet for this reservation.</p>
                        @endforelse
                    </div>

                    <hr class="my-2 border-amber-200">

                    <p class="font-semibold text-[13px] text-gray-800 flex justify-between items-center">
                        <span><i data-lucide="coins" class="inline w-4 h-4 mr-1 text-amber-600"></i>Total Amount Due:</span>
                        <span>
                            ₱{{ number_format($reservation->total_amount ?? 0, 2) }}
                            <span class="ml-2 text-[11px] {{ strtolower($reservation->payment_status) === 'paid' ? 'text-green-600' : 'text-red-500' }}">
                                {{ ucfirst($reservation->payment_status) ?? 'Unpaid' }}
                            </span>
                        </span>
                    </p>
                </div>
            </section>
        </div>

        <!-- RIGHT SIDE -->
        <div class="space-y-5 flex flex-col">
            <!-- Room Info -->
            @if($reservation->room)
                <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100">
                    <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                        <i data-lucide="bed" class="w-5 h-5 text-amber-500"></i>
                        Room Information
                    </h2>
                    <div class="grid grid-cols-2 gap-y-1 text-[12px] text-gray-700">
                        <p><strong>Type:</strong> {{ $reservation->room->room_type }}</p>
                        <p><strong>Floor:</strong> {{ $reservation->room->room_floor }}</p>
                        <p><strong>Capacity:</strong> {{ $reservation->room->max_occupancy }}</p>
                        <p><strong>Status:</strong> {{ $roomStatus }}</p>
                    </div>

                    <div class="mt-3 border-t pt-2 space-y-1 text-[12px] text-gray-700">
                        <p><strong>Beds:</strong> {{ implode(', ', $reservation->room->bed_type ?? ['N/A']) }}</p>
                        <p><strong>Amenities:</strong> {{ implode(', ', $reservation->room->room_amenities ?? ['N/A']) }}</p>
                    </div>
                </section>
            @endif

            <!-- Stay Summary -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100 flex-1 flex flex-col justify-between">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="file-text" class="w-5 h-5 text-amber-500"></i>
                    Stay Summary
                </h2>
                <div class="space-y-2 text-[12px] text-gray-700 flex-1">
                    <p><strong>Total Nights Stayed:</strong> {{ $reservation->total_nights }}</p>
                    <p><strong>Reports:</strong> Room cleaned daily, no incidents</p>
                    <p><strong>Feedback:</strong> Excellent service, comfortable stay</p>
                </div>
            </section>
        </div>
    </div>

    <!-- Confirm Check-Out -->
    <form method="POST" action="{{ route('hotelmanager.booking.checkout') }}" class="mt-6 flex justify-center">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation->reservation_id }}">
        <input type="hidden" name="room_number" value="{{ $reservation->room_number }}">
        <button type="submit"
            class="flex items-center justify-center gap-2 w-full md:w-1/3 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-lg shadow transition disabled:opacity-50 disabled:cursor-not-allowed"
            {{ (!$canCheckout) ? 'disabled' : '' }}>
            <i data-lucide="log-out" class="w-4 h-4"></i>
            Confirm Check-Out
        </button>
    </form>
</main>
@endsection
