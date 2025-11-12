@extends('layout.app')

@section('title', 'POS | Consuelo Restaurant')

@section('content')
<style>
  /* Hide scrollbars on elements with .no-scrollbar */
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
  .no-scrollbar::-webkit-scrollbar { display: none; }
  /* Slightly tighter grid to fit more items without scrollbars being visible */
</style>
<div 
  class="p-4 bg-linear-to-b from-[#FFF9E6] to-[#FFF2B0]/40 backdrop-blur-lg 
         min-h-[calc(100vh-2rem)] rounded-3xl shadow-xl border border-[#FFD600]/60 
         font-[Poppins] text-[#2E2E2E]"
  x-data="posSystem()" 
  x-init="init()"
>

  <form method="POST" action="{{ route('cashier.order.store') }}" @submit.prevent="submitForm" class="grid lg:grid-cols-[3fr_1fr] gap-3" id="posForm">
    @csrf

    <!-- MENU SECTION -->
    <section class="space-y-3 flex flex-col h-[calc(100vh-4rem)] min-h-0">
      <!-- Header -->
      <div class="text-left mb-1">
        <h1 class="text-3xl mt-6 mb-8 font-extrabold text-[#C45E25] flex justify-center items-center gap-3 drop-shadow-sm">
          <i data-lucide="shopping-bag" class="w-7 h-7 text-[#FFD600]"></i>
          Restaurant Menu
        </h1>
      </div>

      <!-- Search -->
      <!-- Categories + Search (right-aligned search) -->
      <div class="flex items-center gap-3 mt-3 pb-1 border-b border-[#FFD600]/40">
        <!-- Left: categories -->
        <div class="flex gap-2.5 overflow-x-auto pr-2 flex-1">
          <template x-for="c in categories" :key="c">
            <button type="button" @click="selectedCategory = c; if(!search){ filteredMenu = JSON.parse(JSON.stringify(menu)); }"
              :class="selectedCategory === c ? 'bg-[#3B9441] text-white shadow-md border border-[#FFD600]/40' : 'bg-transparent text-[#3B9441] hover:bg-[#FFF2B0]/60 border border-transparent'"
              class="text-sm px-3.5 py-1.5 rounded-full font-semibold transition whitespace-nowrap">
              <span x-text="c"></span>
            </button>
          </template>
        </div>
        <!-- Right: search -->
        <div class="flex items-center gap-2 shrink-0">
          <div class="relative w-56 md:w-72">
            <input type="search" x-ref="search" x-model="search" @input="search = $event.target.value; filterMenu()" @search="clearSearch()" @keydown.escape.prevent="clearSearch(); $refs.search.focus()" placeholder="Search menu item..." autocapitalize="none" autocomplete="off" spellcheck="false"
               class="w-full h-9 rounded-xl border border-[#3B9441]/40 bg-white/70 backdrop-blur-sm px-3 pl-9 text-sm focus:ring-2 focus:ring-[#3B9441]/50 focus:border-[#3B9441] outline-none shadow-sm placeholder-gray-400">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
          </div>
          <button type="button" @click="clearSearch(); $refs.search.focus()"
            class="h-9 px-3 rounded-xl bg-[#FFF2B0] text-[#3B9441] hover:bg-[#FFD600]/90 transition font-medium shadow-sm text-xs">
            Clear
          </button>
        </div>
      </div>

    <!-- Menu Items -->
    <div class="flex-1 min-h-0 overflow-y-auto no-scrollbar grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 mt-2 pr-1 pb-2">
        <template x-for="item in (filteredMenu[selectedCategory] || (menu[selectedCategory] || []))" :key="item.id">
            <div class="rounded-2xl border border-[#FFD600]/30 bg-white/70 backdrop-blur-md 
                        flex flex-col hover:shadow-lg hover:-translate-y-1 transition-all duration-300 
                        h-[300px] cursor-pointer relative"
                :class="item.is_available ? '' : 'opacity-50 grayscale'">

            <!-- Image -->
            <div class="relative h-[55%] rounded-t-2xl overflow-hidden">
                <img 
                :src="'{{ asset('images/menus') }}/' + (item.image_url || 'noimage.jpg')" 
                alt=""
                class="w-full h-full object-cover transition-transform duration-300 hover:scale-110">

                <!-- Stock Badge -->
                <span class="absolute top-2 right-2 bg-[#FFD600]/90 text-[#2E2E2E] text-[11px] font-semibold 
                            px-2 py-0.5 rounded-full shadow-md">
                Stock: <span x-text="item.stock"></span>
                </span>
            </div>

            <!-- Details -->
            <div class="flex-1 flex flex-col justify-between p-3">
                <div>
                <h4 class="font-semibold text-[#3B9441] truncate" x-text="item.name"></h4>
                <p class="text-gray-600 text-xs mt-1 line-clamp-2" x-text="item.description"></p>

                <!-- Price -->
                <p class="text-base font-semibold text-[#C45E25] mt-2" x-text="formatCurrency(item.price)"></p>
                </div>

                <button type="button" 
                        @click="addMenuItem(item)"
                        :disabled="item.stock <= 0"
                        class="mt-2 w-full bg-[#3B9441] hover:bg-[#2E7D32] disabled:bg-gray-300 disabled:cursor-not-allowed
                            text-white rounded-lg py-1.5 text-sm transition shadow-md font-medium">
                <span x-text="item.stock > 0 ? 'Add to Order' : 'Out of Stock'"></span>
                </button>
            </div>
        </div>
    </template>
    </div>

    </section>

    <!-- ORDER SUMMARY -->
    <aside class="sticky top-16 self-start h-[calc(95vh-4rem)]">
      <div class="flex flex-col border border-[#FFD600]/40 rounded-2xl bg-white/80 backdrop-blur-md p-5 shadow-lg min-h-0 h-full">

        <!-- Order Header -->
        <div class="mb-3 border-b border-[#FFD600]/40 pb-3">
          <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold text-[#C45E25] flex items-center gap-2">
              <i data-lucide="shopping-cart" class="w-5 h-5 text-[#FFD600]"></i>
              Order Summary
            </h3>
          </div>

          <div class="grid grid-cols-1 gap-1 text-xs text-gray-600">
            <p>
              <span class="font-semibold text-[#3B9441]">Order No:</span>
              <span x-text="(daily_order_number ? daily_order_number : (order_id ? order_id.split('-').slice(-1)[0] : 0)).toString().padStart(4,'0')"></span>
            </p>
            <p>
              <span class="font-semibold text-[#3B9441]">Order ID:</span>
              <span x-text="order_id"></span>
            </p>
            <p><span class="font-semibold text-[#3B9441]">Date:</span> <span x-text="currentDate"></span></p>
          </div>
        </div>

        <!-- Categorized Items List -->
        <div class="flex-1 min-h-0 overflow-y-auto pr-1 space-y-5">
          <template x-if="order_items.length === 0">
            <p class="text-center text-gray-400 italic text-sm py-4">No items yet. Add from the menu.</p>
          </template>

          <template x-for="(group, category) in groupedAndSortedItems" :key="category">
            <div>
              <h4 class="text-[#C45E25] text-sm font-semibold border-b border-dashed border-[#FFD600]/50 mb-1 pb-0.5">
                <span x-text="category"></span>
              </h4>

              <template x-for="(it, idx) in group" :key="it.id">
                <div class="flex items-center justify-between text-sm border-b border-dashed border-[#FFD600]/30 py-1">
                  <div>
                    <span class="font-medium" x-text="it.name"></span>
                    <div class="text-xs text-gray-500">
                      <span x-text="it.qty"></span> Ã— <span x-text="formatCurrency(it.price)"></span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <input type="number" min="1" x-model.number="it.qty" @change="updateStockQuantity(it)"
                          class="w-12 text-sm border border-[#FFD600]/40 rounded-lg px-1 py-1 text-center 
                                  focus:ring-1 focus:ring-[#FFD600] outline-none">
                    <span class="font-semibold text-[#3B9441]" x-text="formatCurrency(it.qty * it.price)"></span>
                    <button type="button" @click="removeItemById(it.id)"
                            class="p-1 rounded-full bg-[#FFF9E6] text-[#C45E25] hover:bg-[#FFD600]/30 transition border border-[#FFD600]/50">
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </div>
              </template>
            </div>
          </template>
        </div>

        <!-- Payment Section -->
        <div class="pt-3 border-t border-[#FFD600]/40 bg-white/80 backdrop-blur-md mt-2 space-y-4">

          <!-- Totals -->
          <div class="text-sm">
            <div class="flex justify-between mb-1">
              <span>Subtotal</span>
              <span x-text="formatCurrency(subtotal)"></span>
            </div>
            <div class="flex justify-between text-base font-bold">
              <span class="text-[#C45E25]">Total</span>
              <span class="text-[#3B9441]" x-text="formatCurrency(total)"></span>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex gap-3 mt-4">
            <button type="button" @click="clearOrder"
                    class="flex-1 py-2 rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 
                          transition font-medium shadow-sm">
              Clear
            </button>
        <button type="button" @click="openPayment()"
                class="flex-1 py-2 rounded-xl bg-linear-to-r from-[#3B9441] to-[#2E7D32] 
                       text-white hover:scale-[1.02] transition font-medium shadow-md">
          Pay
        </button>
          </div>
          <div class="mt-2">
            <span id="itemsError" class="text-xs text-red-600 hidden">Please add at least one item to continue.</span>
          </div>
        </div>
      </div>
    </aside>
  </form>

  <!-- Payment Modal -->
  <!-- Payment Modal -->
  <div x-show="showPayment" style="display:none" class="fixed inset-0 z-50 flex items-center justify-center font-[Poppins]">
    <div class="absolute inset-0 bg-black/50" @click="showPayment=false"></div>

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-[#FFD600]/40 p-5 flex flex-col max-h-[90vh]">

      <!-- Header -->
      <div class="flex items-center justify-between mb-3">
        <div class="flex items-center gap-2">
          <i data-lucide="credit-card" class="w-5 h-5 text-[#C45E25]"></i>
          <h3 class="text-lg font-semibold text-[#C45E25]">Payment</h3>
        </div>
        <button type="button" class="text-gray-400 hover:text-[#C45E25] transition" @click="showPayment=false">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>

      <!-- 🧾 Scrollable Receipt Only -->
      <div class="flex-1 overflow-y-auto no-scrollbar">
        <div class="bg-[#FFFDF8] border border-[#FFD600]/50 rounded-xl p-4 text-sm">
          <div class="text-center mb-2">
            <h4 class="font-semibold text-[#C45E25]">Hotel Consuelo</h4>
            <p class="text-xs text-gray-500">Restaurant Receipt</p>
          </div>

          <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-xs text-gray-600 mb-2">
            <div>Order No:</div>
            <div class="text-right font-medium text-[#3B9441]"
                x-text="(daily_order_number ? daily_order_number : (order_id ? order_id.split('-').slice(-1)[0] : 0)).toString().padStart(4,'0')"></div>
            <div>Order ID:</div>
            <div class="text-right" x-text="order_id"></div>
            <div>Date:</div><div class="text-right" x-text="currentDate"></div>
            <div>Method:</div><div class="text-right" x-text="payment_method"></div>
          </div>

          <div class="border-t border-dashed border-[#FFD600]/50 my-2"></div>

          <!-- Items List -->
          <div :class="order_items.length >= 6 ? 'max-h-56 overflow-y-auto no-scrollbar pr-1' : ''" class="text-[12px]">
            <template x-if="order_items.length === 0">
              <p class="text-center text-gray-400 italic">No items yet.</p>
            </template>
            <template x-for="it in order_items" :key="it.id">
              <div class="flex items-start justify-between py-0.5">
                <div class="pr-2">
                  <div class="font-medium text-[#2E2E2E]" x-text="it.name"></div>
                  <div class="text-[10px] text-gray-500">
                    Qty: <span x-text="it.qty"></span> @ <span x-text="formatCurrency(it.price)"></span>
                  </div>
                </div>
                <div class="font-semibold text-[#3B9441]" x-text="formatCurrency(it.qty * it.price)"></div>
              </div>
            </template>
          </div>

          <div class="border-t border-dashed border-[#FFD600]/50 my-2"></div>

            <div class="text-sm">
              <div class="flex justify-between"><span>Subtotal</span><span x-text="formatCurrency(subtotal)"></span></div>
              <div class="flex justify-between font-semibold">
                <span class="text-[#C45E25]">Total</span>
                <span class="text-[#3B9441]" x-text="formatCurrency(total)"></span>
              </div>
              <!-- Card details in receipt -->
              <div class="flex justify-between text-xs mt-1" x-show="payment_method==='Card'">
                <span>Card Amount</span><span x-text="formatCurrency(card_amount || 0)"></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='Card' && card_reference">
                <span>Card Ref</span><span x-text="card_reference"></span>
              </div>
              <!-- E-Wallet details in receipt -->
              <div class="flex justify-between text-xs mt-1" x-show="payment_method==='E-Wallet'">
                <span>Provider</span><span x-text="ewallet_provider || '-' "></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='E-Wallet'">
                <span>Reference</span><span x-text="ewallet_reference || '-' "></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='E-Wallet'">
                <span>E-Wallet Amount</span><span x-text="formatCurrency(ewallet_amount || 0)"></span>
              </div>
              <div class="flex justify-between text-xs mt-1" x-show="payment_method==='Cash' || payment_method==='Mixed'">
                <span>Cash Tendered</span><span x-text="formatCurrency(amount_tendered || mixed_cash || 0)"></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='Cash' || payment_method==='Mixed'">
                <span>Change</span><span x-text="formatCurrency(change_due)"></span>
              </div>
              <!-- Mixed: Digital amount line -->
              <div class="flex justify-between text-xs" x-show="payment_method==='Mixed'">
                <span>Digital Amount</span><span x-text="formatCurrency(mixed_digital || 0)"></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='Mixed' && mixed_provider">
                <span>Provider</span><span x-text="mixed_provider"></span>
              </div>
              <div class="flex justify-between text-xs" x-show="payment_method==='Mixed' && mixed_reference">
                <span>Reference</span><span x-text="mixed_reference"></span>
              </div>
            </div>
        </div>
      </div>

      <!-- 💳 Payment Fields (Fixed Middle) -->
      <div class="space-y-3 mt-4">
        <div>
          <label class="text-xs text-gray-600">Method</label>
          <select x-model="payment_method" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600] focus:border-[#FFD600]">
            <option>Cash</option>
            <option>Card</option>
            <option>E-Wallet</option>
            <option>Mixed</option>
          </select>
        </div>

        <!-- Cash Fields -->
        <div x-show="payment_method==='Cash'" class="grid grid-cols-2 gap-2">
          <div class="relative pb-3">
            <label class="text-xs text-gray-600">Amount Tendered</label>
            <input type="number" step="0.01" x-model.number="amount_tendered" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
            <span x-show="Number(amount_tendered||0) < Number(total||0)" class="absolute right-2 -bottom-1 text-[11px] text-red-600">Insufficient</span>
          </div>
          <div>
            <label class="text-xs text-gray-600">Change</label>
            <div class="h-[34px] flex items-center px-3 rounded-lg border bg-gray-50 text-xs font-semibold text-[#3B9441]" x-text="formatCurrency(change_due)"></div>
          </div>
        </div>

        <!-- Card Fields -->
        <div x-show="payment_method==='Card'" class="grid grid-cols-2 gap-2">
          <div>
            <label class="text-xs text-gray-600">Reference No.</label>
            <input type="text" x-model="card_reference" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
          <div class="relative pb-3">
            <label class="text-xs text-gray-600">Amount</label>
            <input type="number" step="0.01" x-model.number="card_amount" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
            <span x-show="Number(card_amount||0) < Number(total||0)" class="absolute right-2 -bottom-1 text-[11px] text-red-600">Less than total</span>
          </div>
        </div>

        <!-- E-Wallet Fields -->
        <div x-show="payment_method==='E-Wallet'" class="grid grid-cols-3 gap-2">
          <div>
            <label class="text-xs text-gray-600">Provider</label>
            <input type="text" placeholder="GCash/Maya" x-model="ewallet_provider" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
          <div>
            <label class="text-xs text-gray-600">Reference No.</label>
            <input type="text" x-model="ewallet_reference" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
          <div class="relative pb-3">
            <label class="text-xs text-gray-600">Amount</label>
            <input type="number" step="0.01" x-model.number="ewallet_amount" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
            <span x-show="Number(ewallet_amount||0) < Number(total||0)" class="absolute right-2 -bottom-1 text-[11px] text-red-600">Less than total</span>
          </div>
        </div>

        <!-- Mixed Fields -->
        <div x-show="payment_method==='Mixed'" class="grid grid-cols-2 gap-2">
          <div>
            <label class="text-xs text-gray-600">Cash Amount</label>
            <input type="number" step="0.01" x-model.number="amount_tendered" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
          <div class="relative pb-3">
            <label class="text-xs text-gray-600">Digital Amount</label>
            <input type="number" step="0.01" x-model.number="mixed_digital" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
            <span x-show="Number((amount_tendered||0) + (mixed_digital||0)) < Number(total||0)" class="absolute right-2 -bottom-1 text-[11px] text-red-600">Less than total</span>
          </div>
          <div>
            <label class="text-xs text-gray-600">Provider</label>
            <input type="text" placeholder="GCash/Maya/Bank" x-model="mixed_provider" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
          <div>
            <label class="text-xs text-gray-600">Reference No.</label>
            <input type="text" x-model="mixed_reference" class="w-full rounded-lg border px-3 py-1.5 text-xs focus:ring-[#FFD600]" />
          </div>
        </div>
      </div>

      <!-- ✅ Footer Buttons -->
      <div class="flex justify-end gap-3 pt-3 border-t border-[#FFD600]/30 mt-4 sticky bottom-0 bg-white">
        <button type="button" class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition" @click="showPayment=false">
          <i data-lucide='x-circle' class="w-4 h-4"></i>
          Cancel
        </button>
        <button type="button" class="flex items-center gap-1 px-4 py-1.5 rounded-lg bg-[#3B9441] text-white hover:bg-[#2E7D32] transition" @click="submitForm">
          <i data-lucide='check-circle' class="w-4 h-4"></i>
          Pay Now
        </button>
      </div>
    </div>
  </div>

<script>
  document.addEventListener("alpine:init", () => {
    Alpine.data("paymentModal", () => ({
      // your Alpine state here
    }));
  });
  lucide.createIcons();
</script>



</div>

<script>
function posSystem() {
  return {
    // ===========================
// DATA STATE
    // ===========================
    search: '',
    categories: [],
    menu: {},
    filteredMenu: {},
    selectedCategory: null,
    order_items: [],
    // Derived identifiers
    daily_order_number: '',
// Payment Data
    payment_method: 'Cash',
    payment_status: 'Paid',
    showPayment: false,

    // Cash
    cash_amount: 0,

    // Card
    card_amount: 0,
    card_reference: '',

    // E-Wallet
    ewallet_provider: '',
    ewallet_amount: 0,
    ewallet_reference: '',

    // Mixed Payment
    mixed_cash: 0,
    mixed_digital: 0,
    mixed_reference: '',
    mixed_provider: '',
// Amount tendered
    amount_tendered: 0,
// Identifiers & Date
    order_id: '',
    transaction_id: '',
    currentDate: '',

    // ===========================
    // âš™ï¸ INIT
    // ===========================
    async init() {
      this.menu = @json($menu ?? []);
      this.categories = Object.keys(this.menu);
      this.selectedCategory = this.categories[0];
      this.filteredMenu = JSON.parse(JSON.stringify(this.menu));
// Local time \(Asia/Manila\)
      const options = {
        timeZone: 'Asia/Manila',
        hour12: true,
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      };
      this.currentDate = new Date().toLocaleString('en-PH', options);
// Generate next IDs
      await this.fetchOrderIds();
      // Derive daily_order_number from order_id if backend doesn't return it
      if (!this.daily_order_number && this.order_id) {
        const last = String(this.order_id).split('-').slice(-1)[0] || '';
        this.daily_order_number = last.replace(/[^0-9]/g, '');
      }
// Initialize Lucide icons
      this.$nextTick(() => { if (window.refreshLucide) window.refreshLucide(); });
    },

    // ===========================
// FETCH NEXT IDS
    // ===========================
    async fetchOrderIds() {
      try {
        const res = await fetch("{{ route('orders.nextIds') }}");
        const data = await res.json();
        this.order_id = data.order_id || 'ORD-00001';
        this.transaction_id = data.transaction_id || 'TXN-ERROR';
        // If API provides daily_order_number, capture it; else derive
        this.daily_order_number = String(data.daily_order_number || '') || (this.order_id?.split('-').slice(-1)[0] || '');
      } catch (err) {
        console.error('âŒ Failed to fetch order IDs', err);
        this.order_id = 'ORD-ERROR';
        this.transaction_id = 'TXN-ERROR';
      }
    },

    // ===========================
// UTILITIES
    // ===========================
    formatCurrency(val) {
      return new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(val ?? 0);
    },

    // ===========================
// SEARCH \+ FILTER
    // ===========================
    clearSearch() {
      this.search = '';
      // Hard reset filtered list to full menu
      this.filteredMenu = JSON.parse(JSON.stringify(this.menu));
      // Ensure selected category is valid
      if (!this.selectedCategory || !this.filteredMenu[this.selectedCategory]) {
        this.selectedCategory = this.categories[0] || null;
      }
    },
    filterMenu() {
      const q = String(this.search || '').toLowerCase().trim();
      const out = {};
      let firstWithResults = null;
      // Build filtered lists and track first category with results
      for (const cat of this.categories) {
        const items = this.menu[cat] || [];
        const matches = q ? items.filter(i => (i.name || '').toLowerCase().includes(q)) : items;
        out[cat] = matches;
        if (!firstWithResults && matches.length) firstWithResults = cat;
      }
      this.filteredMenu = out;

      // Auto-navigate category when searching
      if (q) {
        // If query matches a category name, jump there
        const direct = this.categories.find(c => c.toLowerCase().includes(q));
        if (direct) {
          this.selectedCategory = direct;
          return;
        }
        // Otherwise, if current category is empty, move to first with results
        if (!out[this.selectedCategory] || out[this.selectedCategory].length === 0) {
          if (firstWithResults) this.selectedCategory = firstWithResults;
        }
      } else {
        // On clear: reset filtered lists to full menu and keep selection valid
        // Ensure current selectedCategory exists; otherwise use the first
        if (!this.selectedCategory || !out[this.selectedCategory]) {
          this.selectedCategory = this.categories[0] || null;
        }
      }
    },

    // ===========================
// ORDER MANAGEMENT
    // ===========================
    addMenuItem(item) {
      if (!item.is_available || item.stock <= 0) return;

      const found = this.order_items.find(i => i.id === item.id);
      if (found) {
        found.qty++;
      } else {
        this.order_items.push({
          id: item.id,
          name: item.name,
          category: item.category,
          price: Number(item.price),
          qty: 1,
        });
      }

      this.updateStock(item.id, -1);
      this.$nextTick(() => { if (window.refreshLucide) window.refreshLucide(); });
    },

    removeItemById(id) {
      const index = this.order_items.findIndex(i => i.id === id);
      if (index !== -1) {
        const item = this.order_items[index];
        this.updateStock(item.id, item.qty);
        this.order_items.splice(index, 1);
        this.$nextTick(() => { if (window.refreshLucide) window.refreshLucide(); });
      }
    },

    updateStockQuantity(it) {
      const menuItem = Object.values(this.filteredMenu)
        .flat()
        .find(m => m.id === it.id);

      if (menuItem) {
        const prevQty = menuItem.prevQty ?? 1;
        const diff = it.qty - prevQty;
        menuItem.stock = Math.max(0, menuItem.stock - diff);
        menuItem.prevQty = it.qty;
      }
    },

    updateStock(id, change) {
      for (const cat in this.filteredMenu) {
        const target = this.filteredMenu[cat].find(i => i.id === id);
        if (target) {
          target.stock = Math.max(0, (target.stock ?? target.stock_quantity ?? 0) + change);
        }
      }
    },

    clearOrder() {
      this.order_items.forEach(i => this.updateStock(i.id, i.qty));
      this.order_items = [];
      this.$nextTick(() => { if (window.refreshLucide) window.refreshLucide(); });

      this.payment_method = 'Cash';
      this.payment_status = 'Paid';
      this.cash_amount = 0;
      this.card_amount = 0;
      this.card_reference = '';
      this.ewallet_provider = '';
      this.ewallet_reference = '';
      this.ewallet_amount = 0;
      this.mixed_cash = 0;
      this.mixed_digital = 0;
      this.mixed_reference = '';
      this.mixed_provider = '';
      this.amount_tendered = 0;
    },

    // ===========================
// COMPUTED TOTALS
    // ===========================
    get subtotal() {
      return this.order_items.reduce((s, i) => s + i.price * i.qty, 0);
    },
    get total() {
      return this.subtotal;
    },
    get change_due() {
      const change = this.amount_tendered - this.total;
      return change > 0 ? change : 0;
    },

    get groupedAndSortedItems() {
      const order = ['Main Course', 'Dessert', 'Drinks', 'Rice', 'Appetizer', 'Combo'];
      const grouped = {};
      for (const o of order) grouped[o] = [];
      for (const item of this.order_items) {
        (grouped[item.category] ||= []).push(item);
      }
      return Object.fromEntries(Object.entries(grouped).filter(([_, arr]) => arr.length));
    },

    // ===========================
    openPayment() {
      if (!Array.isArray(this.order_items) || this.order_items.length === 0) {
        if (window.toast) { window.toast({ icon: 'error', title: 'Please add items first' }); }
        else if (window.Swal) { Swal.fire({ icon: 'error', title: 'No items', text: 'Please add items first.' }); }
        return;
      }
      this.showPayment = true;
    },
// SUBMIT ORDER
    // ===========================
    submitForm() {
      if (!this.order_items.length) { if (window.Swal) { Swal.fire({icon:'error',title:'Validation error',text:'Please add at least one item.'}); } return; }
// Build payload based on payment type
      // Validation per payment method
      const hide = id => { const el = document.getElementById(id); if (el) el.classList.add('hidden'); };
      const show = (id, msg) => { const el = document.getElementById(id); if (el) { if (msg) el.textContent = msg; el.classList.remove('hidden'); }};
      ['paymentError','amountTenderedError','cardReferenceError','cardAmountError','ewalletProviderError','ewalletReferenceError','ewalletAmountError','amountTenderedMixedError','mixedProviderError','mixedReferenceError','mixedDigitalError','itemsError'].forEach(hide);

      let valid = true;
      let firstError = '';
      if (!this.order_items.length) { show('itemsError'); if (!firstError) firstError = 'Please add at least one item.'; valid = false; }

      if (!this.payment_method) { show('paymentError'); if (!firstError) firstError = 'Please choose a payment method.'; valid = false; }
      const tot = Number(this.total || 0);
      if (this.payment_method === 'Cash') {
        const amt = Number(this.amount_tendered || 0);
        if (!(amt > 0)) { show('amountTenderedError', 'Amount is required.'); if (!firstError) firstError = 'Amount tendered is required.'; valid = false; }
        else if (amt < tot) { show('amountTenderedError', 'Insufficient amount tendered.'); if (!firstError) firstError = 'Insufficient cash amount.'; valid = false; }
      } else if (this.payment_method === 'Card') {
        if (!this.card_reference || String(this.card_reference).trim() === '') { show('cardReferenceError'); if (!firstError) firstError = 'Card reference is required.'; valid = false; }
        const camt = Number(this.card_amount || 0);
        if (!(camt > 0)) { show('cardAmountError'); if (!firstError) firstError = 'Card amount is required.'; valid = false; }
        else if (camt < tot) { if (!firstError) firstError = 'Card amount is less than total.'; valid = false; }
      } else if (this.payment_method === 'E-Wallet') {
        if (!this.ewallet_provider) { show('ewalletProviderError'); if (!firstError) firstError = 'E-wallet provider is required.'; valid = false; }
        if (!this.ewallet_reference || String(this.ewallet_reference).trim() === '') { show('ewalletReferenceError'); if (!firstError) firstError = 'E-wallet reference is required.'; valid = false; }
        const eamt = Number(this.ewallet_amount || 0);
        if (!(eamt > 0)) { show('ewalletAmountError'); if (!firstError) firstError = 'E-wallet amount is required.'; valid = false; }
        else if (eamt < tot) { if (!firstError) firstError = 'E-wallet amount is less than total.'; valid = false; }
      } else if (this.payment_method === 'Mixed') {
        const cash = Number(this.amount_tendered || 0);
        const damt = Number(this.mixed_digital || 0);
        if (!(cash >= 0)) { show('amountTenderedMixedError', 'Enter cash amount (0 allowed).'); if (!firstError) firstError = 'Enter a cash amount (can be 0).'; valid = false; }
        if (!this.mixed_provider) { show('mixedProviderError'); if (!firstError) firstError = 'Digital provider is required.'; valid = false; }
        if (!this.mixed_reference || String(this.mixed_reference).trim() === '') { show('mixedReferenceError'); if (!firstError) firstError = 'Digital reference is required.'; valid = false; }
        if (!(damt > 0)) { show('mixedDigitalError'); if (!firstError) firstError = 'Digital amount is required.'; valid = false; }
        if (cash + damt < tot) { show('mixedDigitalError', 'Combined cash + digital must cover total.'); if (!firstError) firstError = 'Cash + digital must cover the total.'; valid = false; }
      }

      if (!valid) {
        if (window.toast) {
          window.toast({ icon: 'error', title: firstError || 'Please fix highlighted fields.' });
        } else if (window.Swal) {
          Swal.fire({ icon: 'error', title: firstError || 'Validation error' });
        }
        return;
      }

            // Build receipt snapshot for storage/printing
      const method = this.payment_method;
      const receipt = {
        header: {
          order_id: this.order_id,
          transaction_id: this.transaction_id,
          date: this.currentDate,
          method,
          status: this.payment_status,
        },
        items: this.order_items.map(i => ({
          name: i.name,
          qty: i.qty,
          price: Number(i.price),
          total: Number(i.price) * i.qty,
          category: i.category,
        })),
        totals: {
          subtotal: this.subtotal,
          total: this.total,
          change: this.change_due,
        },
        payments: {
          cash: method === "Cash" ? Number(this.amount_tendered || 0) : (method === "Mixed" ? Number(this.mixed_cash || this.amount_tendered || 0) : 0),
          card_amount: method === "Card" ? Number(this.card_amount || 0) : 0,
          card_reference: method === "Card" ? (this.card_reference || '') : '',
          ewallet_provider: method === "E-Wallet" ? (this.ewallet_provider || '') : '',
          ewallet_reference: method === "E-Wallet" ? (this.ewallet_reference || '') : '',
          ewallet_amount: method === "E-Wallet" ? Number(this.ewallet_amount || 0) : 0,
          mixed_cash: method === "Mixed" ? Number(this.mixed_cash || this.amount_tendered || 0) : 0,
          mixed_digital: method === "Mixed" ? Number(this.mixed_digital || 0) : 0,
          mixed_reference: method === "Mixed" ? (this.mixed_reference || '') : '',
          mixed_provider: method === "Mixed" ? (this.mixed_provider || '') : '',
        },
      };

      const payload = {
        order_id: this.order_id,
        transaction_id: this.transaction_id,
        payment_method: this.payment_method,
        payment_status: this.payment_status,
        cash_amount: this.cash_amount,
        card_amount: this.card_amount,
        card_reference: this.card_reference,
        ewallet_provider: this.ewallet_provider,
        ewallet_reference: this.ewallet_reference,
        ewallet_amount: this.ewallet_amount,
        mixed_cash: this.mixed_cash,
        mixed_digital: this.mixed_digital,
        mixed_reference: this.mixed_reference,
        mixed_provider: this.mixed_provider,
        amount_tendered: this.amount_tendered,
        change_due: this.change_due,
        items: this.order_items,
        subtotal: this.subtotal,
        total: this.total,
      };

      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '{{ route('cashier.order.store') }}';
      form.innerHTML = `@csrf
        <input type="hidden" name="payload" value='${JSON.stringify(payload)}'>
      `;
      document.body.appendChild(form);
      form.submit();
    },
  };
}

</script>

@if(session('error'))
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const msg = @json(session('error'));
      if (msg && msg.toLowerCase().includes('order is empty')) {
        const el = document.getElementById('itemsError');
        if (el) el.classList.remove('hidden');
      }
    });
  </script>
@endif

@endsection










