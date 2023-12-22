<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'ec_name',
        'ec_surname',
        'ec_id_number',
        'ec_contact_number',
        'ec_relationship',
    ];

    /**
     * Get the user associated with this rider profiles.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
