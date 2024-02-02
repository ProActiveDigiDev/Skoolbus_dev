<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusDriver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bus_driver_phone',
        'bus_driver_license',
        'bus_driver_license_expiry',
        'bus_driver_status',
    ];


    protected $casts = [
        'bus_driver_license' => 'array',
        'bus_driver_status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function busdriver(): HasMany
    {
        return $this->hasMany(UserBooking::class, 'busroute_driver', 'id');
    }
}
