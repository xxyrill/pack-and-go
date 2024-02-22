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
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->dropColumn('business_barangay');
            $table->dropColumn('business_city');
            $table->dropColumn('business_province');
            $table->dropColumn('business_postal_code');
        });
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->text('business_complete_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->dropColumn('business_complete_address');
        });
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->string('business_barangay')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_province')->nullable();
            $table->string('business_postal_code')->nullable();
        });
    }
};
