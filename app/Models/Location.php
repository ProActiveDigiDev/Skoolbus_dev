<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'destination_type',
        'custom_type',
        'location',
        'address',
    ];

    /**
     * Get the bus routes associated with this location.
     */
    public function busRoutesFrom(): HasMany
    {
        return $this->hasMany(BusRoute::class, 'from_location_id');
    }

    /**
     * Get the bus routes associated with this location.
     */
    public function busRoutesTo(): HasMany
    {
        return $this->hasMany(BusRoute::class, 'to_location_id');
    }

}
