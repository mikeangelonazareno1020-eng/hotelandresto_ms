<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transport_services', function (Blueprint $table) {
            $table->id();

            // 🔗 Relation
            $table->string('reservation_id')->nullable(); // links to room_reservations
            $table->foreign('reservation_id')
                ->references('reservation_id')
                ->on('room_reservations')
                ->nullOnDelete();

            // 🧍 Passenger Details
            $table->enum('passenger_type', ['Single', 'Double', 'Group'])->nullable();
            $table->unsignedInteger('group_quantity')->default(1);
            $table->json('luggage')->nullable(); // [{"type":"Suitcase","qty":2,"weight":25}]
            $table->string('transport_type', 50)->nullable(); // e.g., Shuttle, Rent Car, Pickup

            // 🗺️ Location Details
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();


            // 🚗 Pickup Details
            $table->string('pickup_location')->nullable(); // e.g., "NAIA Terminal 3" or "Hotel Lobby"
            $table->string('pickup_address')->nullable();
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->dateTime('pickup_eta')->nullable();
            $table->decimal('transport_rate', 10, 2)->default(0.00);

            // 🚕 Status and Control
            $table->enum('service_status', ['Pending', 'Confirmed', 'In Transit', 'Completed', 'Cancelled'])->default('Pending');
            $table->string('driver_name')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('vehicle_type')->nullable(); // e.g., Sedan, Van, SUV

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_services');
    }
};
