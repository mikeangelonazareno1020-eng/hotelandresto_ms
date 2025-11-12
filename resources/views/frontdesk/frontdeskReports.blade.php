@extends('layout.app')

@section('title', 'Frontdesk Reports')

@section('content')
<div class="min-h-[calc(100vh-2rem)] bg-linear-to-b from-[#FFF9E6] to-[#FEE934]/40 rounded-2xl shadow-lg border border-amber-200 p-6 font-[Poppins]">
  <div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-2">
      <i data-lucide="bar-chart-3" class="w-7 h-7 text-[#B0452D]"></i>
      <h1 class="text-2xl font-bold text-[#B0452D]">Frontdesk Reports</h1>
    </div>
  </div>

  <div class="grid md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Total Reservations</div>
      <div class="text-2xl font-bold text-[#B0452D]" >{{ $summary['total_reservations'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Fully Paid</div>
      <div class="text-2xl font-bold text-green-700">{{ $summary['fully_paid'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Not Fully Paid</div>
      <div class="text-2xl font-bold text-red-600">{{ $summary['not_fully_paid'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Check-ins Today</div>
      <div class="text-2xl font-bold text-[#B0452D]">{{ $summary['checkins_today'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Checkouts Today</div>
      <div class="text-2xl font-bold text-[#B0452D]">{{ $summary['checkouts_today'] }}</div>
    </div>
    <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
      <div class="text-sm text-gray-600">Total Amount (₱)</div>
      <div class="text-2xl font-bold text-[#B0452D]">{{ number_format($summary['total_amount'], 2) }}</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-amber-100 p-4 shadow">
    <h2 class="text-lg font-semibold text-[#B0452D] mb-3">Recent Reservations</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left text-gray-600 border-b">
            <th class="py-2 pr-4">Reservation ID</th>
            <th class="py-2 pr-4">Guest</th>
            <th class="py-2 pr-4">Room</th>
            <th class="py-2 pr-4">Amount</th>
            <th class="py-2 pr-4">Net</th>
            <th class="py-2 pr-4">Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($reservations->take(15) as $r)
          <tr class="border-b hover:bg-gray-50/60">
            <td class="py-2 pr-4 font-medium">{{ $r->reservation_id }}</td>
            <td class="py-2 pr-4">{{ $r->first_name }} {{ $r->last_name }}</td>
            <td class="py-2 pr-4">{{ $r->room_number }}</td>
            <td class="py-2 pr-4">₱{{ number_format($r->total_amount,2) }}</td>
            <td class="py-2 pr-4">₱{{ number_format($r->net_amount,2) }}</td>
            <td class="py-2 pr-4">
              <span class="px-2 py-1 rounded text-xs font-semibold {{ strtolower($r->payment_status)==='fully paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                {{ $r->payment_status }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="py-6 text-center text-gray-500">No reservations yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

