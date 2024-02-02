<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'has_medical_aid',
        'medical_aid_name',
        'medical_aid_plan',
        'medical_aid_number',
        'medical_aid_main_member_name',
        'medical_aid_main_member_number',
    ];

    protected $casts = [
        'has_medical_aid' => 'boolean',
        'medical_aid_dependants' => 'array',
    ];

    /**
     * Get the user associated with this rider profiles.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
