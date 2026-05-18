<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PickupRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'pickup_request_id',
        'pickup_location_name',
        'scheduled_date',
        'status',
        'shipment_count',
        'metadata',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'metadata' => 'array',
    ];

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(Shipment::class)->withTimestamps();
    }
}
