<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'provider',
        'endpoint',
        'request_type',
        'response_code',
        'latency_ms',
        'success',
        'request_summary',
        'response_summary',
        'retry_count',
    ];

    protected $casts = [
        'success' => 'boolean',
        'request_summary' => 'array',
        'response_summary' => 'array',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
