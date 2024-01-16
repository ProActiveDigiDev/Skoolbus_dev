<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisteredBus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_name',
        'bus_registration_number',
        'bus_driver_name',
        'bus_routes',
        'bus_capacity',
        'bus_status',
        'bus_image',
    ];

    protected $casts = [
        'bus_routes' => 'array',
    ];

    public function busRoutes()
    {
        return $this->hasMany(BusRoute::class);
    }
}
