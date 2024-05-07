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
            $table->text('dti_registration_path')->nullable();
            $table->text('business_permit_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_business_details', function (Blueprint $table) {
            $table->dropColumn('dti_registration_path');
            $table->dropColumn('business_permit_path');
        });
    }
};
