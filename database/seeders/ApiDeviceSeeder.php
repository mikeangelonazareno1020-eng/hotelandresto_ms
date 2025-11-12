<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ApiDeviceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('api_devices')->insert([
            [
                'name' => 'ESP32 Shuttle 1',
                'uid' => 'esp32-shuttle-1',
                'api_key_hash' => Hash::make('your-long-random-key'),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
