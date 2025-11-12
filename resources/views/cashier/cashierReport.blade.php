@extends('layout.app')

@section('title', 'Cashier Report')

@section('content')
<div class="p-6 space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-[#D92332]">Cashier Report</h1>
    <form method="GET" class="flex gap-3 items-end">
      <div>
        <label class="block text-xs text-gray-600">From</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="rounded border px-2 py-1 text-sm" />
      </div>
      <div>
        <label class="block text-xs text-gray-600">To</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="rounded border px-2 py-1 text-sm" />
      </div>
      <button class="px-3 py-2 rounded bg-[#D92332] text-white text-sm">Apply</button>
      @if(($filters['from'] ?? '') || ($filters['to'] ?? ''))
        <a href="{{ route('cashier.report') }}" class="px-3 py-2 rounded bg-gray-100 text-gray-700 text-sm">Clear</a>
      @endif
    </form>
  </div>

  <!-- KPI Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Today Orders</div>
      <div class="text-2xl font-bold">{{ $kpi['today_orders'] }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Today Sales</div>
      <div class="text-2xl font-bold text-[#3B9441]">₱{{ number_format((float)$kpi['today_sales'], 2) }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Cash</div>
      <div class="text-2xl font-bold">₱{{ number_format((float)$kpi['today_cash'], 2) }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Card</div>
      <div class="text-2xl font-bold">₱{{ number_format((float)$kpi['today_card'], 2) }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Discount</div>
      <div class="text-2xl font-bold">₱{{ number_format((float)$kpi['today_discount'], 2) }}</div>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-xs text-gray-500">Net Sales</div>
      <div class="text-2xl font-bold text-[#3B9441]">₱{{ number_format((float)$kpi['today_net'], 2) }}</div>
    </div>
  </div>

  <!-- Charts -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40 lg:col-span-2">
      <div class="text-sm font-semibold mb-2">Sales (Daily)</div>
      <canvas id="salesChart" height="120"></canvas>
    </div>
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-sm font-semibold mb-2">Payments Breakdown</div>
      <canvas id="paymentChart" height="120"></canvas>
    </div>
  </div>

  <!-- Data Tables -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-sm font-semibold mb-2">Recent Orders</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-[#FFF9E6]">
            <tr class="text-left">
              <th class="px-3 py-2">Order</th>
              <th class="px-3 py-2">Status</th>
              <th class="px-3 py-2">Amount</th>
              <th class="px-3 py-2">Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $o)
              <tr class="border-t border-[#FFD600]/40">
                <td class="px-3 py-2 font-medium">{{ $o->order_id }}</td>
                <td class="px-3 py-2">{{ $o->status }}</td>
                <td class="px-3 py-2 font-semibold text-[#D92332]">₱{{ number_format((float)($o->total_amount ?? $o->total ?? 0), 2) }}</td>
                <td class="px-3 py-2 text-gray-700">{{ optional($o->ordered_at)->format('M d, Y h:i A') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-3 py-3 text-center text-gray-400">No orders for selected range.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="p-4 rounded-xl bg-white/80 border border-[#FFD600]/40">
      <div class="text-sm font-semibold mb-2">Recent Receipts</div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-[#FFF9E6]">
            <tr class="text-left">
              <th class="px-3 py-2">Receipt</th>
              <th class="px-3 py-2">Method</th>
              <th class="px-3 py-2">Total</th>
              <th class="px-3 py-2">Issued At</th>
            </tr>
          </thead>
          <tbody>
            @forelse($receipts as $r)
              <tr class="border-t border-[#FFD600]/40">
                <td class="px-3 py-2 font-medium">{{ $r->receipt_id }}</td>
                <td class="px-3 py-2">{{ $r->payment_method ?? '—' }}</td>
                <td class="px-3 py-2 font-semibold text-[#3B9441]">₱{{ number_format((float)$r->total, 2) }}</td>
                <td class="px-3 py-2 text-gray-700">{{ optional($r->issued_at)->format('M d, Y h:i A') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="px-3 py-3 text-center text-gray-400">No receipts for selected range.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Charts JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const salesLabels = @json($salesSeries['labels']);
  const salesValues = @json($salesSeries['values']);

  const ctxSales = document.getElementById('salesChart');
  if (ctxSales) {
    new Chart(ctxSales, {
      type: 'line',
      data: {
        labels: salesLabels,
        datasets: [{
          label: 'Daily Sales',
          data: salesValues,
          borderColor: '#D92332',
          backgroundColor: 'rgba(217,35,50,0.12)',
          tension: 0.3,
          fill: true,
        }]
      },
      options: {
        plugins: { legend: { display: false } },
        scales: {
          y: {
            ticks: {
              callback: (val) => '₱' + Number(val).toLocaleString(undefined, { minimumFractionDigits: 0 })
            }
          }
        }
      }
    });
  }

  const payLabels = @json($paymentBreakdown['labels']);
  const payValues = @json($paymentBreakdown['values']);
  const ctxPay = document.getElementById('paymentChart');
  if (ctxPay) {
    new Chart(ctxPay, {
      type: 'doughnut',
      data: {
        labels: payLabels,
        datasets: [{
          data: payValues,
          backgroundColor: ['#3B9441', '#D92332', '#FFD600', '#4B5563'],
          borderWidth: 0
        }]
      },
      options: {
        plugins: { legend: { position: 'bottom' } }
      }
    });
  }
</script>
@endsection

