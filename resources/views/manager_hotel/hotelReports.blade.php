@extends('layout.app')

@section('title', 'Hotel Reports')

@section('content')
<main class="p-6 text-sm">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-xl font-semibold text-[#B0452D]">Hotel Reports</h1>
    @include('reports.print_controls')
  </div>

  <form method="GET" class="bg-white border rounded-xl shadow p-4 mb-4 flex flex-wrap gap-3 items-end">
    <div>
      <label class="block text-xs text-gray-600">From</label>
      <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="border rounded px-3 py-1.5">
    </div>
    <div>
      <label class="block text-xs text-gray-600">To</label>
      <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="border rounded px-3 py-1.5">
    </div>
    <div>
      <button class="px-4 py-2 border rounded">Apply</button>
    </div>
  </form>

  <section id="report-content" class="bg-white border rounded-xl shadow p-4 print:p-0">
    <h2 class="text-lg font-semibold mb-3">Summary</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
      <div class="p-3 border rounded">
        <div class="text-gray-500">Booked</div>
        <div class="text-2xl font-bold">{{ $summary['booked'] ?? 0 }}</div>
      </div>
      <div class="p-3 border rounded">
        <div class="text-gray-500">Checked In</div>
        <div class="text-2xl font-bold">{{ $summary['checked_in'] ?? 0 }}</div>
      </div>
      <div class="p-3 border rounded">
        <div class="text-gray-500">Checked Out</div>
        <div class="text-2xl font-bold">{{ $summary['checked_out'] ?? 0 }}</div>
      </div>
      <div class="p-3 border rounded">
        <div class="text-gray-500">Cancelled</div>
        <div class="text-2xl font-bold">{{ $summary['cancelled'] ?? 0 }}</div>
      </div>
    </div>

    <h2 class="text-lg font-semibold mb-3">Revenue</h2>
    <div class="grid grid-cols-2 md:grid-cols-2 gap-3">
      <div class="p-3 border rounded">
        <div class="text-gray-500">Receipts</div>
        <div class="text-2xl font-bold">{{ $totals['receipts'] ?? 0 }}</div>
      </div>
      <div class="p-3 border rounded">
        <div class="text-gray-500">Total Revenue</div>
        <div class="text-2xl font-bold">₱ {{ number_format((float)($totals['revenue'] ?? 0), 2) }}</div>
      </div>
    </div>
  </section>
</main>
@endsection
