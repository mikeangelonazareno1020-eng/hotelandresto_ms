<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports_cashier', function (Blueprint $table) {
            $table->id('report_id');

            // 🧑‍💼 Cashier Info
            $table->string('adminId')->nullable(); // like ADM-10001
            $table->string('cashier_name');

            // 📅 Report Data
            $table->date('report_date');
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_cash', 10, 2)->default(0);
            $table->decimal('total_card', 10, 2)->default(0);
            $table->decimal('total_refund', 10, 2)->default(0);
            $table->decimal('total_discount', 10, 2)->default(0);
            $table->decimal('net_sales', 10, 2)->default(0);

            // 🕒 Additional Info
            $table->string('shift')->nullable(); // Morning / Afternoon / Night
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports_cashier');
    }
};
