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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('prospect_id')->unique();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_initial')->nullable();
            $table->string('name_extension')->nullable();
            $table->string('company')->nullable();
            $table->string('position_title')->nullable();
            $table->decimal('salary', 15, 2)->nullable();
            $table->string('mid')->nullable();
            $table->string('hloan')->nullable();
            $table->string('email')->unique();
            $table->string('mobile_number')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
