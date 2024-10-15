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
            $table->string('pagibig_id')->nullable();
            $table->string('civil_status_code')->nullable();
            $table->string('gender_code')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('ownership_code')->nullable();
            $table->float('rent_amount')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('employment_tenure')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prospects', function (Blueprint $table) {
            $table->dropColumn('pagibig_id');
            $table->dropColumn('civil_status_code');
            $table->dropColumn('gender_code');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('ownership_code');
            $table->dropColumn('rent_amount');
            $table->dropColumn('employment_status');
            $table->dropColumn('employment_tenure');
        });
    }
};
