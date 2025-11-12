@extends('layout.app')

@section('title', 'Cashier Logs')

@section('content')
<div class="p-6 space-y-4">
  <h1 class="text-2xl font-bold text-[#D92332]">Logs</h1>

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
    <div>
      <label class="block text-xs text-gray-600">Action</label>
      <input type="text" name="action" value="{{ $filters['action'] ?? '' }}" placeholder="e.g. Login" class="rounded border px-2 py-1 text-sm" />
    </div>
    <button class="px-3 py-2 rounded bg-[#D92332] text-white text-sm">Apply</button>
    @if(($filters['from'] ?? '') || ($filters['to'] ?? '') || ($filters['cashier'] ?? '') || ($filters['action'] ?? ''))
      <a href="{{ route('cashier.logs') }}" class="px-3 py-2 rounded bg-gray-100 text-gray-700 text-sm">Clear</a>
    @endif
  </form>

  <div class="overflow-x-auto bg-white/80 backdrop-blur-md rounded-xl border border-[#FFD600]/40">
    <table class="min-w-full text-sm">
      <thead class="bg-[#FFF9E6]">
        <tr class="text-left">
          <th class="px-3 py-2">When</th>
          <th class="px-3 py-2">Cashier</th>
          <th class="px-3 py-2">Action</th>
          <th class="px-3 py-2">Reference</th>
          <th class="px-3 py-2">IP</th>
          <th class="px-3 py-2">Device</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $l)
          <tr class="border-t border-[#FFD600]/40">
            <td class="px-3 py-2 text-gray-600">@dt($l->logged_at)</td>
            <td class="px-3 py-2 font-medium">{{ $l->cashier_name }}</td>
            <td class="px-3 py-2">{{ $l->action_type }}</td>
            <td class="px-3 py-2">{{ $l->reference_id }}</td>
            <td class="px-3 py-2 text-gray-600">{{ $l->ip_address }}</td>
            <td class="px-3 py-2 text-gray-600">{{ $l->device }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-3 py-3 text-center text-gray-400">No recent updates.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
