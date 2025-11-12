@extends('layout.app')

@section('title', 'Edit Room')

@section('content')
<main class="mt-0 p-6 bg-white/80 backdrop-blur rounded-lg shadow border text-sm">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-[#B0452D]">Edit Room {{ $room->room_number }}</h1>
        <a href="{{ route('hotelmanager.rooms') }}" class="text-xs px-3 py-1.5 rounded border hover:bg-gray-50">Back</a>
    </div>

    <form method="POST" action="{{ route('hotelmanager.rooms.update', $room->room_number) }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Room Number</label>
                <input value="{{ $room->room_number }}" disabled class="w-full border rounded px-3 py-1.5 bg-gray-100 text-gray-500" />
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Room Type</label>
                <select name="room_type" class="w-full border rounded px-3 py-1.5" required>
                    @foreach(['Standard','Matrimonial','Fammily Room'] as $opt)
                        <option value="{{ $opt }}" @selected(old('room_type', $room->room_type) == $opt)>{{ $opt }}</option>
                    @endforeach
                </select>
                @error('room_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Floor</label>
                <input type="number" name="room_floor" value="{{ old('room_floor', $room->room_floor) }}" class="w-full border rounded px-3 py-1.5" required>
                @error('room_floor')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Status</label>
                <select name="room_status" class="w-full border rounded px-3 py-1.5" required>
                    @foreach(['Vacant','Checked In','Checked Out','Maintenance','Booked','Out of Service'] as $st)
                        <option value="{{ $st }}" @selected(old('room_status', $room->room_status) == $st)>{{ $st }}</option>
                    @endforeach
                </select>
                @error('room_status')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Max Occupancy</label>
                <input type="number" name="max_occupancy" value="{{ old('max_occupancy', $room->max_occupancy) }}" class="w-full border rounded px-3 py-1.5" required>
                @error('max_occupancy')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Nightly Rate</label>
                <input type="number" name="room_rate" value="{{ old('room_rate', $room->room_rate) }}" class="w-full border rounded px-3 py-1.5" required>
                @error('room_rate')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Bed Type</label>
                <input type="text" name="bed_type_type" value="{{ old('bed_type_type', data_get($room->bed_type,'type')) }}" class="w-full border rounded px-3 py-1.5" placeholder="e.g. King" required>
                @error('bed_type_type')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Bed Quantity</label>
                <input type="number" name="bed_type_quantity" value="{{ old('bed_type_quantity', (int) data_get($room->bed_type,'quantity')) }}" class="w-full border rounded px-3 py-1.5" required>
                @error('bed_type_quantity')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Amenities</label>
                <div class="border rounded p-2 max-h-48 overflow-auto">
                    @foreach(($amenities['Room'] ?? collect())->merge($amenities['Bathroom'] ?? collect())->merge($amenities['Kitchen'] ?? collect()) as $am)
                        <label class="flex items-center gap-2 text-xs py-1">
                            <input type="checkbox" name="amenity_ids[]" value="{{ $am->id }}"
                                   @checked(collect(old('amenity_ids', $room->amenities->pluck('id')->all()))->contains($am->id))>
                            <span>{{ $am->name }} <span class="text-gray-400">({{ $am->category }})</span></span>
                        </label>
                    @endforeach
                </div>
                <p class="text-[11px] text-gray-500 mt-1">You can also manage amenities in the master list later.</p>
                @error('amenity_ids')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label class="block text-xs text-gray-600 mb-1">Description</label>
            <textarea name="room_description" rows="4" class="w-full border rounded px-3 py-1.5">{{ old('room_description', $room->room_description) }}</textarea>
            @error('room_description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="pt-3">
            <button type="submit" class="bg-[#B0452D] text-white px-4 py-2 rounded hover:opacity-95">Save Changes</button>
        </div>
    </form>
</main>
@endsection
