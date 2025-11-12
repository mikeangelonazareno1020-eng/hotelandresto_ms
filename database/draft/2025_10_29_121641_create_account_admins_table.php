<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('account_admins', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key (id)

            // Custom formatted admin ID like ADM-10000 + id
            $table->string('adminId')->unique()->nullable();

            // Basic information
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            // Additional profile info
            $table->string('gender')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('profile_image')->nullable();

            // Role and permissions
            $table->string('role')->default('Admin');
            $table->boolean('is_active')->default(true);

            // Security and login tracking
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->rememberToken();

            // Record management
            $table->timestamps();
            $table->softDeletes();
        });

        // Automatically populate adminId after each insert
        DB::unprepared('
            CREATE TRIGGER trg_generate_adminId BEFORE INSERT ON account_admins
            FOR EACH ROW
            BEGIN
                DECLARE nextId INT;
                SELECT AUTO_INCREMENT INTO nextId
                FROM INFORMATION_SCHEMA.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME = "account_admins";
                SET NEW.adminId = CONCAT("ADM-", 10000 + nextId);
            END;
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_generate_adminId');
        Schema::dropIfExists('account_admins');
    }
};
