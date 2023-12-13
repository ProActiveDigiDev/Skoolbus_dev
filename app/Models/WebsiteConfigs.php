<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteConfigs extends Model
{
    use HasFactory;


    protected $fillable = [
        'var_name',
        'name',
        'type',
        'description',
        'var_value',
        'notes',
    ];

}