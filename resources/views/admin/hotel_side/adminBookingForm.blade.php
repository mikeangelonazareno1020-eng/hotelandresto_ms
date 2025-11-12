@extends('layout.app')

@section('title', 'Room Reservation Form')

@section('content')
<main 
    x-data="reservationForm()" 
    x-init="init()" 
    data-rooms='@json($rooms)' 
    class="mt-0 p-8 bg-[#FFFBEA]/50 backdrop-blur-lg min-h-[calc(100vh-2rem)] rounded-2xl shadow-lg 
           border border-amber-200 transition-all duration-300 font-[Poppins] text-[#333]"
>

    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-[#B0452D] flex items-center gap-2">
                <i data-lucide="bed" class="w-6 h-6 text-[#B0452D]"></i>
                Create Room Reservation
            </h2>
            <p class="text-[11px] text-gray-600 mt-1">Fill out the details below to create a new room reservation.</p>
        </div>
        <a href="{{ route('hotelmanager.booking') }}" 
           class="mt-4 md:mt-0 flex items-center gap-2 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 
                  rounded-lg text-xs font-semibold shadow-sm transition">
           <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Room Reservations
        </a>
    </div>

    <!-- FORM CARD -->
    <div class="bg-white/90 backdrop-blur-md border border-amber-100 rounded-2xl shadow-md p-6">
        <form @submit.prevent="validateAndSubmit" class="space-y-8">
            @csrf

            <!-- 🧍 Guest Information -->
            <section>
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                    <i data-lucide="user" class="w-4 h-4 text-[#B0452D]"></i>
                    Guest Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-semibold text-[#315D43] mb-1">First Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="first_name" 
                                x-model="guest.first_name"
                                placeholder="Enter first name"
                                class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                            <p x-show="errors.first_name" x-text="errors.first_name" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>
                    
                    <!-- Middle Name -->
                    <div>
                        <label class="block text-sm font-semibold text-[#315D43] mb-1">Middle Name (Optional)</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="middle_name" 
                                x-model="guest.middle_name"
                                placeholder="Enter middle name"
                                class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label class="block text-sm font-semibold text-[#315D43] mb-1">Last Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="user-round" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            <input 
                                type="text" 
                                name="last_name" 
                                x-model="guest.last_name"
                                placeholder="Enter last name"
                                class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                            <p x-show="errors.last_name" x-text="errors.last_name" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Email</label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            <input type="email" name="email" x-model="guest.email"
                                   class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                          focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                            <p x-show="errors.email" x-text="errors.email" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Phone</label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            
                            <!-- +63 prefix -->
                            <span class="absolute left-9 top-2.5 text-sm text-gray-500">+63</span>

                            <input 
                                type="text"
                                name="phone"
                                x-model="guest.phone"
                                x-on:input="guest.phone = guest.phone.replace(/[^0-9]/g, '').slice(0, 10)"
                                placeholder="9123456789"
                                class="w-full pl-[4.2rem] rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                    focus:ring-amber-400 focus:border-amber-400 shadow-sm"
                            >

                            <p x-show="errors.phone" x-text="errors.phone" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>


                    <!-- Address -->
                    <div class="md:col-span-3">
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Address</label>
                        <div class="relative">
                            <i data-lucide="map-pin" class="absolute left-3 top-2.5 w-4 h-4 text-gray-400"></i>
                            <input type="text" name="address" x-model="guest.address"
                                   class="w-full pl-9 rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                          focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                            <p x-show="errors.address" x-text="errors.address" class="text-xs text-red-500 mt-1"></p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 🏠 Stay & Room -->
            <section>
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                    <i data-lucide="calendar-days" class="w-4 h-4 text-[#B0452D]"></i>
                    Stay & Room Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-5">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Check-In Date</label>
                        <input type="date" x-model="checkinDate" :min="minCheckinDate"
                               @change="updateCheckoutMin(); updateTotals(); searchRooms()"
                               class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                      focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                        <p x-show="errors.checkinDate" x-text="errors.checkinDate" class="text-xs text-red-500 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Check-Out Date</label>
                        <input type="date" x-model="checkoutDate" :min="minCheckoutDate" @change="updateTotals(); searchRooms()"
                               class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                      focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                        <p x-show="errors.checkoutDate" x-text="errors.checkoutDate" class="text-xs text-red-500 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Total Days</label>
                        <input
                            type="number"
                            :value="total_days"
                            readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-100 text-sm px-3 py-2 shadow-sm"
                        >
                        </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Guest Quantity</label>
                        <input type="number" min="1" x-model="guestQuantity"  @change="searchRooms()"
                               class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                      focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                        <p x-show="errors.guestQuantity" x-text="errors.guestQuantity" class="text-xs text-red-500 mt-1"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold mb-1 text-[#315D43]">Room Type</label>
                        <select x-model="selectedRoomType"
                                class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 
                                       focus:ring-amber-400 focus:border-amber-400 shadow-sm">
                            <option value="" disabled selected>Select Room Type</option>
                            <option>Standard</option>
                            <option>Deluxe</option>
                            <option>Suite</option>
                        </select>
                        <p x-show="errors.selectedRoomType" x-text="errors.selectedRoomType" class="text-xs text-red-500 mt-1"></p>
                    </div>
                </div>

                <!-- Search Button -->
                <div class="mt-5">
                    <button type="button"
                        class="flex items-center gap-2 bg-[#B0452D] hover:bg-[#953B26] text-white px-5 py-2.5 
                               rounded-lg text-sm font-semibold shadow-md transition"
                        @click="searchRooms()">
                        <i data-lucide="search" class="w-4 h-4"></i> Search Rooms
                    </button>
                </div>
            </section>

            <!-- 🛏️ Room Cards -->
            <section x-show="roomsVisible" x-transition>
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                    <i data-lucide="hotel" class="w-4 h-4 text-[#B0452D]"></i>
                    Available Rooms
                </h3>

                <div 
                    class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-5 max-h-[500px] overflow-y-auto pr-2"
                >
                    <template x-for="room in staticRooms" :key="room.number">
                        <div 
                            class="border rounded-xl p-5 shadow-sm hover:shadow-md cursor-pointer transition-all duration-200 space-y-4 
                                backdrop-blur-sm"
                            :class="selectedRoom === room.number ? 'border-green-600 bg-green-50' : 'border-amber-100 bg-white/80'"
                            @click="selectRoom(room.number); updateTotals()"
                        >
                            <!-- 🏠 Room Info -->
                            <div class="grid grid-cols-2 gap-y-1 text-sm text-gray-700">
                                <p><i data-lucide="hash" class="inline w-4 h-4 text-[#B0452D] mr-1"></i>Room #: <span x-text="room.number"></span></p>
                                <p><i data-lucide="home" class="inline w-4 h-4 text-[#B0452D] mr-1"></i>Type: <span x-text="room.type"></span></p>
                                <p><i data-lucide="stairs" class="inline w-4 h-4 text-[#B0452D] mr-1"></i>Floor: <span x-text="room.floor"></span></p>
                                <p><i data-lucide="users" class="inline w-4 h-4 text-[#B0452D] mr-1"></i>Capacity: <span x-text="room.maxOccupancy ?? 'N/A'"></span></p>
                                <p class="col-span-2"><i data-lucide="wallet" class="inline w-4 h-4 text-[#B0452D] mr-1"></i>Rate: ₱<span x-text="room.price"></span></p>
                            </div>

                            <!-- 🛁 Facilities -->
                            <div class="border-t border-amber-100 pt-2 text-xs leading-relaxed text-gray-700 space-y-1">
                                <p><i data-lucide="bed" class="inline w-3.5 h-3.5 text-[#B0452D] mr-1"></i>Beds: <span x-text="formatList(room.beds)"></span></p>
                                <p><i data-lucide="utensils-crossed" class="inline w-3.5 h-3.5 text-[#B0452D] mr-1"></i>Dining: <span x-text="formatList(room.dining)"></span></p>
                                <p><i data-lucide="shower-head" class="inline w-3.5 h-3.5 text-[#B0452D] mr-1"></i>Bathroom: <span x-text="formatList(room.bathroom)"></span></p>
                                <p><i data-lucide="cooking-pot" class="inline w-3.5 h-3.5 text-[#B0452D] mr-1"></i>Kitchen: <span x-text="formatList(room.kitchen)"></span></p>
                            </div>

                            <!-- ✨ Amenities -->
                            <div class="border-t border-amber-100 pt-2">
                                <p class="font-medium text-[#B0452D] flex items-center gap-1 text-sm">
                                    <i data-lucide="sparkles" class="w-4 h-4"></i> Amenities
                                </p>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <template x-for="a in room.amenities">
                                        <span class="px-2 py-1 text-xs bg-amber-100 rounded-full text-[#B0452D] flex items-center gap-1 shadow-sm">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            <span x-text="a"></span>
                                        </span>
                                    </template>
                                    <span x-show="!room.amenities?.length" class="text-gray-400 text-xs">N/A</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <p x-show="errors.selectedRoom" x-text="errors.selectedRoom" class="text-xs text-red-500 mt-1"></p>
                
                <input type="hidden" name="room_number" x-model="selectedRoom">
            </section>


            <!-- 🧩 Added Amenities -->
            <section class="mt-8">
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-[#B0452D]"></i>
                    Added Amenities
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="(amenity, index) in availableAmenities" :key="index">
                        <label 
                            class="flex items-center justify-between border border-amber-100 rounded-lg p-3 bg-white/80 hover:bg-amber-50 
                                transition cursor-pointer shadow-sm hover:shadow-md backdrop-blur-sm"
                        >
                            <div class="flex items-center gap-2">
                                <input type="checkbox" :value="amenity" @change="toggleAmenity(amenity)" 
                                    class="w-4 h-4 text-[#B0452D] rounded focus:ring-[#B0452D]">
                                <span x-text="amenity.name" class="font-medium text-gray-700 text-sm"></span>
                            </div>
                            <span class="text-[#B0452D] font-semibold text-sm">₱<span x-text="amenity.price"></span></span>
                        </label>
                    </template>
                </div>
            </section>

            <!-- 📝 Special Request -->
            <section class="mt-6">
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-2 tracking-wide flex items-center gap-1">
                    <i data-lucide="edit-3" class="w-4 h-4 text-[#B0452D]"></i>
                    Special Request
                </h3>
                <textarea
                    name="special_request"
                    x-model="specialRequest"
                    placeholder="Any special requests (e.g., extra pillows, late check-in)"
                    class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 shadow-sm focus:ring-amber-400 focus:border-amber-400"
                    rows="3"
                ></textarea>
            </section>

            <!-- 💳 Fees & Payment -->
            <section>
                <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                    <i data-lucide="credit-card" class="w-4 h-4 text-[#B0452D]"></i>
                    Fees & Payment
                </h3>

                <div class="flex flex-wrap items-end gap-6">
                    <div class="flex flex-col w-[180px]">
                        <label class="text-xs font-semibold mb-1 text-[#315D43]">Added Amenities Fee (₱)</label>
                        <input type="number" x-model.number="addedAmenitiesFee" name="added_amenities_fee"
                               readonly class="rounded-lg border border-gray-300 bg-gray-100 text-sm px-3 py-2 shadow-sm">
                    </div>

                    <div class="flex flex-col w-[180px]">
                        <label class="text-xs font-semibold mb-1 text-[#315D43]">Reservation Fee (₱)</label>
                        <input type="number" x-model.number="reservationFee" name="reservation_fee"
                               readonly class="rounded-lg border border-gray-300 bg-gray-100 text-sm px-3 py-2 shadow-sm">
                    </div>

                    <div class="flex flex-col w-[180px]">
                        <label class="text-xs font-semibold mb-1 text-[#315D43]">Total Amount (₱)</label>
                        <input type="number" x-model.number="totalAmount" name="total_amount"
                               readonly class="rounded-lg border border-gray-300 bg-gray-100 text-sm px-3 py-2 shadow-sm">
                    </div>
                </div>

            </section>

            <!-- 💳 Payment & Confirmation -->
            <section class="mt-10 border-t border-amber-100 pt-6">
            <h3 class="text-sm font-semibold text-[#B0452D] uppercase mb-3 tracking-wide flex items-center gap-1">
                <i data-lucide="credit-card" class="w-4 h-4 text-[#B0452D]"></i>
                Payment & Confirmation
            </h3>
            <!-- 🧾 RECEIPT OVERVIEW -->
            <div class="bg-white/80 border border-amber-100 rounded-xl p-4 text-sm space-y-1 font-[Poppins] mb-6">
                <div class="flex justify-between">
                <span class="text-gray-700">Room Fee (<span x-text="total_days"></span> nights)</span>
                <span class="font-semibold">₱<span x-text="reservationFee.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between">
                <span class="text-gray-700">Added Amenities</span>
                <span class="font-semibold">₱<span x-text="addedAmenitiesFee.toFixed(2)"></span></span>
                </div>
                <div class="flex justify-between border-t border-dashed border-amber-200 pt-2">
                <span class="text-gray-700">Total Amount</span>
                <span class="font-bold text-[#B0452D]">₱<span x-text="totalAmount.toFixed(2)"></span></span>
                </div>

                <template x-if="paymentAmount > 0">
                <div class="flex justify-between text-[#D92332]">
                    <span>Payment Entered</span>
                    <span class="font-semibold">-₱<span x-text="paymentAmount.toFixed(2)"></span></span>
                </div>
                </template>

                <div class="flex justify-between border-t border-dashed border-amber-200 pt-2">
                <span class="font-semibold text-gray-800">Total Due</span>
                <span class="font-bold text-green-700">₱<span x-text="totalDue.toFixed(2)"></span></span>
                </div>
            </div>

            <!-- 🧾 PAYMENT DETAILS -->
            <div class="space-y-4">
                <h4 class="text-sm font-semibold text-[#315D43] flex items-center gap-1">
                <i data-lucide="wallet" class="w-4 h-4"></i> Enter Payment Details
                </h4>

                <!-- 💰 Payment Amount -->
                <div>
                <label class="block text-sm font-semibold text-[#315D43] mb-1">Payment Amount (₱)</label>
                <input type="number" x-model.number="paymentAmount" min="0" @input="updatePaymentValidation()"
                        class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400"
                        placeholder="Enter customer payment amount">
                <span x-show="paymentAmountError" x-text="paymentAmountError" class="text-xs text-red-500 mt-1 block"></span>
                <p class="text-xs text-gray-500 mt-1">Must be at least ₱1000 and not exceed total.</p>
                </div>

                <!-- 💳 Payment Method -->
                <div>
                <label class="block text-sm font-semibold text-[#315D43] mb-1">Payment Method</label>
                <select x-model="paymentMethod" @change="updatePaymentValidation()"
                        class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
                    <option value="">Select Method</option>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="PayMaya">PayMaya</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
                </div>

                <!-- CASH DETAILS -->
                <template x-if="paymentMethod === 'Cash'">
                <div class="space-y-2 mt-2">
                    <div>
                    <label class="block text-sm font-semibold text-[#315D43] mb-1">Amount Tendered (₱)</label>
                    <input type="number" x-model.number="amountTendered" min="0" @input="calculateChange()"
                            placeholder="Enter amount received"
                            class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
                    <span x-show="amountTenderedError" x-text="amountTenderedError"
                            class="text-xs text-red-500 mt-1 block"></span>
                    </div>

                    <div>
                    <label class="block text-sm font-semibold text-[#315D43] mb-1">Change Due (₱)</label>
                    <input type="number" x-model.number="changeDue" readonly
                            class="w-full rounded-lg border border-gray-300 bg-gray-100 text-sm px-3 py-2 shadow-sm">
                    </div>
                </div>
                </template>

                <!-- DIGITAL / BANK DETAILS -->
                <template x-if="['GCash', 'PayMaya', 'Bank Transfer'].includes(paymentMethod)">
                <div class="space-y-2 mt-2">
                    <div>
                    <label class="block text-sm font-semibold text-[#315D43] mb-1">Reference Number</label>
                    <input type="text" x-model="paymentDetails.reference_number"
                            placeholder="e.g. GCASH12345"
                            class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
                    </div>

                    <div>
                    <label class="block text-sm font-semibold text-[#315D43] mb-1">Transaction ID</label>
                    <input type="text" x-model="paymentDetails.transaction_id"
                            placeholder="e.g. TXN987654"
                            class="w-full rounded-lg border border-gray-300 bg-white text-sm px-3 py-2 focus:ring-amber-400 focus:border-amber-400">
                    </div>

                    <div>
                    <label class="block text-sm font-semibold text-[#315D43] mb-1">Proof of Payment</label>
                    <input type="file" @change="handleScreenshot($event)"
                            class="w-full text-sm text-gray-700 border border-gray-300 rounded-lg bg-white px-3 py-1.5 file:mr-3 file:px-3 file:py-1.5 file:rounded-md file:border-0 file:bg-[#B0452D] file:text-white file:text-xs hover:file:bg-[#953B26]">
                    <p x-show="paymentDetails.screenshot"
                        class="text-xs text-gray-500 mt-1 italic truncate">
                        <i data-lucide="image" class="inline w-3 h-3 mr-1 text-amber-500"></i>
                        <span x-text="paymentDetails.screenshot"></span>
                    </p>
                    </div>
                </div>
                </template>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="flex justify-end gap-3 mt-6 border-t border-amber-100 pt-4">
                <button type="reset"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-200 hover:bg-gray-300 text-gray-800 transition">
                Cancel
                </button>
                <button @click="validateAndSubmit()" type="button"
                        class="px-5 py-2 rounded-lg text-sm font-semibold bg-[#B0452D] hover:bg-[#953B26] text-white shadow transition">
                Confirm & Submit
                </button>
            </div>
            </section>
        </form>
    </div>
</main>

@endsection  

<script>
function reservationForm() {
    return {
        // ==========================
        // 🏨 BASIC RESERVATION DATA
        // ==========================
        checkinDate: '',
        checkoutDate: '',
        total_days: 1,
        isCheckinToday: false,
        selectedRoomType: '',
        guestQuantity: 1,
        selectedRoom: '',
        guest: { first_name: '', middle_name: '', last_name: '', email: '', phone: '', address: '' },

        // ==========================
        // 💰 FEES & TOTALS
        // ==========================
        addedAmenitiesFee: 0,
        reservationFee: 0,
        totalAmount: 0,
        totalDue: 0,        // ✅ live total due
        overallAmount: 0,

        // ==========================
        // 💳 PAYMENT DATA
        // ==========================
        paymentStatus: '',
        paymentMethod: '',
        paymentAmount: 0,
        amountTendered: 0,
        changeDue: 0,
        paymentDetails: {
            reference_number: '',
            transaction_id: '',
            amount: 0,
            screenshot: '',
        },

        // 🧾 ERRORS
        paymentAmountError: '',
        amountTenderedError: '',
        
        errors: {},

        // ==========================
        // 🧺 ROOMS & AMENITIES
        // ==========================
        allRooms: [],
        staticRooms: [],
        roomsVisible: false,
        availableAmenities: [
            { name: 'Extra Pillow', price: 100 },
            { name: 'Extra Bed', price: 300 },
            { name: 'Room Service', price: 250 },
            { name: 'Laundry Service', price: 150 },
            { name: 'Breakfast Buffet', price: 200 },
            { name: 'Mini Bar Access', price: 180 },
        ],
        selectedAmenities: [],

        // ==========================
        // ⚙️ MISCELLANEOUS
        // ==========================
        specialRequest: '',
        showConfirmation: false,

        // ==========================
        // 🚀 INITIALIZATION
        // ==========================
        init() {
            const dbRooms = JSON.parse(this.$el.dataset.rooms || '[]');
            this.allRooms = dbRooms.map(r => ({
                number: r.room_number,
                name: `${r.room_number} - ${r.room_type}`,
                type: r.room_type,
                price: r.room_rate,
                floor: r.room_floor,
                description: r.room_description,
                status: r.room_status,
                operation: r.room_operation,
                beds: Array.isArray(r.bed_type) ? r.bed_type : [r.bed_type].filter(Boolean),
                dining: Array.isArray(r.dining_table) ? r.dining_table : [r.dining_table].filter(Boolean),
                bathroom: Array.isArray(r.bathroom) ? r.bathroom : [r.bathroom].filter(Boolean),
                kitchen: Array.isArray(r.kitchen) ? r.kitchen : [r.kitchen].filter(Boolean),
                maxOccupancy: r.max_occupancy,
                amenities: Array.isArray(r.room_amenities) ? r.room_amenities : [r.room_amenities].filter(Boolean),
                room_reservations: Array.isArray(r.room_reservations)
                    ? r.room_reservations
                    : JSON.parse(r.room_reservations || '[]'),
            }));

            if (!this.checkinDate) this.checkinDate = this.getToday();
            if (!this.checkoutDate) this.checkoutDate = this.addDays(this.checkinDate, 1);

            this.minCheckinDate = this.getToday();
            this.minCheckoutDate = this.addDays(this.checkinDate, 1);
            this.updateTotals();

            this.$nextTick(() => lucide?.createIcons?.());
        },

        // ==========================
        // 📅 DATE UTILITIES
        // ==========================
        getToday() {
            const d = new Date();
            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        },
        addDays(date, days) {
            const d = new Date(date);
            d.setDate(d.getDate() + days);
            return d.toISOString().split('T')[0];
        },
        updateCheckoutMin() {
            if (!this.checkinDate) return;
            const minCheckout = this.addDays(this.checkinDate, 1);
            if (!this.checkoutDate || new Date(this.checkoutDate) <= new Date(this.checkinDate)) {
                this.checkoutDate = minCheckout;
            }
            this.minCheckoutDate = minCheckout;
            this.updateTotals();
        },

        // ==========================
        // 🧺 AMENITIES HANDLING
        // ==========================
        toggleAmenity(a) {
            const i = this.selectedAmenities.findIndex(x => x.name === a.name);
            if (i > -1) this.selectedAmenities.splice(i, 1);
            else this.selectedAmenities.push(a);
            this.addedAmenitiesFee = this.selectedAmenities.reduce((s, x) => s + x.price, 0);
            this.updateTotals();
        },

        // ==========================
        // 💳 PAYMENT LOGIC
        // ==========================
        updatePaymentValidation() {
            this.paymentAmountError = '';
            this.amountTenderedError = '';

            if (['GCash', 'PayMaya', 'Bank Transfer'].includes(this.paymentMethod)) {
                this.paymentAmount = this.totalAmount;
                this.paymentDetails.amount = this.totalAmount;
                this.amountTendered = 0;
                this.changeDue = 0;
            }

            if (this.paymentAmount < 1000) {
                this.paymentAmountError = 'Payment must be at least ₱1000.';
            } else if (this.paymentAmount > this.totalAmount) {
                this.paymentAmountError = 'Payment cannot exceed total amount.';
            }

            this.calculateTotalDue();
        },

        calculateChange() {
            this.amountTenderedError = '';
            const pay = Number(this.paymentAmount) || 0;
            const tendered = Number(this.amountTendered) || 0;

            if (tendered < pay) {
                this.amountTenderedError = 'Amount tendered cannot be less than payment amount.';
                this.changeDue = 0;
            } else {
                this.changeDue = (tendered - pay).toFixed(2);
            }

            this.calculateTotalDue();
        },

        handleScreenshot(e) {
            const f = e.target.files[0];
            if (f) this.paymentDetails.screenshot = f.name;
        },

        calculateTotalDue() {
            this.totalDue = this.totalAmount - this.paymentAmount;
            if (this.totalDue < 0) this.totalDue = 0;
            this.updateReceiptOverview();
        },

        updatePaymentValidation() {
            this.paymentAmountError = '';
            const amt = Number(this.paymentAmount) || 0;
            const total = Number(this.totalAmount) || 0;

            // 💵 If method is Cash, compute change
            if (this.paymentMethod === 'Cash') this.calculateChange();

            // 💰 Recompute total due
            this.calculateTotalDue();
        },


        updateReceiptOverview() {
            if (this.paymentMethod === 'Cash') {
                this.paymentDetails = {
                    amount_tendered: this.amountTendered,
                    change_due: this.changeDue,
                };
            } else if (['GCash', 'PayMaya', 'Bank Transfer'].includes(this.paymentMethod)) {
                this.paymentDetails.amount = this.paymentAmount;
            }
        },

        handlePaymentStatusChange() {
            if (this.paymentStatus === 'Paid') this.downpayment = 0;
            else if (this.paymentStatus === 'Downpayment') this.downpayment = 500;
            this.updateTotals();
        },
        

        // ==========================
        // 💰 TOTAL COMPUTATION
        // ==========================
        updateTotals() {
            if (this.checkinDate && this.checkoutDate) {
                const ci = new Date(this.checkinDate);
                const co = new Date(this.checkoutDate);
                this.total_days = Math.max(1, Math.ceil((co - ci) / (1000 * 60 * 60 * 24)));
                this.isCheckinToday = this.checkinDate === this.getToday();
            }

            let rate = 0;
            if (this.selectedRoom) {
                const sel = this.allRooms.find(r => r.number === this.selectedRoom);
                rate = Number(sel?.price || 0);
            }

            this.reservationFee = this.total_days * rate;
            this.totalAmount = this.reservationFee + this.addedAmenitiesFee;
            this.calculateTotalDue();
        },

        // ==========================
        // 🔍 ROOM SEARCH
        // ==========================
         searchRooms() {
            if (!this.checkinDate || !this.checkoutDate) return;

            const checkin = Date.parse(this.checkinDate);
            const checkout = Date.parse(this.checkoutDate);

            // Step 1: Filter rooms based on date overlap
            let availableRooms = this.allRooms.filter(room => {
                if (!Array.isArray(room.room_reservations) || room.room_reservations.length === 0) {
                    return true; // No reservations, room is available
                }

                // Check if any reservation overlaps (ignore Cancelled)
                for (const res of room.room_reservations) {
                    // ✅ Skip cancelled reservations
                    if (res.status && res.status.toLowerCase() === 'cancelled') continue;

                    if (!res.checkin_date || !res.checkout_date) continue;

                    const resCheckin = Date.parse(res.checkin_date);
                    const resCheckout = Date.parse(res.checkout_date);

                    // Inclusive checkout: any overlap means room is not available
                    if (checkin <= resCheckout && checkout >= resCheckin) {
                        return false; // Overlaps, remove room
                    }
                }

                return true; // No overlaps, room available
            });

            // Step 2: Filter by selected room type and guest quantity
            this.staticRooms = availableRooms.filter(room => {
                const matchType = this.selectedRoomType ? room.type === this.selectedRoomType : true;
                const matchGuest = this.guestQuantity ? (room.maxOccupancy ?? 0) >= this.guestQuantity : true;
                return matchType && matchGuest;
            });

            this.roomsVisible = this.staticRooms.length > 0;

            // Re-create lucide icons if necessary
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },

        selectRoom(num) { this.selectedRoom = num; },

        // ==========================
        // Confirmation Modal Controls
        // ==========================
        openConfirmation() {
            this.showConfirmation = true;
            this.$nextTick(() => lucide?.createIcons?.());
        },
        confirmSubmit() {
            this.showConfirmation = false;
            this.validateAndSubmit();
        },

        // ==========================
        // 🧾 PAYLOAD + SUBMIT
        // ==========================
        payload() {
            return {
                // ==============================
                // 🧍 GUEST INFORMATION
                // ==============================
                first_name: this.guest.first_name?.trim() || '',
                middle_name: this.guest.middle_name?.trim() || '',
                last_name: this.guest.last_name?.trim() || '',
                email: this.guest.email?.trim() || '',
                phone: this.guest.phone?.trim() || '',
                address: this.guest.address?.trim() || '',

                // ==============================
                // 🏨 STAY & ROOM DETAILS
                // ==============================
                checkin_date: this.checkinDate,
                checkout_date: this.checkoutDate,
                total_days: this.total_days,
                guest_quantity: this.guestQuantity,
                selected_room_type: this.selectedRoomType,
                selected_room_number: this.selectedRoom,
                special_request: this.specialRequest?.trim() || '',
                is_checkin_today: this.isCheckinToday,

                // ==============================
                // 🧺 AMENITIES & SERVICES
                // ==============================
                selected_amenities: this.selectedAmenities,
                added_amenities_fee: this.addedAmenitiesFee,

                // ==============================
                // 💰 FEES & TOTALS
                // ==============================
                reservation_fee: this.reservationFee,
                total_amount: this.totalAmount,
                total_due: this.totalDue ,
                overall_amount: this.overallAmount || 0,


                // ==============================
                // 💳 PAYMENT INFORMATION
                // ==============================
                payment_status: this.paymentStatus || 'Pending',
                payment_method: this.paymentMethod || '',
                payment_amount: this.paymentAmount,
                amount_tendered: this.amountTendered,
                change_due: this.changeDue,
                payment_details: {
                    ...this.paymentDetails,
                    amount: this.paymentDetails.amount || this.paymentAmount,
                    reference_number: this.paymentDetails.reference_number || '',
                    transaction_id: this.paymentDetails.transaction_id || '',
                    screenshot: this.paymentDetails.screenshot || '',
                },

                // ==============================
                // 🧾 RESERVATION METADATA
                // ==============================
                reservation_status: this.isCheckinToday ? 'Checked In' : 'Booked',
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString(),

                // ==============================
                // 🧑‍💼 ADMIN / FRONTDESK CONTEXT
                // ==============================
                issued_by: '{{ session("name") ?? "Frontdesk Staff" }}',
                admin_id: '{{ session("adminId") ?? null }}',
                role: '{{ session("user_role") ?? "Frontdesk" }}',
            };
        },


        validateAndSubmit() {
            // 🔄 Reset error objects
            this.errors = {};
            this.paymentAmountError = '';
            this.amountTenderedError = '';

            // ==============================
            // 🧍 GUEST INFORMATION VALIDATION (with Regex)
            // ==============================
            const nameRegex = /^[A-Za-z\s'-]+$/; // allows letters, spaces, apostrophes, and hyphens
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // simple but effective email format check
            const phoneRegex = /^[0-9]{10}$/; // only 10 digits, no symbols

            if (!this.guest.first_name.trim()) {
                this.errors.first_name = 'First name is required.';
            } else if (!nameRegex.test(this.guest.first_name)) {
                this.errors.first_name = 'First name can only contain letters.';
            }

            if (this.guest.middle_name && !nameRegex.test(this.guest.middle_name)) {
                this.errors.middle_name = 'Middle name can only contain letters.';
            }

            if (!this.guest.last_name.trim()) {
                this.errors.last_name = 'Last name is required.';
            } else if (!nameRegex.test(this.guest.last_name)) {
                this.errors.last_name = 'Last name can only contain letters.';
            }

            if (!emailRegex.test(this.guest.email)) {
                this.errors.email = 'Enter a valid email address.';
            }

            if (!this.guest.phone.trim()) {
                this.errors.phone = 'Phone number is required.';
            } else if (!phoneRegex.test(this.guest.phone)) {
                this.errors.phone = 'Phone must be exactly 10 digits (e.g., 9123456789).';
            }

            if (!this.guest.address.trim()) {
                this.errors.address = 'Address is required.';
            } else if (this.guest.address.length < 5) {
                this.errors.address = 'Address must be at least 5 characters.';
            }


            // ==============================
            // 📅 STAY & ROOM VALIDATION
            // ==============================
            if (!this.checkinDate) this.errors.checkinDate = 'Check-in date is required.';
            if (!this.checkoutDate) this.errors.checkoutDate = 'Check-out date is required.';
            if (new Date(this.checkoutDate) <= new Date(this.checkinDate))
                this.errors.checkoutDate = 'Checkout must be after check-in date.';
            if (!this.selectedRoom) this.errors.selectedRoom = 'Please select an available room.';
            if (this.guestQuantity < 1) this.errors.guestQuantity = 'Guest quantity must be at least 1.';

            // ==============================
            // 💳 PAYMENT VALIDATION
            // ==============================
            const amt = Number(this.paymentAmount) || 0;
            const total = Number(this.totalAmount) || 0;
            const tendered = Number(this.amountTendered) || 0;

            if (amt <= 0) {
                this.paymentAmountError = 'Enter a valid payment amount.';
            } else if (amt < 1000) {
                this.paymentAmountError = 'Payment must be at least ₱1000.';
            } else if (amt > total) {
                this.paymentAmountError = 'Payment cannot exceed total amount.';
            }

            if (!this.paymentMethod) this.errors.paymentMethod = 'Select a payment method.';

            if (this.paymentMethod === 'Cash') {
                if (tendered <= 0) {
                    this.amountTenderedError = 'Enter the amount tendered by the customer.';
                } else if (tendered < amt) {
                    this.amountTenderedError = 'Amount tendered cannot be less than payment amount.';
                }
            }

            // ==============================
            // ❌ SHOW INLINE ERRORS
            // ==============================
            if (
                Object.keys(this.errors).length > 0 ||
                this.paymentAmountError ||
                this.amountTenderedError
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'Form Incomplete',
                    text: 'Please correct the highlighted errors before submitting.',
                });
                return;
            }

            // ==============================
            // ✅ SUBMIT FORM
            // ==============================
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('hotelmanager.booking.store') }}";
            form.innerHTML = `@csrf<input type="hidden" name="payload" value='${JSON.stringify(this.payload())}'>`;
            document.body.appendChild(form);
            form.submit();
        },


        // ==========================
        // 🧩 FORMATTERS
        // ==========================
        formatList(list) {
            if (!list || !list.length) return 'N/A';
            return list.map(i => {
                if (typeof i === 'object' && i !== null) {
                    if (i.type && i.quantity !== undefined) return `${i.type} x${i.quantity}`;
                    if (i.tables || i.chairs) return `Tables: ${i.tables ?? 'N/A'}, Chairs: ${i.chairs ?? 'N/A'}`;
                    if (i.type || i.fridge || i.sink || i.stove) {
                        const { type, fridge, sink, stove } = i;
                        return `Type: ${type ?? 'N/A'}, Fridge: ${fridge ?? 'N/A'}, Sink: ${sink ?? 'N/A'}, Stove: ${stove ?? 'N/A'}`;
                    }
                    if (i.bathrooms || i.toilet || i.shower || i.bathtub || i.basin) {
                        const { bathrooms, toilet, shower, bathtub, basin } = i;
                        return `Baths: ${bathrooms ?? 'N/A'}, Toilet: ${toilet ?? 'N/A'}, Shower: ${shower ?? 'N/A'}, Bathtub: ${bathtub ?? 'N/A'}, Basin: ${basin ?? 'N/A'}`;
                    }
                }
                return i;
            }).join(', ');
        },
    };
}
</script>



