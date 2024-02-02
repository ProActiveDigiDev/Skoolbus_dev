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
        Schema::create('bus_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('bus_driver_phone');
            $table->string('bus_driver_license');
            $table->date('bus_driver_license_expiry');
            $table->boolean('bus_driver_status')->default(false);
            $table->timestamps();
        });
    }

    protected $casts = [
        'bus_driver_license' => 'array',
    ];

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_drivers');
    }
};
