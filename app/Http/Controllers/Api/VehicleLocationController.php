<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleLocation;
use App\Models\TransportVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleLocationController extends Controller
{
    private function noCache($response)
    {
        return $response
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
    /**
     * Store a new GPS location for a vehicle.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $deviceUid = $request->header('X-DEVICE-UID') ?? $request->header('X-Device-Uid');
        $apiDevice = $request->attributes->get('api_device');
        if (!$deviceUid && $apiDevice && !empty($apiDevice->uid)) {
            $deviceUid = $apiDevice->uid;
        }
        if (!$deviceUid) {
            return $this->noCache(response()->json([
                'message' => 'Missing X-DEVICE-UID header'
            ], 422));
        }

        $vehicle = TransportVehicle::where('gps_device_id', $deviceUid)->first();
        if (!$vehicle) {
            return $this->noCache(response()->json([
                'message' => 'Device UID not registered to any vehicle'
            ], 404));
        }

        $location = VehicleLocation::create([
            'vehicle_id' => $vehicle->id,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
        ]);

        return $this->noCache(response()->json([
            'status' => 'success',
            'data' => $location,
        ], 201));
    }

    /**
     * Get the latest GPS location for a single vehicle.
     */
    public function latestByVehicle($vehicleId)
    {
        $row = DB::table('transport_vehicle_locations as v')
            ->where('v.vehicle_id', $vehicleId)
            ->orderByDesc('v.id')
            ->limit(1)
            ->select(
                'v.vehicle_id',
                'v.latitude',
                'v.longitude',
                'v.created_at',
                DB::raw("CASE WHEN TIMESTAMPDIFF(SECOND, v.created_at, NOW()) <= 60 THEN 'online' ELSE 'offline' END as status")
            )
            ->first();

        if (!$row) {
            return $this->noCache(response()->json(['message' => 'No GPS data for this vehicle'], 404));
        }

        return $this->noCache(response()->json($row));
    }

    /**
     * Get latest GPS location for all vehicles.
     */
    public function latestAll()
    {
        // Use MAX(id) per vehicle for latest row; supported by composite index (vehicle_id, id)
        $sub = DB::table('transport_vehicle_locations')
            ->select('vehicle_id', DB::raw('MAX(id) as max_id'))
            ->groupBy('vehicle_id');

        $rows = DB::table('transport_vehicle_locations as v')
            ->joinSub($sub, 'm', function ($join) {
                $join->on('v.vehicle_id', '=', 'm.vehicle_id')
                     ->on('v.id', '=', 'm.max_id');
            })
            ->select(
                'v.vehicle_id',
                'v.latitude',
                'v.longitude',
                'v.created_at',
                DB::raw("CASE WHEN TIMESTAMPDIFF(SECOND, v.created_at, NOW()) <= 60 THEN 'online' ELSE 'offline' END as status")
            )
            ->get();

        return $this->noCache(response()->json($rows));
    }

    /**
     * Historical path for a vehicle.
     * Query params:
     *  - limit: max points (default 200, max 1000)
     *  - since: ISO datetime (optional)
     *  - until: ISO datetime (optional)
     */
     public function history(Request $request, $vehicleId)
     {
         $limit = (int) $request->query('limit', 200);
         $limit = max(1, min(1000, $limit));

         $since = $request->query('since');
         $until = $request->query('until');

         $q = DB::table('transport_vehicle_locations')
             ->where('vehicle_id', $vehicleId);

         if ($since) {
             $q->where('created_at', '>=', $since);
         }
         if ($until) {
             $q->where('created_at', '<=', $until);
         }

         // Default: last N points by id, then return chronologically
         if (!$since && !$until) {
             $rows = $q->orderByDesc('id')->limit($limit)->get([
                 'latitude', 'longitude', 'created_at'
             ])->reverse()->values();
         } else {
             $rows = $q->orderBy('created_at')->limit($limit)->get([
                 'latitude', 'longitude', 'created_at'
             ]);
         }

         return $this->noCache(response()->json($rows));
     }
}
