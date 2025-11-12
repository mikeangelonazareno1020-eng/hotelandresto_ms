<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resto_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('resto_products')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->integer('reorder_level')->default(10); // Minimum before warning
            $table->integer('restocked_amount')->nullable();
            $table->date('last_restocked_at')->nullable();
            $table->string('adminId')->nullable();
            $table->string('admin_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resto_inventory');
    }
};
