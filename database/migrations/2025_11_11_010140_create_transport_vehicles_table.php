<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transport_vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_no', 20)->unique();
            $table->string('vehicle_type', 40)->nullable(); // Sedan, Van, SUV, Bus
            $table->string('make', 60)->nullable();
            $table->string('model', 60)->nullable();
            $table->string('color', 40)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('capacity')->default(4);

            // Assigned driver (staff)
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('staffs')->nullOnDelete();

            // GPS device binding
            $table->string('gps_device_id', 60)->nullable();

            // Last known telemetry
            $table->decimal('last_latitude', 10, 7)->nullable();
            $table->decimal('last_longitude', 10, 7)->nullable();
            $table->decimal('last_speed', 6, 2)->nullable(); // km/h
            $table->unsignedSmallInteger('last_heading')->nullable(); // degrees
            $table->dateTime('last_seen_at')->nullable();

            $table->enum('status', ['Active', 'Inactive', 'Out of Service'])->default('Active');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_vehicles');
    }
};
