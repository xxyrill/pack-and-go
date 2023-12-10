<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicle_lists', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->softDeletes();
        });
        Artisan::call('db:seed', array('--class' => 'VehicleListSeeder'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_lists');
    }
};
