<?php

namespace App\Http\Controllers;

use App\Models\GpsData;
use Illuminate\Http\Request;
use App\Models\VehicleLocation;
use App\Models\TransportVehicle;
use App\Models\TransportVehiclePath;
use App\Models\TransportVehiclePathSave;

class AdministratorGpsController extends Controller
{
    public function locations()
    {
        $vehicleIndex = TransportVehicle::select(
                'id','plate_no','gps_device_id','status','vehicle_type','make','model'
            )
            ->orderBy('plate_no')
            ->get()
            ->keyBy('id');
        $vehicleTypes = TransportVehicle::whereNotNull('vehicle_type')
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');
        return view('admin.gps_tracking.adminTracker', compact('vehicleIndex','vehicleTypes'));
    }

    public function services()
    {
        $services = \App\Models\TransportService::orderByDesc('created_at')->get();
        return view('admin.gps_tracking.adminTransportServices', compact('services'));
    }

    public function serviceCreate()
    {
        return view('admin.gps_tracking.adminTransportBookPage');
    }

    public function serviceStore(Request $request)
    {
        $data = $request->validate([
            'reservation_id' => 'nullable|string|max:50',
            'transport_type' => 'required|string|max:50',
            'passenger_type' => 'required|string|in:Single,Double,Group',
            'group_quantity' => 'nullable|integer|min:1|required_if:passenger_type,Group',
            'pickup_eta' => 'required|date',
            'pickup_location' => 'nullable|string|max:120',
            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'transport_rate' => 'nullable|numeric|min:0',
            'driver_name' => 'nullable|string|max:120',
            'vehicle_plate' => 'nullable|string|max:40',
            'vehicle_type' => 'nullable|string|max:60',
            'luggage' => 'nullable',

            // address parts for composing pickup_address
            'region' => 'nullable|string|max:120',
            'province' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'barangay' => 'nullable|string|max:120',
            'street' => 'nullable|string|max:200',
        ]);

        // Compose pickup_address from parts when provided
        $address = collect([
            $data['street'] ?? null,
            $data['barangay'] ?? null,
            $data['city'] ?? null,
            $data['province'] ?? null,
            $data['region'] ?? null,
        ])->filter()->implode(', ');

        // Determine safe group quantity for DB constraint (non-null)
        $groupQty = ($data['passenger_type'] === 'Group')
            ? (int) ($data['group_quantity'] ?? 1)
            : 1; // default 1 for Single/Double

        $payload = [
            'reservation_id' => $data['reservation_id'] ?? null,
            'passenger_type' => $data['passenger_type'],
            'group_quantity' => $groupQty,
            'luggage' => $request->input('luggage') ?: null,
            'transport_type' => $data['transport_type'],
            'pickup_location' => $data['pickup_location'] ?? null,
            'pickup_address' => $address ?: null,
            'pickup_latitude' => $data['pickup_latitude'] ?? null,
            'pickup_longitude' => $data['pickup_longitude'] ?? null,
            'pickup_eta' => $data['pickup_eta'],
            'transport_rate' => $data['transport_rate'] ?? 0,
            'service_status' => 'Pending',
            'driver_name' => $data['driver_name'] ?? null,
            'vehicle_plate' => $data['vehicle_plate'] ?? null,
            'vehicle_type' => $data['vehicle_type'] ?? null,
        ];

        \App\Models\TransportService::create($payload);

        return redirect()->route('admin.gps.services')->with('success', 'Transport service created');
    }

    public function trips()
    {
        $paths = TransportVehiclePathSave::orderByDesc('created_at')->paginate(12);
        return view('admin.gps_tracking.adminGpsTrips', compact('paths'));
    }

    public function vehicles()
    {
        $vehicles = \App\Models\TransportVehicle::with('driver')->orderBy('plate_no')->get();
        return view('admin.gps_tracking.adminVehicles', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $gps = GpsData::create([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json(['success' => true, 'data' => $gps], 200);
    }

    public function update(Request $request)
    {
        $vehicle_id = $request->query('vehicle_id');
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        VehicleLocation::create([
            'vehicle_id' => $vehicle_id,
            'latitude' => $lat,
            'longitude' => $lng,
        ]);

        return response()->json(['status' => 'success']);
    }

    public function vehiclesStore(Request $request)
    {
        $data = $request->validate([
            'plate_no' => 'required|string|max:20|unique:transport_vehicles,plate_no',
            'gps_device_id' => 'nullable|string|max:60',
        ]);
        TransportVehicle::create($data);
        return back()->with('success', 'Vehicle created');
    }

    public function vehiclesUpdate(Request $request, TransportVehicle $vehicle)
    {
        $data = $request->validate([
            'plate_no' => 'required|string|max:20|unique:transport_vehicles,plate_no,' . $vehicle->id,
            'gps_device_id' => 'nullable|string|max:60',
        ]);
        $vehicle->update($data);
        return back()->with('success', 'Vehicle updated');
    }

    public function vehiclesDestroy(TransportVehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success', 'Vehicle deleted');
    }

    public function vehiclesSavePath(Request $request, TransportVehicle $vehicle)
    {
        $data = $request->validate([
            'points' => 'required|array|min:2',
            'points.*.lat' => 'required|numeric|between:-90,90',
            'points.*.lng' => 'required|numeric|between:-180,180',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
        ]);

        $points = array_map(function ($p) {
            return [
                'lat' => (float) $p['lat'],
                'lng' => (float) $p['lng'],
            ];
        }, $data['points']);

        $first = $points[0];
        $last = $points[count($points) - 1];

        $saved = TransportVehiclePath::create([
            'vehicle_id' => $vehicle->id,
            'path' => $points,
            'points_count' => count($points),
            'start_latitude' => $first['lat'],
            'start_longitude' => $first['lng'],
            'end_latitude' => $last['lat'],
            'end_longitude' => $last['lng'],
            'started_at' => $data['started_at'] ?? null,
            'ended_at' => $data['ended_at'] ?? null,
            'saved_by' => optional($request->user())->id,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'id' => $saved->id,
            ]);
        }

        return back()->with('success', 'Path saved');
    }

    public function trackerSavePath(Request $request, TransportVehicle $vehicle)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'points' => 'required|array|min:2',
            'points.*.0' => 'required|numeric|between:-90,90',
            'points.*.1' => 'required|numeric|between:-180,180',
            'started_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
        ]);

        $points = array_map(function ($p) {
            return [ 'lat' => (float) $p[0], 'lng' => (float) $p[1] ];
        }, $data['points']);

        $first = $points[0];
        $last = $points[count($points) - 1];

        $snapshot = [
            'id' => $vehicle->id,
            'plate_no' => $vehicle->plate_no,
            'vehicle_type' => $vehicle->vehicle_type,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'color' => $vehicle->color,
            'year' => $vehicle->year,
            'capacity' => $vehicle->capacity,
            'gps_device_id' => $vehicle->gps_device_id,
            'last_latitude' => $vehicle->last_latitude,
            'last_longitude' => $vehicle->last_longitude,
            'last_seen_at' => optional($vehicle->last_seen_at)?->toISOString(),
            'status' => $vehicle->status,
            'driver_id' => $vehicle->driver_id,
        ];

        $saved = TransportVehiclePathSave::create([
            'vehicle_id' => $vehicle->id,
            'name' => $data['name'],
            'path' => $points,
            'points_count' => count($points),
            'start_latitude' => $first['lat'],
            'start_longitude' => $first['lng'],
            'end_latitude' => $last['lat'],
            'end_longitude' => $last['lng'],
            'started_at' => $data['started_at'] ?? null,
            'ended_at' => $data['ended_at'] ?? null,
            'vehicle_snapshot' => $snapshot,
            'saved_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'status' => 'success',
            'id' => $saved->id,
        ], 201);
    }
}
