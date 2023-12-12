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
        Schema::create('website_configs', function (Blueprint $table) {
            $table->id()->unique();
            $table->string('var_name')->unique()->comment('Machine readable');
            $table->string('name')->nullable()->comment('Human readable');
            $table->string('type')->nullable();
            $table->string('description')->nullable();
            $table->string('var_value')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_configs');
    }
};
