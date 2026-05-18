<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'package_number',
        'weight_kg',
        'length_cm',
        'width_cm',
        'height_cm',
        'volumetric_weight_kg',
        'awb',
        'metadata',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:3',
        'length_cm' => 'decimal:2',
        'width_cm' => 'decimal:2',
        'height_cm' => 'decimal:2',
        'volumetric_weight_kg' => 'decimal:3',
        'metadata' => 'array',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
