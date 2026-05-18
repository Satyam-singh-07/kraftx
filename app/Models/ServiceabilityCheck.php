<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceabilityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'pincode',
        'provider',
        'is_serviceable',
        'cod_available',
        'prepaid_available',
        'estimated_days',
        'response_snapshot',
        'checked_at',
    ];

    protected $casts = [
        'is_serviceable' => 'boolean',
        'cod_available' => 'boolean',
        'prepaid_available' => 'boolean',
        'response_snapshot' => 'array',
        'checked_at' => 'datetime',
    ];
}
