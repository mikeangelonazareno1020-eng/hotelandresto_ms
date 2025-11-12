@extends('layout.app')

@section('title', 'Edit Menu Item')

@section('content')
<div class="p-6 max-w-3xl">
    <h1 class="text-2xl font-semibold mb-4">Edit Menu Item</h1>

    @if($errors->any())
        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('restomanager.products.update', $menu) }}" method="POST" class="space-y-4 bg-white rounded shadow p-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Menu ID</label>
                <input type="text" class="w-full border rounded px-3 py-2 bg-gray-100" value="{{ $menu->menu_id }}" disabled>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Category</label>
                <input type="text" name="category" class="w-full border rounded px-3 py-2" value="{{ old('category', $menu->category) }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $menu->name) }}" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description', $menu->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Price (₱)</label>
                <input type="number" step="0.01" min="0" name="price" class="w-full border rounded px-3 py-2" value="{{ old('price', $menu->price) }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Stock Quantity</label>
                <input type="number" min="0" name="stock_quantity" class="w-full border rounded px-3 py-2" value="{{ old('stock_quantity', $menu->stock_quantity) }}" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Image URL</label>
                <input type="text" name="image_url" class="w-full border rounded px-3 py-2" value="{{ old('image_url', $menu->image_url) }}">
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save Changes</button>
            <a href="{{ route('restomanager.products') }}" class="px-4 py-2 rounded border hover:bg-gray-50">Cancel</a>
        </div>
    </form>
</div>
@endsection

