<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VehicleLocation;
use App\Models\TransportVehicle;
use Illuminate\Http\Request;

class GpsController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'latitude'   => 'required|numeric|between:-90,90',
            'longitude'  => 'required|numeric|between:-180,180',
        ]);

        $deviceUid = $request->header('X-DEVICE-UID') ?? $request->header('X-Device-Uid');
        $apiDevice = $request->attributes->get('api_device');
        if (!$deviceUid && $apiDevice && !empty($apiDevice->uid)) {
            $deviceUid = $apiDevice->uid;
        }
        if (!$deviceUid) {
            return response()->json([
                'success' => false,
                'message' => 'Missing X-DEVICE-UID header',
            ], 422);
        }

        $vehicle = TransportVehicle::where('gps_device_id', $deviceUid)->first();
        if (!$vehicle) {
            return response()->json([
                'success' => false,
                'message' => 'Device UID not registered to any vehicle',
            ], 404);
        }

        VehicleLocation::create([
            'vehicle_id' => $vehicle->id,
            'latitude'   => $data['latitude'],
            'longitude'  => $data['longitude'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'GPS data saved successfully',
        ], 201);
    }
}
