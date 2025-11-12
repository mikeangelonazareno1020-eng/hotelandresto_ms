@extends('layout.app')

@section('title', 'Resto Products')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Restaurant Products</h1>

    @if(session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
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

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3">Menu ID</th>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Stock</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $m)
                    <tr class="border-t">
                        <td class="px-4 py-2 font-mono text-xs text-gray-600">{{ $m->menu_id }}</td>
                        <td class="px-4 py-2">
                            <div class="font-medium">{{ $m->name }}</div>
                            <div class="text-gray-500 text-xs truncate max-w-[360px]">{{ $m->description }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $m->category }}</td>
                        <td class="px-4 py-2">₱ {{ number_format((float)$m->price, 2) }}</td>
                        <td class="px-4 py-2">{{ (int)$m->stock_quantity }}</td>
                        <td class="px-4 py-2">
                            @if($m->is_available)
                                <span class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-700 text-xs">Available</span>
                            @else
                                <span class="inline-block px-2 py-0.5 rounded bg-red-100 text-red-700 text-xs">Unavailable</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2 whitespace-nowrap">
                            <a href="{{ route('restomanager.products.edit', $m) }}" class="text-blue-600 hover:underline">Edit</a>
                            <form action="{{ route('restomanager.products.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Delete this item? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">No menu items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

