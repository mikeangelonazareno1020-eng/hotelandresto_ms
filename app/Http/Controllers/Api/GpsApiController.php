<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsApiController extends Controller
{
    // 🛰️ Save GPS data (for a specific driver)
    public function update(Request $request)
    {
        $validated = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        DB::table('gps_locations')->insert([
            'driver_id' => $validated['driver_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'recorded_at' => now(),
        ]);

        // Optional: mark driver online
        DB::table('drivers')
            ->where('id', $validated['driver_id'])
            ->update(['status' => 'online', 'updated_at' => now()]);

        return response()->json(['status' => 'success']);
    }

    // 🌍 Get latest location of a single driver
    public function latestByDriver($driverId)
    {
        $latest = DB::table('gps_locations')
            ->where('driver_id', $driverId)
            ->orderByDesc('id')
            ->first();

        if (!$latest) {
            return response()->json(['message' => 'No GPS data for this driver'], 404);
        }

        return response()->json([
            'driver_id' => $driverId,
            'latitude' => $latest->latitude,
            'longitude' => $latest->longitude,
            'recorded_at' => $latest->recorded_at,
        ]);
    }

    // 🚗 Get latest location for ALL drivers
    public function latestAll()
    {
        $subQuery = DB::table('gps_locations')
            ->select('driver_id', DB::raw('MAX(id) as latest_id'))
            ->groupBy('driver_id');

        $latest = DB::table('gps_locations as g')
            ->joinSub($subQuery, 'latest_sub', function ($join) {
                $join->on('g.id', '=', 'latest_sub.latest_id');
            })
            ->join('drivers as d', 'd.id', '=', 'g.driver_id')
            ->select('d.id as driver_id', 'd.name', 'd.vehicle', 'g.latitude', 'g.longitude', 'g.recorded_at')
            ->get();

        return response()->json($latest);
    }
}
