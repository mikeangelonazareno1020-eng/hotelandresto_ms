<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_id')->unique(); // e.g. RCPT-10001
            $table->string('reference_id')->nullable(); // links to room_reservations or resto_orders
            $table->enum('type', ['Hotel', 'Restaurant'])->nullable(); // distinguishes the source

            // 🧍 Customer Details
            $table->string('customer_id', 20)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            // 💵 Financial Details
            $table->json('items')->nullable(); // list of ordered products or room/amenities breakdown
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('amount_tendered', 10, 2)->nullable();
            $table->decimal('change_due', 10, 2)->nullable();

            // 💳 Payment Info
            $table->enum('payment_method', ['Cash', 'E-Wallet', 'Card', 'Bank Transfer'])->nullable();
            $table->json('payment_details')->nullable();

            $table->string('issued_by')->nullable(); // staff or cashier name
            $table->foreignId('admin_id')->nullable()->constrained('account_admins')->onDelete('set null');

            // 📅 Timing
            $table->dateTime('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
