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
        Schema::create('user_driver_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('vehicle_list_id')->nullable();
            $table->string('driver_license_number')->nullable();
            $table->text('front_license_path')->nullable();
            $table->text('back_license_path')->nullable();
            $table->string('make')->nullable();
            $table->string('year_model')->nullable();
            $table->string('plate_number')->nullable();
            $table->boolean('helper')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vehicle_list_id')->references('id')->on('vehicle_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_driver_details');
    }
};