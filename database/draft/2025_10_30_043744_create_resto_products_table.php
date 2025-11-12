<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resto_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('unit_price', 10, 2); // Cost to restaurant
            $table->decimal('selling_price', 10, 2); // Price to customer
            $table->string('category')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('admin_name')->nullable();
            $table->timestamps();

            $table->index(['name', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resto_products');
    }
};
