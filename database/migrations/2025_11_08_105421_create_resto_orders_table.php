<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resto_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('daily_order_number');
            $table->string('order_id')->unique();

            $table->enum('order_type', ['Dine In', 'Takeout'])->default('Dine In');

            $table->unsignedBigInteger('admin_id');
            $table->string('cashier_name')->nullable();

            $table->json('main_dish')->nullable();
            $table->json('dessert')->nullable();
            $table->json('drinks')->nullable();
            $table->json('rice')->nullable();
            $table->json('appetizer')->nullable();
            $table->json('combo')->nullable();

            $table->text('special_request')->nullable();

            $table->decimal('total_amount', 10, 2)->default(0);

            $table->enum('status', ['Pending', 'Preparing', 'Served', 'Cancelled'])->default('Pending');

            $table->text('cancel_reason')->nullable();

            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['ordered_at', 'admin_id']);
            $table->index(['daily_order_number', 'ordered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resto_orders');
    }
};
