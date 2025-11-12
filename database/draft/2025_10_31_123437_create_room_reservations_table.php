<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_id')->unique();
            $table->string('room_number');
            $table->enum('reservation_process', ['Walk In', 'Online'])->nullable();
            $table->string('customer_id', 20)->nullable();

            $table->string('first_name', 55);
            $table->string('middle_name', 55)->nullable();
            $table->string('last_name', 55);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('booking_for', 50)->nullable();
            $table->string('guest_name')->nullable();
            $table->unsignedInteger('guest_quantity')->default(1);


            // ✅ Scheduled check-in/out (date + time)
            $table->date('checkin_date');
            $table->date('checkout_date');
            $table->unsignedInteger('total_days')->default(1);

            // ✅ Actual check-in/out (when guest truly checks in/out)
            $table->dateTime('actual_checkin')->nullable();
            $table->dateTime('actual_checkout')->nullable();
            $table->unsignedInteger('acquired_days')->default(1);

            $table->json('added_amenities')->nullable();
            $table->json('extras')->nullable();
            $table->text('special_request')->nullable();
            $table->string('arrival_time', 50)->nullable();

            $table->decimal('room_charge', 10, 2)->default(0.00);
            $table->decimal('amenities_charge', 10, 2)->default(0.00);
            $table->decimal('extra_charge', 10, 2)->default(0.00);
            $table->decimal('transport_charge', 10, 2)->default(0.00);

            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->json('net_amount')->nullable();


            $table->enum('payment_status', ['Not Fully Paid', 'Fully Paid'])->default('Not Fully Paid');

            $table->enum('reservation_status', ['Booked', 'Checked In', 'Checked Out', 'Cancelled', 'No Show', 'Pending'])->default('Booked');

            $table->dateTime('cancel_date')->nullable();
            $table->json('cancel_list')->nullable();
            $table->text('cancel_reason')->nullable();

            $table->boolean('is_new')->default(true);

            $table->timestamps();

            // Index
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_reservations');
    }
};
