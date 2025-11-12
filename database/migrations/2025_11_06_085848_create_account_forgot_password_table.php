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
        Schema::create('account_forgot_password', function (Blueprint $table) {
            $table->id();

            // 🧍 Reference to user account (can link to admin, customer, etc.)
            $table->string('email')->index(); // email used for password reset

            // 🔑 Reset token (random secure string)
            $table->string('token', 255)->unique();

            // 🕒 Token expiration (optional for auto-expiry systems)
            $table->timestamp('expires_at')->nullable();

            // ⚙️ Status tracking
            $table->boolean('is_used')->default(false); // if the link has been used
            $table->timestamp('used_at')->nullable();

            // 🕓 Laravel timestamps (created_at = requested_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_forgot_password');
    }
};

