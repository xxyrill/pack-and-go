<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_list_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('user_driver_id')->nullable();
            $table->string('pick_up_longitude')->nullable();
            $table->string('pick_up_latitude')->nullable();
            $table->string('drop_off_longitude')->nullable();
            $table->string('drop_off_latitude')->nullable();
            $table->text('pickup_house_information')->nullable();
            $table->boolean('pickup_helper_stairs')->nullable();
            $table->boolean('pickup_helper_elivator')->nullable();
            $table->boolean('pickup_ring_door')->nullable();
            $table->text('pickup_adition_remarks')->nullable();
            $table->text('drop_off_house_information')->nullable();
            $table->dateTime('booking_date_time_start')->nullable();
            $table->dateTime('booking_date_time_end')->nullable();
            $table->boolean('need_helper')->nullable()->default(false);
            $table->string('alt_contact_number_one')->nullable();
            $table->string('alt_contact_number_two')->nullable();
            $table->string('alt_email')->nullable();
            $table->double('price')->nullable();
            $table->string('status')->nullable();
            $table->string('order_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_list_id')->references('id')->on('vehicle_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
