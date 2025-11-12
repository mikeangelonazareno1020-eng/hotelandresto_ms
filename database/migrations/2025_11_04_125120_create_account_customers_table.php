<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('account_customers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            // 🧾 Basic Identifiers
            $table->string('customer_id', 20)->unique(); // e.g., HC100001

            // 🧍 Personal Information
            $table->string('first_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->string('last_name', 50);
            $table->string('email', 100)->unique();
            $table->string('customer_password'); // hashed
            $table->string('phone', 20)->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->string('profile_image')->nullable();

            // 📍 Address Info
            $table->string('region')->nullable();
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('barangay')->nullable();
            $table->string('street')->nullable();
            $table->text('other_address')->nullable();

            // If you want to include `is_new` column:
            // $table->boolean('is_new')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_customers');
    }
};
