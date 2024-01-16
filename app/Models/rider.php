<?php

namespace App\Models;

use App\Models\Location;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Rider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'surname',
        'id_number',
        'birthday',
        'phone',
        'school',
        'avatar',
    ];

    /**
     * Get the user associated with this rider profiles.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user_bookings associated with this rider.
     */
    public function user_bookings()
    {
        return $this->hasMany(UserBooking::class);
    }

    /**
     * Get the location associated with this rider.
     */
    public function schoolLocation()
    {
        return $this->belongsTo(Location::class, 'school');
    }
}
