@extends('layout.app')

@section('title', 'Cashier Reports')

@section('content')
<div class="p-6 space-y-6">
  <h1 class="text-2xl font-bold text-[#D92332]">Reports</h1>

  <!-- Filters -->
  <form method="GET" class="flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs text-gray-600">From</label>
      <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded border px-2 py-1 text-sm" />
    </div>
    <div>
      <label class="block text-xs text-gray-600">To</label>
      <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded border px-2 py-1 text-sm" />
    </div>
    <div>
      <label class="block text-xs text-gray-600">Cashier</label>
      <input type="text" name="cashier" value="{{ $filters['cashier'] ?? '' }}" placeholder="Name" class="rounded border px-2 py-1 text-sm" />
    </div>
    <button class="px-3 py-2 rounded bg-[#D92332] text-white text-sm">Apply</button>
    @if(($filters['from'] ?? '') || ($filters['to'] ?? '') || ($filters['cashier'] ?? ''))
      <a href="{{ route('cashier.reports') }}" class="px-3 py-2 rounded bg-gray-100 text-gray-700 text-sm">Clear</a>
    @endif
  </form>

  <!-- KPIs -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Today Orders</div>
      <div class="text-2xl font-bold">{{ $kpi['today_orders'] }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Today Sales</div>
      <div class="text-2xl font-bold text-[#3B9441]">₱{{ number_format((float)$kpi['today_sales'], 2) }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Month Orders</div>
      <div class="text-2xl font-bold">{{ $kpi['month_orders'] }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Month Sales</div>
      <div class="text-2xl font-bold text-[#3B9441]">₱{{ number_format((float)$kpi['month_sales'], 2) }}</div>
    </div>
  </div>

  <!-- Breakdown Today -->
  <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
    <div class="text-sm font-semibold mb-2">Breakdown Today</div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
      <div>
        <div class="text-gray-500">Cash</div>
        <div class="text-lg font-bold">₱{{ number_format((float)$kpi['today_cash'], 2) }}</div>
      </div>
      <div>
        <div class="text-gray-500">Card</div>
        <div class="text-lg font-bold">₱{{ number_format((float)$kpi['today_card'], 2) }}</div>
      </div>
      <div>
        <div class="text-gray-500">Refunds</div>
        <div class="text-lg font-bold">₱{{ number_format((float)$kpi['today_refund'], 2) }}</div>
      </div>
      <div>
        <div class="text-gray-500">Discount</div>
        <div class="text-lg font-bold">₱{{ number_format((float)$kpi['today_discount'], 2) }}</div>
      </div>
      <div>
        <div class="text-gray-500">Net Sales</div>
        <div class="text-lg font-bold text-[#3B9441]">₱{{ number_format((float)$kpi['today_net'], 2) }}</div>
      </div>
    </div>
  </div>

  <!-- Recent Cashier Reports -->
  <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
    <div class="text-sm font-semibold mb-2">Recent Cashier Reports</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#FFF9E6]">
          <tr class="text-left">
            <th class="px-3 py-2">Date</th>
            <th class="px-3 py-2">Cashier</th>
            <th class="px-3 py-2">Orders</th>
            <th class="px-3 py-2">Sales</th>
            <th class="px-3 py-2">Cash</th>
            <th class="px-3 py-2">Card</th>
            <th class="px-3 py-2">Net</th>
        </tr>
      </thead>
      <tbody>
          @foreach($recent as $r)
            <tr class="border-t border-[#FFD600]/40">
              <td class="px-3 py-2 text-gray-800">@dt($r->created_at)</td>
              <td class="px-3 py-2">{{ $r->cashier_name }}</td>
              <td class="px-3 py-2">{{ $r->total_orders }}</td>
              <td class="px-3 py-2">₱{{ number_format((float)$r->total_sales, 2) }}</td>
              <td class="px-3 py-2">₱{{ number_format((float)$r->total_cash, 2) }}</td>
              <td class="px-3 py-2">₱{{ number_format((float)$r->total_card, 2) }}</td>
              <td class="px-3 py-2 font-semibold text-[#3B9441]">₱{{ number_format((float)$r->net_sales, 2) }}</td>
            </tr>
          @endforeach
      </tbody>
    </table>
    </div>
  </div>

  <!-- Orders (Filtered) -->
  <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
    <div class="text-sm font-semibold mb-2">Orders (Filtered)</div>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#FFF9E6]">
          <tr class="text-left">
            <th class="px-3 py-2">Order</th>
            <th class="px-3 py-2">Amount</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $o)
            <tr class="border-t border-[#FFD600]/40">
              <td class="px-3 py-2 font-medium">{{ $o->order_id }}</td>
              <td class="px-3 py-2 font-semibold text-[#D92332]">₱{{ number_format((float)$o->total, 2) }}</td>
            </tr>
          @empty
            <tr><td colspan="2" class="px-3 py-3 text-center text-gray-400">No orders for selected filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
