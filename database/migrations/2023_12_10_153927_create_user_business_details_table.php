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
        Schema::create('user_business_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('business_name')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_barangay')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_postal_code')->nullable();
            $table->string('business_permit_number')->nullable();
            $table->string('business_tourism_number')->nullable();
            $table->text('business_contact_person')->nullable();
            $table->string('business_contact_person_number')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_business_details');
    }
};
