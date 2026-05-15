<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'platform_order_id',
        'cart_id',
        'order_number',
        'total_amount',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'status',
        'checkout_status',
        'source',
        'shipping_plan',
        'rto_prediction',
        'estimated_delivery_date',
        'payment_method',
        'payment_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'shipping_country',
        'shipping_address_data',
        'billing_address_data',
        'payments',
        'coupon_codes',
        'discount_detail',
        'awb_code',
        'courier_name',
        'shipment_status',
        'shipment_status_id',
        'shipment_status_updated_at',
        'shipment_track_url',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'shipping_address_data' => 'array',
        'billing_address_data' => 'array',
        'payments' => 'array',
        'coupon_codes' => 'array',
        'discount_detail' => 'array',
        'estimated_delivery_date' => 'date',
        'shipment_status_updated_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
