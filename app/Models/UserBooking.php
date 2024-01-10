<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rider_id',
        'busroute_id',
        'busroute_date',
        'busroute_status',
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
}
