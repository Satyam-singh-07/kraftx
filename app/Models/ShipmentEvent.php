<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'provider',
        'event_type',
        'raw_status',
        'normalized_status',
        'location',
        'event_time',
        'payload_hash',
        'raw_payload',
        'is_duplicate',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'raw_payload' => 'array',
        'is_duplicate' => 'boolean',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
