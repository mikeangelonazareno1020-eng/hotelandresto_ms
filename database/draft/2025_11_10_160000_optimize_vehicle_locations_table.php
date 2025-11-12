<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add indexes to speed up frequent writes and reads.
     */
    public function up(): void
    {
        Schema::table('vehicle_locations', function (Blueprint $table) {
            // For latest-per-vehicle by id (fast when using MAX(id))
            $table->index(['vehicle_id', 'id'], 'vl_vehicle_id_id_idx');

            // For latest-per-vehicle by time and time range queries
            $table->index(['vehicle_id', 'recorded_at'], 'vl_vehicle_recorded_idx');

            // For global pruning/retention queries by time
            $table->index('recorded_at', 'vl_recorded_idx');
        });
    }

    /**
     * Rollback indexes.
     */
    public function down(): void
    {
        Schema::table('vehicle_locations', function (Blueprint $table) {
            $table->dropIndex('vl_vehicle_id_id_idx');
            $table->dropIndex('vl_vehicle_recorded_idx');
            $table->dropIndex('vl_recorded_idx');
        });
    }
};

