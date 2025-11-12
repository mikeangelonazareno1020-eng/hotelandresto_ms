@extends('layout.app')

@section('title', 'Cancel Reservation - Reason')

@section('content')
<main 
    x-data 
    class="mt-0 p-5 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-lg shadow-lg border border-amber-200 text-[13px] font-[Poppins] text-[#333]"
>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
            Cancel Reservation — {{ $reservation->guest_name }}
        </h1>
        <a href="{{ route('hotelmanager.booking') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition text-[12px] font-medium">
            Back to Reservations
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

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
                    <p><strong>Added Amenities:</strong> 
                        {{ !empty($addedAmenityNames) ? implode(', ', $addedAmenityNames) : 'None' }}
                    </p>
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

            <!-- Billing -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="wallet" class="w-5 h-5 text-amber-500"></i>
                    Billing Details
                </h2>
                <div class="text-[12px] text-gray-700 space-y-1">
                    <p><strong>Added Amenities Fee:</strong> ₱{{ number_format($reservation->added_amenities_fee ?? 0, 2) }}</p>
                    <p><strong>Reservation Fee:</strong> ₱{{ number_format($reservation->reservation_fee ?? 0, 2) }}</p>

                    @if(strtolower($reservation->payment_status ?? '') === 'downpayment')
                        <p><strong>Downpayment:</strong> - ₱{{ number_format($downpaymentAmount, 2) }}</p>
                    @endif

                    <hr class="my-1">
                    <p class="font-semibold text-[13px] flex justify-between items-center">
                        <span>Total Amount:</span>
                        <span>
                            ₱{{ number_format($reservation->total_amount, 2) }}
                            <span class="ml-2 text-[11px] {{ strtolower($reservation->payment_status) === 'paid' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $paymentLabel }}
                            </span>
                        </span>
                    </p>
                </div>
            </section>
        </div>

        <!-- RIGHT SIDE -->
        <div class="space-y-5 flex flex-col">
            @if($reservation->room)
            <!-- Room Info -->
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
            </section>
            @endif

            <!-- Reason for Cancellation -->
            <section class="bg-white p-5 rounded-lg shadow-md border border-gray-100 flex-1 flex flex-col justify-between">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-gray-800">
                    <i data-lucide="info" class="w-5 h-5 text-red-500"></i>
                    Reason for Cancellation
                </h2>

                <form action="{{ route('hotelmanager.booking.cancelbooking') }}" method="POST" class="flex flex-col flex-1">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ $reservation->reservation_id }}">

                    <div class="space-y-2 mb-3 text-[12px] text-gray-700">
                        @foreach(['Change of Plans', 'Found Better Option', 'Financial Reasons', 'Other'] as $reasonlist)
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="cancel_list[]" value="{{ $reasonlist }}" class="form-checkbox">
                                {{ $reasonlist }}
                            </label>
                        @endforeach
                    </div>

                    <textarea 
                        name="cancel_reason" 
                        rows="4" 
                        placeholder="Additional details..." 
                        class="w-full border rounded-md p-2 text-[12px] text-gray-700 mb-3"
                    ></textarea>

                    <button type="submit" 
                            class="px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-[11px] w-full">
                        Submit Cancellation
                    </button>
                </form>
            </section>
        </div>
    </div>
</main>
@endsection
