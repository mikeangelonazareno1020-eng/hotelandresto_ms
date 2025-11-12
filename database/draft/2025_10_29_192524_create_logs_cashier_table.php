<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logs_cashier', function (Blueprint $table) {
            $table->id('log_id');

            // 🧑‍💼 Cashier Info
            $table->string('adminId')->nullable(); // like ADM-10001
            $table->string('cashier_name');

            // 🧭 Action Details
            $table->string('action_type'); // e.g. Create Order, Cancel Order, Login, etc.
            $table->string('reference_id')->nullable(); // could be order_id or report_id
            $table->text('description')->nullable();

            // 💻 Device Info
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();

            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_cashier');
    }
};
