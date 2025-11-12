<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_vehicle_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                ->constrained('transport_vehicles')
                ->onDelete('cascade');
            $table->json('path'); // array of {lat, lng}
            $table->unsignedInteger('points_count');
            $table->decimal('start_latitude', 10, 6)->nullable();
            $table->decimal('start_longitude', 10, 6)->nullable();
            $table->decimal('end_latitude', 10, 6)->nullable();
            $table->decimal('end_longitude', 10, 6)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->unsignedBigInteger('saved_by')->nullable();
            $table->timestamps();

            $table->index(['vehicle_id', 'created_at'], 'tvp_vehicle_created_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_vehicle_paths');
    }
};

