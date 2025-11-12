@extends('layout.app')

@section('title', 'Reservation Details')

@section('content')
<main 
    x-data 
    class="mt-0 p-5 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-lg shadow-lg border border-amber-200 text-[13px] font-[Poppins] text-[#333]"
>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
            <i data-lucide="clipboard-list" class="w-5 h-5 text-amber-600"></i>
            Reservation — {{ $reservation->guest_name }}
        </h1>
        <a href="{{ route(name: 'hotelmanager.booking') }}" 
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
                    <p><strong>Check-In:</strong> {{ \Carbon\Carbon::parse($reservation->checkin_date)->format('M d, Y') }}</p>
                    <p><strong>Check-Out:</strong> {{ \Carbon\Carbon::parse($reservation->checkout_date)->format('M d, Y') }}</p>
                    <p><strong>Nights:</strong> {{ $reservation->total_nights }}</p>
                    <p><strong>Guests:</strong> {{ $reservation->guest_quantity }} Adult(s), {{ $reservation->children }} Child(ren)</p>
                </div>

                <div class="mt-3 border-t pt-3 text-[12px] text-gray-700 space-y-1">
                    <p><strong>Added Amenities:</strong> 
                        {{ !empty($addedAmenityNames) ? implode(', ', $addedAmenityNames) : 'None' }}
                    </p>
                    <p><strong>Special Request:</strong> {{ $reservation->special_request ?? 'None' }}</p>
                </div>

                @if(in_array($reservation->reservation_status, ['Booked', 'Checked In']))
                <div 
                    x-data="{
                        showModal: false,
                        amenities: [],
                        extras: [],
                        save() {
                            const form = $refs.form;
                            form.querySelector('input[name=amenities_json]').value = JSON.stringify(this.amenities);
                            form.querySelector('input[name=extras_json]').value = JSON.stringify(this.extras);
                            form.submit();
                        }
                    }" 
                    class="mt-4 text-[11px]"
                >
                    <!-- 🟨 Single Action Button -->
                    <button 
                        @click="showModal = true"
                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg flex items-center gap-1.5 transition">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Add Amenities / Extras
                    </button>

                    <!-- 🪟 Modal -->
                    <div 
                        x-cloak 
                        x-show="showModal"
                        x-transition
                        class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50"
                    >
                        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
                            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-4">
                                <i data-lucide="plus-circle" class="w-4 h-4 text-amber-500"></i>
                                Add Amenities & Extras
                            </h3>

                            <!-- Form -->
                            <form x-ref="form" method="POST" action="{{ route('frontdesk.booking.saveadditions') }}" class="space-y-5">
                                @csrf
                                <input type="hidden" name="reservation_id" value="{{ $reservation->reservation_id }}">
                                <input type="hidden" name="amenities_json">
                                <input type="hidden" name="extras_json">

                                <!-- 🌿 Amenities -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                        <i data-lucide="bed-double" class="w-3.5 h-3.5 text-green-500"></i>
                                        Available Amenities
                                    </h4>
                                    <div class="space-y-2 text-[12px]">
                                        @foreach(['Extra Pillow' => 100, 'Extra Bed' => 300, 'Breakfast Buffet' => 200, 'Laundry Service' => 150] as $name => $price)
                                            <label class="flex items-center justify-between border border-gray-200 rounded-md p-2 hover:bg-gray-50 cursor-pointer">
                                                <span class="flex items-center gap-2">
                                                    <input 
                                                        type="checkbox"
                                                        @change="
                                                            if ($event.target.checked) {
                                                                amenities.push({ name: '{{ $name }}', price: {{ $price }} });
                                                            } else {
                                                                amenities = amenities.filter(a => a.name !== '{{ $name }}');
                                                            }"
                                                        class="form-checkbox accent-green-500"
                                                    >
                                                    {{ $name }}
                                                </span>
                                                <span class="text-gray-600">₱{{ number_format($price, 2) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <hr class="border-gray-300 my-2">

                                <!-- 📦 Extras -->
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center gap-1">
                                        <i data-lucide="package-plus" class="w-3.5 h-3.5 text-blue-500"></i>
                                        Additional Extras
                                    </h4>
                                    <div class="space-y-2 text-[12px]">
                                        @foreach(['Early Check-In' => ['amount' => 1513, 'qty' => 4, 'total' => 6052], 'Late Checkout' => ['amount' => 800, 'qty' => 1, 'total' => 800], 'Airport Pickup' => ['amount' => 500, 'qty' => 1, 'total' => 500]] as $name => $data)
                                            <label class="flex items-center justify-between border border-gray-200 rounded-md p-2 hover:bg-gray-50 cursor-pointer">
                                                <span class="flex items-center gap-2">
                                                    <input 
                                                        type="checkbox"
                                                        @change="
                                                            if ($event.target.checked) {
                                                                extras.push({ name: '{{ $name }}', amount: {{ $data['amount'] }}, qty: {{ $data['qty'] }}, total: {{ $data['total'] }} });
                                                            } else {
                                                                extras = extras.filter(e => e.name !== '{{ $name }}');
                                                            }"
                                                        class="form-checkbox accent-blue-500"
                                                    >
                                                    {{ $name }}
                                                </span>
                                                <span class="text-gray-600">₱{{ number_format($data['total'], 2) }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- 🧾 Buttons -->
                                <div class="flex justify-end gap-2 pt-3 border-t border-gray-200">
                                    <button type="button" 
                                        @click="showModal = false" 
                                        class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 text-[11px]">
                                        Cancel
                                    </button>
                                    <button type="button"
                                        @click="save();"
                                        class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px]">
                                        Save Selections
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif


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
                    <p><strong>Extra Charges:</strong> ₱{{ number_format($reservation->extra_charge ?? 0, 2) }}</p>

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

                <!-- ✅ Make Payment Modal -->
<!-- ✅ Make Payment Modal -->
@if(strtolower($reservation->payment_status) !== 'full paid')
<div 
  x-data="paymentModal({ 
    netAmount: {{ $reservation->net_amount ?? 0 }},
    roomCharge: {{ $reservation->room_charge ?? 0 }},
    amenitiesCharge: {{ $reservation->amenities_charge ?? 0 }},
    extraCharge: {{ $reservation->extra_charge ?? 0 }},
    nights: {{ $reservation->total_nights ?? 1 }}
  })" 
  class="mt-3"
>

  <!-- 💳 Main Button -->
  <button @click="openModal"
          class="w-full inline-flex justify-center items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg shadow text-[12px] transition">
      <i data-lucide="credit-card" class="w-4 h-4"></i>
      Make Payment
  </button>

  <!-- 🪟 Modal -->
  <div x-cloak x-show="show" x-transition
       class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 relative text-sm font-[Poppins]">

      <!-- Header -->
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-base font-semibold text-[#B0452D] flex items-center gap-2">
          <i data-lucide="credit-card" class="w-5 h-5 text-[#B0452D]"></i>
          Payment & Confirmation
        </h3>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- 🧾 Receipt Overview -->
      <div class="bg-white/80 border border-amber-100 rounded-xl p-4 space-y-1 mb-6">
        <div class="flex justify-between">
          <span class="text-gray-700">Room Fee (<span x-text="nights"></span> nights)</span>
          <span class="font-semibold">₱<span x-text="roomCharge.toFixed(2)"></span></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-700">Added Amenities</span>
          <span class="font-semibold">₱<span x-text="amenitiesCharge.toFixed(2)"></span></span>
        </div>
        <div class="flex justify-between">
          <span class="text-gray-700">Extras</span>
          <span class="font-semibold">₱<span x-text="extraCharge.toFixed(2)"></span></span>
        </div>

        <div class="flex justify-between border-t border-dashed border-amber-200 pt-2">
          <span class="font-semibold text-gray-800">Total Due</span>
          <span class="font-bold text-[#B0452D]">₱<span x-text="netAmount.toFixed(2)"></span></span>
        </div>

        <template x-if="paymentAmount > 0">
          <div class="flex justify-between text-[#D92332]">
            <span>Payment Entered</span>
            <span class="font-semibold">-₱<span x-text="paymentAmount.toFixed(2)"></span></span>
          </div>
        </template>

        <div class="flex justify-between border-t border-dashed border-amber-200 pt-2">
          <span class="font-semibold text-gray-800">Remaining Balance</span>
          <span class="font-bold text-green-700">₱<span x-text="remainingBalance.toFixed(2)"></span></span>
        </div>
      </div>

      <!-- 💰 Payment Form -->
      <form method="POST" action="{{ route('frontdesk.booking.processPayment') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <input type="hidden" name="reservation_id" value="{{ $reservation->reservation_id }}">
        <input type="hidden" name="payment_amount" :value="paymentAmount">
        <input type="hidden" name="amount_tendered" :value="amountTendered">
        <input type="hidden" name="change_due" :value="changeDue">
        <input type="hidden" name="payment_method" :value="paymentMethod">
        <input type="hidden" name="reference_number" :value="reference">
        <input type="hidden" name="transaction_id" :value="transactionId">
        <input type="hidden" name="remaining_balance" :value="remainingBalance">

        <!-- Payment Amount -->
        <div>
          <label class="block text-sm font-semibold text-[#315D43] mb-1">Payment Amount (₱)</label>
          <input type="number" x-model.number="paymentAmount" min="0" @input="updateRemaining"
                 class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:ring-amber-400 focus:border-amber-400"
                 placeholder="Enter payment amount...">
          <p class="text-xs text-gray-500 mt-1">Cannot exceed total due.</p>
        </div>

        <!-- Payment Method -->
        <div>
          <label class="block text-sm font-semibold text-[#315D43] mb-1">Payment Method</label>
          <select x-model="paymentMethod"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
            <option value="">Select Method</option>
            <option value="Cash">Cash</option>
            <option value="GCash">GCash</option>
            <option value="PayMaya">PayMaya</option>
            <option value="Bank Transfer">Bank Transfer</option>
          </select>
        </div>

        <!-- CASH -->
        <template x-if="paymentMethod === 'Cash'">
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-semibold text-[#315D43] mb-1">Amount Tendered (₱)</label>
              <input type="number" x-model.number="amountTendered" @input="calculateChange"
                     class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
              <label class="block text-sm font-semibold text-[#315D43] mb-1">Change Due (₱)</label>
              <input type="number" readonly x-model="changeDue"
                     class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2">
            </div>
          </div>
        </template>

        <!-- DIGITAL / BANK -->
        <template x-if="['GCash','PayMaya','Bank Transfer'].includes(paymentMethod)">
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-semibold text-[#315D43] mb-1">Reference Number</label>
              <input type="text" x-model="reference"
                     class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
              <label class="block text-sm font-semibold text-[#315D43] mb-1">Transaction ID</label>
              <input type="text" x-model="transactionId"
                     class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
            </div>
            <div>
              <label class="block text-sm font-semibold text-[#315D43] mb-1">Proof of Payment</label>
              <input type="file" name="proof"
                     class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg bg-white px-3 py-1.5 file:mr-3 file:px-3 file:py-1.5 file:rounded-md file:border-0 file:bg-[#B0452D] file:text-white file:text-xs hover:file:bg-[#953B26]">
            </div>
          </div>
        </template>

        <!-- Buttons -->
        <div class="flex justify-end gap-2 border-t border-amber-100 pt-4">
          <button type="button" @click="closeModal"
                  class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg text-gray-700 font-semibold text-sm">
            Cancel
          </button>
          <button type="submit"
                  class="px-5 py-2 bg-[#B0452D] hover:bg-[#953B26] text-white rounded-lg font-semibold text-sm">
            Confirm & Submit
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif


            </section>

            <!-- ❌ Cancelled Reservation Info -->
            @if($reservation->reservation_status === 'Cancelled')
            <section class="bg-white p-5 rounded-lg shadow-md border border-red-300">
                <h2 class="flex items-center gap-2 text-base font-semibold mb-3 text-red-600">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                    Cancellation Details
                </h2>
                <div class="text-[12px] text-gray-700 space-y-1">
                    <p><strong>Cancelled On:</strong> {{ \Carbon\Carbon::parse($reservation->cancel_date)->format('M d, Y g:i A') }}</p>
                    <p><strong>Reason List:</strong>
                        @if(!empty($reservation->cancel_list))
                            {{ implode(', ', (array)$reservation->cancel_list) }}
                        @else
                            None
                        @endif
                    </p>
                    <p><strong>Additional Reason:</strong> {{ $reservation->cancel_reason ?? 'None' }}</p>
                </div>
            </section>
            @endif
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
        </div>
    </div>
</main>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('paymentModal', (initial) => ({
    show: false,
    nights: initial.nights,
    roomCharge: initial.roomCharge,
    amenitiesCharge: initial.amenitiesCharge,
    extraCharge: initial.extraCharge,
    netAmount: initial.netAmount,
    remainingBalance: initial.netAmount,

    paymentAmount: 0,
    paymentMethod: '',
    amountTendered: 0,
    changeDue: 0,
    reference: '',
    transactionId: '',

    openModal() { this.show = true },
    closeModal() { this.show = false },

    // 🧮 Update remaining balance when payment input changes
    updateRemaining() {
      const total = this.netAmount;
      this.remainingBalance = Math.max(total - (this.paymentAmount || 0), 0);
    },

    // 💰 Compute change if cash
    calculateChange() {
      const tendered = parseFloat(this.amountTendered) || 0;
      const payment = parseFloat(this.paymentAmount) || 0;
      this.changeDue = Math.max(tendered - payment, 0);
    },
  }));
});
</script>


@endsection
