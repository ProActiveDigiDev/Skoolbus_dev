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
        Schema::create('user_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('rider_id')->constrained('riders')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('busroute_id')->constrained('busroutes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('busroute_date');
            $table->string('busroute_status')->default('pending')->nullable();
            $table->foreignId('busroute_driver')->nullable()->constrained('bus_drivers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('busroute_pickup')->default(false)->nullable();
            $table->boolean('busroute_dropoff')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bookings');
    }
};
