<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // =============== CREATE TABLE ===============
        if (!Schema::hasTable('transport_vehicle_locations')) {
            Schema::create('transport_vehicle_locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vehicle_id')
                    ->constrained('transport_vehicles')
                    ->onDelete('cascade');
                $table->decimal('latitude', 10, 6);
                $table->decimal('longitude', 10, 6);
                $table->timestamps();

                // Primary index for faster lookup by vehicle + id
                $table->index(['vehicle_id', 'id'], 'tvl_vehicle_id_id_idx');
            });
        }

        // =============== BACKFILL VEHICLE REFERENCES ===============
        try {
            DB::statement("
                INSERT IGNORE INTO transport_vehicles (id, plate_no, created_at, updated_at)
                SELECT DISTINCT vehicle_id, CONCAT('TEMP-', vehicle_id), NOW(), NOW()
                FROM transport_vehicle_locations
                WHERE vehicle_id IS NOT NULL
                  AND vehicle_id NOT IN (SELECT id FROM transport_vehicles)
            ");
        } catch (\Throwable $e) {
            // Silent fail for existing or missing references
        }

        // =============== ADD OPTIMIZED INDEXES ===============
        Schema::table('transport_vehicle_locations', function (Blueprint $table) {
            // For time-based queries
            if (!Schema::hasColumn('transport_vehicle_locations', 'recorded_at')) {
                $table->timestamp('recorded_at')->nullable()->after('longitude');
            }

            // Indexes for performance
            $table->index(['vehicle_id', 'recorded_at'], 'tvl_vehicle_recorded_idx');
            $table->index('recorded_at', 'tvl_recorded_idx');
        });

        // =============== VALIDATE FOREIGN KEYS ===============
        $hasFk = DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'transport_vehicle_locations')
            ->where('COLUMN_NAME', 'vehicle_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        if (!$hasFk) {
            Schema::table('transport_vehicle_locations', function (Blueprint $table) {
                $table->dropForeignIfExists(['vehicle_id']);
                $table->foreign('vehicle_id')
                    ->references('id')->on('transport_vehicles')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes and FK safely before removing the table
        if (Schema::hasTable('transport_vehicle_locations')) {
            Schema::table('transport_vehicle_locations', function (Blueprint $table) {
                $table->dropIndexIfExists('tvl_vehicle_id_id_idx');
                $table->dropIndexIfExists('tvl_vehicle_recorded_idx');
                $table->dropIndexIfExists('tvl_recorded_idx');
                $table->dropForeignIfExists(['vehicle_id']);
            });

            Schema::dropIfExists('transport_vehicle_locations');
        }
    }
};
