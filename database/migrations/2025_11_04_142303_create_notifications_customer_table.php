<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications_customer', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable(); // e.g. booking, order, system
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('customer_id')->on('account_customers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_customer');
    }
};
