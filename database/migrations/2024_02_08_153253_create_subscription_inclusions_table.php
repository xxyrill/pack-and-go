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
        Schema::create('subscription_inclusions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscription_id')->nullable();
            $table->string('inclusion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_inclusions');
    }
};
