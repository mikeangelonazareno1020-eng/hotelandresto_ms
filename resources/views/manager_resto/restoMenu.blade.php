@extends('layout.app')

@section('title', 'Inventory')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Inventory</h1>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($menus as $m)
            <div class="bg-white rounded shadow p-4 flex flex-col">
                <div class="flex items-start gap-3">
                    <img src="{{ $m->image_url ? asset('images/menus/' . $m->image_url) : asset('images/menus/noimage.jpg') }}" class="w-20 h-20 object-cover rounded" alt="{{ $m->name }}">
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">{{ $m->name }}</h3>
                            @if($m->is_available)
                                <span class="text-xs px-2 py-0.5 rounded bg-green-100 text-green-700">Available</span>
                            @else
                                <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700">Unavailable</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">{{ $m->category }}</div>
                        <div class="text-sm mt-1">₱ {{ number_format((float)$m->price, 2) }}</div>
                        <p class="text-gray-500 text-xs mt-1 line-clamp-2">{{ $m->description }}</p>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm">Stock: <span class="font-semibold">{{ (int)$m->stock_quantity }}</span></div>
                    <form action="{{ route('restomanager.menu.toggle', $m) }}" method="POST">
                        @csrf
                        <input type="hidden" name="set_stock" value="1">
                        <button type="submit" class="px-3 py-1 rounded text-white text-sm {{ $m->is_available ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' }}">
                            {{ $m->is_available ? 'Mark Unavailable' : 'Mark Available' }}
                        </button>
                    </form>
                </div>

                <div class="mt-3 flex items-center gap-3">
                    <form action="{{ route('restomanager.menu.stock', $m) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="number" name="amount" min="1" value="1" class="w-24 border rounded px-2 py-1" required>
                        <button type="submit" class="px-3 py-1 rounded bg-blue-600 text-white text-sm hover:bg-blue-700">Add Stock</button>
                    </form>

                    <form action="{{ route('restomanager.menu.remove', $m) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="number" name="amount" min="1" value="1" class="w-24 border rounded px-2 py-1" required>
                        <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700">Remove Stock</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="col-span-full text-center text-gray-500">No items yet.</p>
        @endforelse
    </div>
</div>
@endsection
