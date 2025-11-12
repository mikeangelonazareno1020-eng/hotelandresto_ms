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
        Schema::create('room_reports', function (Blueprint $table) {
            $table->id();

            // 🏨 String IDs
            $table->string('reservation_id');
            $table->string('customer_id');
            $table->string('room_number', 50);


            $table->enum('report_type', ['Plumbing', 'Electrical', 'HVAC', 'Other']);
            $table->text('report');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // 🔗 Optional foreign key relationships
            $table->foreign('reservation_id')->references('reservation_id')->on('room_reservations')->onDelete('cascade');
            $table->foreign('customer_id')->references('customer_id')->on('account_customers')->onDelete('cascade');
            $table->foreign('room_number')->references('room_number')->on('room_info')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_reports');
    }
};
