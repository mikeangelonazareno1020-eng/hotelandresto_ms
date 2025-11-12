<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resto_menu', function (Blueprint $table) {
            $table->id();
            $table->string('menu_id')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->enum('category', ['Main Course', 'Dessert', 'Drinks', 'Rice', 'Appetizer', 'Combo'])->default('Main Course');
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_available')->default(true);
            $table->json('number_of_orders')->nullable();
            $table->json('main_ingredients')->nullable();
            $table->json('allergens')->nullable();
            $table->decimal('cost_price', 8, 2)->nullable();
            $table->json('production_cost')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resto_menu');
    }
};
