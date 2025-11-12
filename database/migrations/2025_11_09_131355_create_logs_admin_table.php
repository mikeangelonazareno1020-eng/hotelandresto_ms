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
        Schema::create('logs_admin', function (Blueprint $table) {
            $table->id();

            // 🔗 Admin Identity
            $table->string('admin_id')->index();          // e.g. ADM-001
            $table->string('admin_name');                 // e.g. John Doe
            $table->string('role');                       // e.g. Hotel Manager, Cashier, etc.

            // 🏢 Log Classification
            $table->enum('type', [
                'Hotel',
                'Transport',
                'Restaurant',
                'Staffs',
                'Account'
            ])->index(); // Enables filtering by category

            // 🧾 Log Details
            $table->string('action_type');                // e.g. Login, Logout, Update, Delete
            $table->string('reference_id')->nullable();   // e.g. RESV-2025-001
            $table->text('description')->nullable();      // Optional detailed explanation
            $table->string('log_type')->nullable();       // e.g. System, Activity, Security

            // 🌐 System Metadata
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();         // Web, Mobile, etc.
            $table->string('browser')->nullable();        // Chrome, Edge, Safari...
            $table->string('location')->nullable();       // Optional (e.g., Dagupan City, PH)

            // 🕓 Time of action
            $table->timestamp('logged_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_admin');
    }
};
