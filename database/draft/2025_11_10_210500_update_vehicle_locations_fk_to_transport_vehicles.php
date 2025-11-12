<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_locations'))
            return;

        // Backfill transport_vehicles for any referenced IDs in vehicle_locations
        try {
            \Illuminate\Support\Facades\DB::statement(
                "INSERT IGNORE INTO transport_vehicles (id, plate_no, created_at, updated_at)
                 SELECT DISTINCT vehicle_id, CONCAT('TEMP-', vehicle_id), NOW(), NOW()
                 FROM vehicle_locations
                 WHERE vehicle_id IS NOT NULL AND vehicle_id NOT IN (SELECT id FROM transport_vehicles)"
            );
        } catch (\Throwable $e) {
        }

        $hasFk = (bool) \Illuminate\Support\Facades\DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'vehicle_locations')
            ->where('COLUMN_NAME', 'vehicle_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        Schema::table('vehicle_locations', function (Blueprint $table) use ($hasFk) {
            if ($hasFk) {
                $table->dropForeign(['vehicle_id']);
            }
            $table->foreign('vehicle_id')
                ->references('id')->on('transport_vehicles')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicle_locations'))
            return;

        $hasFk = (bool) \Illuminate\Support\Facades\DB::table('information_schema.KEY_COLUMN_USAGE')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'vehicle_locations')
            ->where('COLUMN_NAME', 'vehicle_id')
            ->whereNotNull('REFERENCED_TABLE_NAME')
            ->exists();

        Schema::table('vehicle_locations', function (Blueprint $table) use ($hasFk) {
            if ($hasFk) {
                $table->dropForeign(['vehicle_id']);
            }
            $table->foreign('vehicle_id')
                ->references('id')->on('vehicles')
                ->onDelete('cascade');
        });
    }
};
