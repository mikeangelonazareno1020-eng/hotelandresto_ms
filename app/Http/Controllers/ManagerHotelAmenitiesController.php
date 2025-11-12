<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManagerHotelAmenitiesController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'amenities'); // amenities | extras
        $items = Amenity::orderBy('category')->orderBy('name')->get();

        return view('manager_hotel.hotelAmenities', [
            'tab' => $tab,
            'amenities' => $items->where('is_extra', false),
            'extras' => $items->where('is_extra', true),
            'categories' => ['Room', 'Bathroom', 'Kitchen', 'Extra'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|in:Room,Bathroom,Kitchen,Extra',
            'default_price' => 'nullable|numeric|min:0',
            'code' => 'nullable|string|max:40|unique:room_amenities_extras,code',
        ]);

        $code = $data['code'] ?? Str::upper(Str::slug($data['name'], '_'));
        // Ensure unique code
        $base = $code; $i = 1;
        while (Amenity::where('code', $code)->exists()) { $code = $base . '_' . $i++; }

        Amenity::create([
            'code' => $code,
            'name' => $data['name'],
            'category' => $data['category'],
            'default_price' => $data['default_price'] ?? null,
            'is_extra' => $data['category'] === 'Extra',
        ]);

        return back()->with('success', 'Item added');
    }

    public function update(Request $request, Amenity $amenity)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|string|in:Room,Bathroom,Kitchen,Extra',
            'default_price' => 'nullable|numeric|min:0',
            'code' => 'nullable|string|max:40|unique:room_amenities_extras,code,' . $amenity->id,
        ]);

        $amenity->update([
            'code' => $data['code'] ?: $amenity->code,
            'name' => $data['name'],
            'category' => $data['category'],
            'default_price' => $data['default_price'] ?? null,
            'is_extra' => $data['category'] === 'Extra',
        ]);

        return back()->with('success', 'Item updated');
    }

    public function destroy(Amenity $amenity)
    {
        $amenity->delete();
        return back()->with('success', 'Item deleted');
    }
}
