<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
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

            // 🏨 Hotel-Specific Information
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->text('address')->nullable();                // full home or billing address
            $table->string('nationality', 100)->nullable();     // for foreign guests
            $table->string('id_type', 50)->nullable();          // e.g., Passport, Driver’s License
            $table->string('id_number', 100)->nullable();
            $table->string('id_image')->nullable();             // path to uploaded ID image
            $table->string('profile_image')->nullable();        // optional profile picture

            // 💳 Payment and Loyalty
            $table->string('payment_preference')->nullable();   // e.g., Cash, Card, GCash
            $table->decimal('credit_balance', 10, 2)->default(0.00);
            $table->unsignedInteger('loyalty_points')->default(0);

            // 🧾 Booking and Stay Preferences
            $table->enum('preferred_room_type', ['Single', 'Double', 'Suite', 'Family'])->nullable();
            $table->text('special_requests')->nullable();       // guest’s recurring requests
            $table->boolean('newsletter_subscribed')->default(false);

            // 🕒 Account Status
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
