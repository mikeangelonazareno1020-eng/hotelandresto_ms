@extends('layout.app')

@section('title', 'Admin Accounts')

@section('content')
<main class="p-6 text-sm">
  <div class="bg-white border rounded-xl shadow p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold text-[#B0452D]">Admin Accounts</h1>
      <form method="GET" action="{{ route('super.accounts') }}" class="flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, role" class="border rounded px-3 py-1.5 w-64">
        <button class="px-3 py-1.5 border rounded">Search</button>
      </form>
    </div>

    <form method="POST" action="{{ route('super.accounts.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
      @csrf
      <div class="md:col-span-2">
        <label class="block text-xs text-gray-600">Name</label>
        <input name="name" class="w-full border rounded px-3 py-1.5" required>
      </div>
      <div class="md:col-span-2">
        <label class="block text-xs text-gray-600">Email</label>
        <input type="email" name="email" class="w-full border rounded px-3 py-1.5" required>
      </div>
      <div>
        <label class="block text-xs text-gray-600">Password</label>
        <input type="password" name="password" class="w-full border rounded px-3 py-1.5" minlength="8" required>
      </div>
      <div>
        <label class="block text-xs text-gray-600">Role</label>
        <select name="role" class="w-full border rounded px-3 py-1.5" required>
          @foreach($roles as $r)
            <option value="{{ $r }}">{{ $r }}</option>
          @endforeach
        </select>
      </div>
      <div class="md:col-span-6 flex items-center gap-3">
        <label class="inline-flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked> Active</label>
        <input name="phone" placeholder="Phone" class="border rounded px-3 py-1.5">
        <input name="address" placeholder="Address" class="border rounded px-3 py-1.5 flex-1">
      </div>
      <div class="md:col-span-6">
        <button class="bg-[#B0452D] text-white px-4 py-2 rounded">Add Admin</button>
      </div>
    </form>
  </div>

  <div class="bg-white border rounded-xl shadow">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-3 py-2 text-left">ID</th>
          <th class="px-3 py-2 text-left">Name</th>
          <th class="px-3 py-2 text-left">Email</th>
          <th class="px-3 py-2 text-left">Role</th>
          <th class="px-3 py-2 text-left">Active</th>
          <th class="px-3 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($admins as $a)
          <tr class="border-t" x-data="{ edit:false }">
            <td class="px-3 py-2">{{ $a->admin_id ?? $a->id }}</td>
            <td class="px-3 py-2">
              <span x-show="!edit">{{ $a->name }}</span>
              <form x-show="edit" method="POST" action="{{ route('super.accounts.update', $a) }}" class="flex flex-wrap gap-2 items-end">
                @csrf @method('PUT')
                <input name="name" value="{{ $a->name }}" class="border rounded px-2 py-1 w-48">
                <input type="email" name="email" value="{{ $a->email }}" class="border rounded px-2 py-1 w-56">
                <input type="password" name="password" placeholder="New password (optional)" class="border rounded px-2 py-1 w-56">
                <select name="role" class="border rounded px-2 py-1">
                  @foreach($roles as $r)
                    <option value="{{ $r }}" @selected($a->role===$r)>{{ $r }}</option>
                  @endforeach
                </select>
                <label class="inline-flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($a->is_active)> Active</label>
                <button class="px-2 py-1 border rounded bg-[#B0452D] text-white">Save</button>
                <button type="button" @click="edit=false" class="px-2 py-1 border rounded">Cancel</button>
              </form>
            </td>
            <td class="px-3 py-2">{{ $a->email }}</td>
            <td class="px-3 py-2">{{ $a->role }}</td>
            <td class="px-3 py-2">{{ $a->is_active ? 'Yes' : 'No' }}</td>
            <td class="px-3 py-2">
              <div x-show="!edit" class="flex gap-2">
                <button type="button" @click="edit=true" class="px-2 py-1 border rounded">Edit</button>
                <form method="POST" action="{{ route('super.accounts.destroy', $a) }}" onsubmit="return confirm('Delete this admin?')">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 border rounded text-red-600">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">No admin accounts found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</main>
@endsection
