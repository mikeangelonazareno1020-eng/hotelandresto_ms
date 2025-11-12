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
        Schema::create('contact_message', function (Blueprint $table) {
            $table->id();

            // 🧾 Match with fillable fields from the model
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->enum('status', ['Pending', 'Read', 'Replied'])->default('Pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_message');
    }
};
