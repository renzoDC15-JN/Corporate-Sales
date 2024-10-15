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
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropUnique('prospects_email_unique'); // Drop unique constraint for email
            $table->dropUnique('prospects_mobile_number_unique'); // Drop unique constraint for mobile_number
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->unique('email'); // Re-add unique constraint for email
            $table->unique('mobile_number'); // Re-add unique constraint for mobile_number
        });
    }
};
