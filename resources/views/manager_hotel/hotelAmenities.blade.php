@extends('layout.app')

@section('title', 'Amenities & Extras')

@section('content')
<main class="p-6 bg-white/80 rounded-lg shadow border text-sm" x-data="{ tab: '{{ $tab === 'extras' ? 'extras' : 'amenities' }}' }">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-[#B0452D]">Amenities & Extras</h1>
        <div class="flex gap-2">
            <a href="{{ route('hotelmanager.amenities', ['tab' => 'amenities']) }}" :class="tab==='amenities'?'bg-[#B0452D] text-white':'bg-white'" class="px-3 py-1.5 rounded border">Amenities</a>
            <a href="{{ route('hotelmanager.amenities', ['tab' => 'extras']) }}" :class="tab==='extras'?'bg-[#B0452D] text-white':'bg-white'" class="px-3 py-1.5 rounded border">Extras</a>
        </div>
    </div>

    <div class="mb-6 p-4 border rounded-lg bg-white">
        <form action="{{ route('hotelmanager.amenities.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-xs text-gray-600 mb-1">Name</label>
                <input name="name" class="w-full border rounded px-3 py-1.5" placeholder="e.g. Wi‑Fi" required>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Category</label>
                <select name="category" class="w-full border rounded px-3 py-1.5" required>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" @selected(($tab==='extras' && $cat==='Extra'))>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Default Price</label>
                <input type="number" step="0.01" name="default_price" class="w-full border rounded px-3 py-1.5" placeholder="0.00">
            </div>
            <div>
                <button type="submit" class="bg-[#B0452D] text-white px-4 py-2 rounded w-full">Add</button>
            </div>
        </form>
    </div>

    @php
        $list = $tab === 'extras' ? $extras : $amenities;
    @endphp

    <div class="overflow-x-auto bg-white border rounded-lg">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-2">Code</th>
                    <th class="px-4 py-2">Name</th>
                    <th class="px-4 py-2">Category</th>
                    <th class="px-4 py-2">Default Price</th>
                    <th class="px-4 py-2 w-32">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($list as $item)
                <tr class="border-t" x-data="{ editing: false }">
                    <td class="px-4 py-2">
                        <span>{{ $item->code }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <span>{{ $item->name }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <span>{{ $item->category }}</span>
                    </td>
                    <td class="px-4 py-2">
                        <span>
                            {{ is_null($item->default_price) ? '-' : number_format((float) $item->default_price, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <div x-show="!editing" class="flex gap-2">
                            <button type="button" @click="editing=true" class="px-2 py-1 border rounded">Edit</button>
                            <form method="POST" action="{{ route('hotelmanager.amenities.destroy', $item) }}" onsubmit="return confirm('Delete this item?')">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 border rounded text-red-600">Delete</button>
                            </form>
                        </div>
                        <form x-show="editing" method="POST" action="{{ route('hotelmanager.amenities.update', $item) }}" class="bg-gray-50 p-2 rounded border flex flex-wrap gap-2 items-end">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-[11px] text-gray-600">Code</label>
                                <input name="code" value="{{ $item->code }}" class="border rounded px-2 py-1 w-28">
                            </div>
                            <div>
                                <label class="block text-[11px] text-gray-600">Name</label>
                                <input name="name" value="{{ $item->name }}" class="border rounded px-2 py-1 w-48">
                            </div>
                            <div>
                                <label class="block text-[11px] text-gray-600">Category</label>
                                <select name="category" class="border rounded px-2 py-1">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" @selected($item->category===$cat)>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] text-gray-600">Default Price</label>
                                <input type="number" step="0.01" name="default_price" value="{{ $item->default_price }}" class="border rounded px-2 py-1 w-28">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="px-2 py-1 border rounded bg-[#B0452D] text-white">Save</button>
                                <button type="button" @click="editing=false" class="px-2 py-1 border rounded">Cancel</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td class="px-4 py-6 text-center text-gray-500" colspan="5">No items</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection
