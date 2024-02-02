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
        Schema::create('emergency_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('has_medical_aid')->default(false)->nullable();
            $table->string('medical_aid_name')->nullable();
            $table->string('medical_aid_plan')->nullable();
            $table->string('medical_aid_main_member_name')->nullable();
            $table->string('medical_aid_main_member_number')->nullable();
            $table->json('medical_aid_dependants')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_information');
    }
};
