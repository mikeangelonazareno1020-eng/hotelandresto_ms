@extends('layout.app')

@section('title', 'Super GPS')

@section('content')
<main class="p-6">  
  <div class="mt-6 bg-white border rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold text-[#B0452D] mb-3">GPS Devices</h2>
    <div class="overflow-auto border rounded-lg">
      <table class="min-w-full text-sm">
        <thead class="bg-amber-50">
          <tr>
            <th class="px-3 py-2 text-left">ID</th>
            <th class="px-3 py-2 text-left">Name</th>
            <th class="px-3 py-2 text-left">UID</th>
            <th class="px-3 py-2 text-left">Active</th>
            <th class="px-3 py-2 text-left">Last Used</th>
            <th class="px-3 py-2 text-left">Last IP</th>
            <th class="px-3 py-2 text-left">Created</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse(($devices ?? []) as $d)
            <tr>
              <td class="px-3 py-2">{{ $d->id }}</td>
              <td class="px-3 py-2">{{ $d->name ?? '—' }}</td>
              <td class="px-3 py-2 font-mono">{{ $d->uid ?? '—' }}</td>
              <td class="px-3 py-2">
                @php $on = (bool)($d->is_active ?? false); @endphp
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs {{ $on ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                  <span class="w-1.5 h-1.5 rounded-full {{ $on ? 'bg-green-600' : 'bg-gray-400' }}"></span>
                  {{ $on ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="px-3 py-2 text-gray-600">{{ $d->last_used_at }}</td>
              <td class="px-3 py-2 text-gray-600">{{ $d->last_ip ?? '—' }}</td>
              <td class="px-3 py-2 text-gray-600">{{ $d->created_at }}</td>
            </tr>
          @empty
            <tr><td class="px-3 py-4 text-gray-500" colspan="7">No API devices found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</main>
@endsection
