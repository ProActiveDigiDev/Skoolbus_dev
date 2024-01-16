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
        Schema::create('registered_buses', function (Blueprint $table) {
            $table->id();
            $table->string('bus_name');
            $table->string('bus_registration_number');
            $table->string('bus_driver_name');
            $table->json('bus_routes');
            $table->string('bus_capacity');
            $table->string('bus_status');
            $table->string('bus_image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registered_buses');
    } 
};
