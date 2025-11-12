<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('vehicle_locations')) {
            return;
        }

        if (!Schema::hasColumn('vehicle_locations', 'created_at')) {
            Schema::table('vehicle_locations', function (Blueprint $table) {
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('vehicle_locations', 'recorded_at')) {
            Schema::table('vehicle_locations', function (Blueprint $table) {
                $table->dropColumn('recorded_at');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('vehicle_locations')) {
            return;
        }

        if (!Schema::hasColumn('vehicle_locations', 'recorded_at')) {
            Schema::table('vehicle_locations', function (Blueprint $table) {
                $table->timestamp('recorded_at')->nullable();
            });
        }

        if (Schema::hasColumn('vehicle_locations', 'created_at')) {
            Schema::table('vehicle_locations', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
    }
};

