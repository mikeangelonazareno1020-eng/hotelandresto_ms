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
        Schema::create('logs_customer', function (Blueprint $table) {
            $table->id();

            // 🧍 Customer Reference
            $table->string('customer_id', 20)->nullable();
            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('account_customers')
                ->nullOnDelete();

            // 📝 Log Details
            $table->string('log_type', 50)->nullable(); // e.g., "Reservation", "Profile Update", "Login", "Payment"
            $table->string('action', 100)->nullable(); // e.g., "Created Booking", "Cancelled Reservation"
            $table->text('message')->nullable(); // detailed description

            // 📅 Context
            $table->string('ip_address', 45)->nullable();
            $table->string('device', 100)->nullable(); // e.g., "Windows Chrome", "Mobile Safari"
            $table->string('location')->nullable(); // optional, if you want to store geo info

            // 📦 Optional Data
            $table->json('metadata')->nullable(); // e.g., {"reservation_id":"HRES100001","room":"101"}

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_customer');
    }
};
