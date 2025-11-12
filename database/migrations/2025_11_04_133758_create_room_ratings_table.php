<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_ratings', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id(); // Primary key

            // 🔗 Foreign Keys
            $table->string('room_number', 10);
            $table->string('customer_id', 20);

            // ⭐ Rating Data
            $table->unsignedTinyInteger('rating')->check('rating >= 1 and rating <= 5'); // 1–5 stars
            $table->text('comment')->nullable();

            $table->timestamps();

            // 🔒 Foreign Key Constraints
            $table->foreign('room_number')
                ->references('room_number')
                ->on('room_info')
                ->cascadeOnDelete();

            $table->foreign('customer_id')
                ->references('customer_id')
                ->on('account_customers')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_ratings');
    }
};
