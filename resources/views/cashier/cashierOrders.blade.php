@extends('layout.app')

@section('title', 'Orders | Consuelo Restaurant')

@section('content')
<div x-data="{ tab: 'all', search: '' }" class="p-6 font-[Poppins]">

  <!-- Header -->
  <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold text-[#D92332]">Order List</h1>
      <p class="text-sm text-gray-500">{{ now('Asia/Manila')->format('F d, Y') }}</p>
    </div>

    <!-- Search -->
    <div class="mt-3 md:mt-0 relative">
      <input x-model="search" type="text" placeholder="Search orders..." 
             class="w-64 px-4 py-2 text-sm rounded-full border border-gray-300 focus:ring-[#FFD600] focus:border-[#FFD600]">
      <i data-lucide="search" class="absolute right-3 top-2.5 w-4 h-4 text-gray-500"></i>
    </div>
  </div>

  <!-- Tabs -->
  <div class="flex flex-wrap gap-2 mb-6">
    @foreach([
      'all' => 'All',
      'new' => 'New Orders',
      'cook' => 'On Cook',
      'done' => 'Completed',
      'cancelled' => 'Cancelled'
    ] as $key => $label)
      <button @click="tab='{{ $key }}'" 
              :class="tab==='{{ $key }}' ? 'bg-[#FFD600] text-[#D92332]' : 'bg-gray-100 text-gray-600'"
              class="px-4 py-1.5 rounded-full text-sm font-medium">{{ $label }}</button>
    @endforeach
  </div>

  <!-- Combine all orders -->
  @php
    $orders = collect([
        $pending ?? collect(),
        $preparing ?? collect(),
        $served ?? collect(),
        $cancelled ?? collect(),
    ])->flatten();

    $orders = $orders->sortByDesc(function ($o) {
        $dt = $o->updated_at ?? $o->ordered_at ?? $o->created_at ?? null;
        return optional($dt)->timestamp ?? 0;
    })->values();
  @endphp

  <!-- Orders Grid -->
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
    @foreach($orders as $o)
      <template x-if="
        tab === 'all' 
        || (tab === 'new' && '{{ $o->status }}' === 'Pending') 
        || (tab === 'cook' && '{{ $o->status }}' === 'Preparing')
        || (tab === 'done' && '{{ $o->status }}' === 'Served')
        || (tab === 'cancelled' && '{{ $o->status }}' === 'Cancelled')
      ">
        <div x-show="'{{ strtolower($o->order_id) }} {{ strtolower($o->customer_name ?? '') }} {{ strtolower($o->status) }}'.includes(search.toLowerCase())"
             class="relative bg-white/90 backdrop-blur-md border border-[#FFD600]/40 rounded-2xl shadow-sm p-4 flex flex-col hover:shadow-lg transition h-[400px]">

          <!-- Header Info -->
          <div>
            <div class="flex justify-between items-center mb-1">
              <h3 class="font-semibold text-gray-800">
                {{ isset($o->daily_order_number)
                    ? sprintf('%04d', (int) $o->daily_order_number)
                    : (isset($o->order_id)
                        ? (explode('-', $o->order_id)[2] ?? '0000')
                        : '0000')
                }}
              </h3>
              @php
                $statusColor = match($o->status) {
                    'Pending' => 'bg-blue-100 text-blue-700',
                    'Preparing' => 'bg-amber-100 text-amber-700',
                    'Served' => 'bg-green-100 text-green-700',
                    'Cancelled' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-700',
                };
              @endphp

              <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                {{ $o->status }}
              </span>

            </div>
            <p class="text-xs text-gray-500">{{ $o->order_id ?? '+63...' }}</p>
            <p class="text-xs text-gray-500">
              <i data-lucide="clock" class="inline w-3 h-3 mr-1"></i>
              {{ optional($o->ordered_at)->timezone('Asia/Manila')->format('h:i A, M d, Y') }}
            </p>
          </div>

          <!-- Scrollable Item List -->
          <div class="mt-3 text-[13px] flex-1 border-t border-dashed border-gray-200 pt-2 overflow-y-auto hide-scrollbar">
            @php
              $catMap = [
                'Main Course' => 'main_dish',
                'Appetizer'   => 'appetizer',
                'Dessert'     => 'dessert',
                'Drinks'      => 'drinks',
                'Rice'        => 'rice',
                'Combo'       => 'combo',
              ];
            @endphp

            @foreach($catMap as $label => $field)
              @php
                $raw = $o->{$field} ?? [];
                if (is_string($raw)) {
                  $raw = json_decode($raw, true);
                  $raw = is_array($raw) ? $raw : [];
                }
                $catItems = collect($raw)->filter()->values();
              @endphp
              @if($catItems->count())
                <div class="mb-2">
                  <div class="text-[12px] font-semibold text-[#3B9441] tracking-wide">{{ $label }}</div>
                  <div class="mt-1 space-y-0.5">
                    @foreach($catItems as $it)
                      @php
                        $name = is_array($it) ? ($it['name'] ?? 'Item') : ($it->name ?? 'Item');
                        $qty  = (int)(is_array($it) ? ($it['qty'] ?? 1) : ($it->qty ?? 1));
                        $price = (float)(is_array($it) ? ($it['price'] ?? 0) : ($it->price ?? 0));
                        $line = $price * max(1, $qty);
                      @endphp
                      <div class="flex justify-between">
                        <span class="truncate">{{ $name }} <span class="text-[11px] text-gray-500">x{{ $qty }}</span></span>
                        <span>₱{{ number_format($line, 2) }}</span>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif
            @endforeach
          </div>

          <!-- Fixed Footer -->
          @php
            $countItems = 0;
            foreach (['main_dish','appetizer','dessert','drinks','rice','combo'] as $key) {
              $raw = $o->{$key} ?? [];
              if (is_string($raw)) {
                $raw = json_decode($raw, true);
                $raw = is_array($raw) ? $raw : [];
              }
              $countItems += is_countable($raw) ? count($raw) : 0;
            }
          @endphp

          <div class="absolute bottom-0 left-0 w-full bg-white/95 border-t border-[#FFD600]/40 rounded-b-2xl p-3">
            <div class="flex justify-between items-center font-semibold text-[#D92332] text-sm mb-2">
              <span>{{ $countItems }} Items</span>
              <span>₱{{ number_format((float)($o->total_amount ?? $o->total ?? 0), 2) }}</span>
            </div>

            <div class="flex gap-2">
              @if($o->status === 'Pending')
                <form method="POST" action="{{ route('cashier.orders.start', $o) }}">@csrf
                  <button class="w-full text-xs py-1.5 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Start</button>
                </form>
                <form method="POST" action="{{ route('cashier.orders.cancel', $o) }}" onsubmit="return confirm('Cancel order?');">@csrf
                  <button class="w-full text-xs py-1.5 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300">Cancel</button>
                </form>
              @elseif($o->status === 'Preparing')
                <form method="POST" action="{{ route('cashier.orders.serve', $o) }}">@csrf
                  <button class="w-full text-xs py-1.5 rounded-lg bg-green-600 text-white hover:bg-green-700">Mark Served</button>
                </form>
              @else
                <button disabled class="w-full text-xs py-1.5 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">No Actions</button>
              @endif
            </div>
          </div>

        </div>
      </template>
    @endforeach
  </div>
</div>

<!-- Hide scrollbar globally for item list -->
<style>
  .hide-scrollbar::-webkit-scrollbar {
    width: 0;
    height: 0;
  }
  .hide-scrollbar {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
  }
</style>
@endsection
