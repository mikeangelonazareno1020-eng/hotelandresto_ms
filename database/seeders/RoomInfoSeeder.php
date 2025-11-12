<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomInfoSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Safe cleanup (no foreign key conflict)
        DB::table('room_info')->delete();

        $rooms = [];
        $roomNumber = 1;

        /**
         * 🏷️ Room Groups: Total = 27
         * 15 Standard (1–4 single beds)
         * 6 Matrimonial
         * 6 Fammily Room
         */
        $roomGroups = [
            // 🛏️ Standard Variants
            [
                'label' => 'Single Bed',
                'room_type' => 'Standard',
                'rate' => 1100,
                'bed' => ['type' => 'Single', 'quantity' => 1],
                'max' => 2,
                'count' => 5,
            ],
            [
                'label' => '2 Single Bed',
                'room_type' => 'Standard',
                'rate' => 1550,
                'bed' => ['type' => 'Single', 'quantity' => 2],
                'max' => 3,
                'count' => 4,
            ],
            [
                'label' => '3 Single Bed',
                'room_type' => 'Standard',
                'rate' => 2150,
                'bed' => ['type' => 'Single', 'quantity' => 3],
                'max' => 4,
                'count' => 3,
            ],
            [
                'label' => '4 Single Bed',
                'room_type' => 'Standard',
                'rate' => 2400,
                'bed' => ['type' => 'Single', 'quantity' => 4],
                'max' => 5,
                'count' => 3,
            ],

            // 💕 Matrimonial
            [
                'label' => 'Matrimonial',
                'room_type' => 'Matrimonial',
                'rate' => 1550,
                'bed' => ['type' => 'Queen', 'quantity' => 1],
                'max' => 2,
                'count' => 6,
            ],

            // 👨‍👩‍👧 Family Room
            [
                'label' => 'Family Room',
                'room_type' => 'Fammily Room', // ⚠️ ENUM exact spelling
                'rate' => 2600,
                'bed' => ['type' => 'King', 'quantity' => 2],
                'max' => 6,
                'count' => 6,
            ],
        ];

        foreach ($roomGroups as $group) {
            for ($i = 0; $i < $group['count']; $i++) {
                $floor = ceil($roomNumber / 6);

                // 🎛️ Base Amenities
                $amenities = ['WiFi', 'TV', 'Aircon'];

                // Add conditional amenities
                if ($group['room_type'] === 'Matrimonial') {
                    $amenities[] = 'Hot Shower';
                } elseif ($group['room_type'] === 'Fammily Room') {
                    $amenities = array_merge($amenities, ['Mini Bar', 'Bathtub', 'Living Area']);
                } elseif ($group['room_type'] === 'Standard' && $group['bed']['quantity'] >= 2) {
                    $amenities[] = 'Hot Shower';
                }

                $rooms[] = [
                    'room_number' => str_pad($roomNumber, 3, '0', STR_PAD_LEFT),
                    'room_type' => $group['room_type'],
                    'room_floor' => $floor,
                    'room_description' => "A {$group['label']} room offering comfort and affordability.",
                    'room_status' => 'Vacant',

                    // 🛏️ Room structure
                    'bed_type' => json_encode($group['bed']),
                    'max_occupancy' => $group['max'],
                    'room_amenities' => json_encode($amenities),
                    'room_rate' => $group['rate'],
                    'room_reservations' => json_encode([]),

                    // 🍽️ Dining / Bath / Kitchen JSON
                    'dining_table' => json_encode([
                        'tables' => rand(1, 2),
                        'chairs' => rand(2, 4),
                    ]),
                    'bathroom' => json_encode([
                        'bathrooms' => 1,
                        'toilet' => 1,
                        'shower' => 1,
                        'bathtub' => in_array('Bathtub', $amenities) ? 1 : 0,
                        'basin' => 1,
                    ]),
                    'kitchen' => json_encode([
                        'type' => $group['room_type'] === 'Fammily Room' ? 'Mini Kitchen' : 'None',
                        'fridge' => $group['room_type'] === 'Fammily Room' ? 1 : 0,
                        'sink' => 1,
                        'stove' => $group['room_type'] === 'Fammily Room' ? 'Gas' : 'None',
                    ]),

                    'ratings' => json_encode([]),
                    'reservation_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $roomNumber++;
            }
        }

        DB::table('room_info')->insert($rooms);

        echo "✅ Seeded " . count($rooms) . " rooms successfully (" .
            "15 Standard, 6 Matrimonial, 6 Fammily Room)\n";
    }
}
