<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_info', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id(); // Primary key (room_info.id)

            // Room Details
            $table->string('room_number', 10)->unique();
            $table->enum('room_type', ['Standard', 'Deluxe', 'Suite']);
            $table->integer('room_floor');
            $table->text('room_description')->nullable();
            $table->enum('room_status', [
                'Vacant',
                'Checked-In',
                'Booked',
                'Out of Service'
            ])->default('Vacant');
            $table->enum('room_operation', ['Maintenance', 'Housekeeping'])->nullable();

            $table->json('bed_type'); // { "type": "King", "quantity": 1 }
            $table->integer('max_occupancy');
            $table->json('room_amenities')->nullable();
            $table->integer('room_rate');

            $table->json('room_reservations')->nullable(); // Store multiple reservations JSON

            $table->json('dining_table')->nullable(); // { "tables": 1, "chairs": 4 }
            $table->json('bathroom')->nullable(); // { "bathrooms": 1, "toilet": 1, "shower": 1, "bathtub": 1, "basin": 1 }
            $table->json('kitchen')->nullable(); // { "type": "Open", "fridge": 1, "sink": 1, "stove": "Gas" }

            // Id's
            $table->string('reservation_id')->nullable();

            $table->timestamps();

            // 🔒 Foreign Keys
            $table->foreign('reservation_id')
                ->references('reservation_id')
                ->on('room_reservations')
                ->nullOnDelete();

        });

    }

    public function down(): void
    {
        Schema::dropIfExists('room_info');
    }
};
