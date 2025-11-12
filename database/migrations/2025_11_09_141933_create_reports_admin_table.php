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
        Schema::create('logs_reports', function (Blueprint $table) {
            $table->id();

            // 🔗 Admin Info (same pattern as logs_admin)
            $table->string('admin_id')->index();           // e.g., ADM-001
            $table->string('admin_name');                  // e.g., Maria Santos
            $table->string('role');                        // e.g., Hotel Frontdesk, Restaurant Cashier, etc.

            // 💼 Report Category
            $table->enum('type', [
                'Hotel',
                'Transport',
                'Restaurant',
                'Staffs',
                'Account',
                'Finance'
            ])->default('Hotel')->index(); // broader classification

            // 🧾 Transaction / Financial Details
            $table->enum('report_type', [
                'Payment',
                'Refund',
                'Receipt',
                'Expense',
                'Revenue',
                'Adjustment',
                'Cash Register',
                'System'
            ])->index();

            $table->string('reference_id')->nullable();    // e.g. RESV-2025-001 or RCP-2025-001
            $table->decimal('amount', 10, 2)->nullable();  // e.g. 2500.00
            $table->string('payment_method')->nullable();  // e.g. Cash, GCash, Card, Online
            $table->string('transaction_status')->nullable(); // Paid, Refunded, Pending, Failed
            $table->text('description')->nullable();       // optional summary or remarks

            // 🌐 System Metadata
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();          // Web, Mobile
            $table->string('browser')->nullable();         // Chrome, Firefox
            $table->string('location')->nullable();        // optional (city, region)

            // 🕓 Time Tracking
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_reports');
    }
};
