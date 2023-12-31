<?php

namespace App\Models;

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
}
