<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('forgot_password_admin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->string('admin_name')->nullable();
            $table->string('email')->index();
            $table->string('token')->unique();
            $table->enum('role', ['Administrator', 'Hotel Manager', 'Restaurant Manager', 'Cashier', 'Frontdesk'])->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_used')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('device')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forgot_password_admin');
    }
};
