<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Timeslot extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'departure_time',
    ];

    /**
     * Get the bus routes associated with this timeslot.
     */
    public function busRoutes(): HasMany
    {
        return $this->hasMany(BusRoute::class);
    }

    
}
