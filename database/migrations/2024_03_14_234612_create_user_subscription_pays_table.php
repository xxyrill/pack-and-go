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
        Schema::create('user_subscription_pays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_subscription_id')->nullable();
            $table->date('date_pay')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('user_subscription_id')->references('id')->on('user_subscriptions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscription_pays');
    }
};
