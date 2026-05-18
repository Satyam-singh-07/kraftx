<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_READY_TO_SHIP = 'ready_to_ship';
    public const STATUS_SHIPMENT_CREATING = 'shipment_creating';
    public const STATUS_SHIPMENT_CREATED = 'shipment_created';
    public const STATUS_LABEL_GENERATED = 'label_generated';
    public const STATUS_PICKUP_PENDING = 'pickup_pending';
    public const STATUS_PICKED_UP = 'picked_up';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_IN_TRANSIT = 'in_transit';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_RTO = 'rto';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_FAILED = 'failed';

    public const ACTIVE_STATUSES = [
        self::STATUS_READY_TO_SHIP,
        self::STATUS_SHIPMENT_CREATING,
        self::STATUS_SHIPMENT_CREATED,
        self::STATUS_LABEL_GENERATED,
        self::STATUS_PICKUP_PENDING,
        self::STATUS_PICKED_UP,
        self::STATUS_SHIPPED,
        self::STATUS_IN_TRANSIT,
        self::STATUS_DELIVERED,
        self::STATUS_RTO,
    ];

    protected $fillable = [
        'order_id',
        'provider',
        'provider_shipment_id',
        'awb',
        'shipment_status',
        'shipment_status_code',
        'tracking_url',
        'payment_mode',
        'cod_amount',
        'invoice_value',
        'pickup_location_name',
        'serviceability_status',
        'label_path',
        'label_generated_at',
        'shipped_at',
        'delivered_at',
        'cancelled_at',
        'created_by',
        'metadata',
    ];

    protected $casts = [
        'cod_amount' => 'decimal:2',
        'invoice_value' => 'decimal:2',
        'label_generated_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ShipmentPackage::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(ShipmentEvent::class);
    }

    public function apiLogs(): HasMany
    {
        return $this->hasMany(ShipmentApiLog::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ShipmentAttempt::class);
    }

    public function pickupRequests(): BelongsToMany
    {
        return $this->belongsToMany(PickupRequest::class)->withTimestamps();
    }
}
