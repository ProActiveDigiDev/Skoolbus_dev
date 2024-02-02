<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rider_id',
        'busroute_id',
        'busroute_date',
        'busroute_status',
        'busroute_driver',
        'busroute_pickup',
        'busroute_dropoff',
    ];

    /**
     * Get the user associated with this booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the rider associated with this booking.
     */
    public function rider()
    {
        return $this->belongsTo(Rider::class);
    }

    /**
     * Get the bus route associated with this booking.
     */
    public function busroute()
    {
        return $this->belongsTo(BusRoute::class);
    }

    /**
     * Get the bus driver associated with this booking.
     */
    public function busdriver(): HasOne
    {
        return $this->hasOne(BusDriver::class, 'busroute_driver', 'id');
    }
}
