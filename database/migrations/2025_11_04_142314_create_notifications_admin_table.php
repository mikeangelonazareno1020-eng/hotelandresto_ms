<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications_admin', function (Blueprint $table) {
            $table->id();
            $table->string('admin_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('type')->nullable(); // e.g. booking, order, system
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('admin_id')->references('admin_id')->on('account_admins')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_admin');
    }
};
