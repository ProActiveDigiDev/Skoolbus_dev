<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from_location_id',
        'to_location_id',
        'timeslot_id',
        'credits_per_ride',
        'days_active',
        'max_riders',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'days_active' => 'array',
    ];

    /**
     * Get the locations associated with this bus route.
     */
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    /**
     * Get the locations associated with this bus route.
     */
    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    /**
     * Get the timeslot associated with this bus route.
     */
    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(Timeslot::class);
    }

}
