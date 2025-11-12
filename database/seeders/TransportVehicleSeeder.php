<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransportVehicleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transport_vehicles')->insert([
            [
                'plate_no' => 'ABC-1234',
                'vehicle_type' => 'Van',
                'make' => 'Toyota',
                'model' => 'Hiace',
                'color' => 'White',
                'year' => 2020,
                'capacity' => 12,
                'driver_id' => null,
                'gps_device_id' => 'ESP32-TRACKER-1',
                'last_latitude' => 15.428695,
                'last_longitude' => 120.927514,
                'last_speed' => 0.00,
                'last_heading' => 0,
                'last_seen_at' => now(),
                'status' => 'Active',
                'notes' => 'Assigned for airport pickup.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plate_no' => 'XYZ-5678',
                'vehicle_type' => 'SUV',
                'make' => 'Mitsubishi',
                'model' => 'Montero Sport',
                'color' => 'Black',
                'year' => 2022,
                'capacity' => 7,
                'driver_id' => null,
                'gps_device_id' => 'ESP32-TRACKER-2',
                'last_latitude' => 15.428701,
                'last_longitude' => 120.927517,
                'last_speed' => 0.00,
                'last_heading' => 0,
                'last_seen_at' => now(),
                'status' => 'Active',
                'notes' => 'Hotel service vehicle.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
