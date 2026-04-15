<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_cart_value',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
    ];

    public function isValid(float $cartTotal = 0): bool
    {
        if (!$this->status) return false;
        
        $now = now();
        if ($this->start_date && $this->start_date > $now) return false;
        if ($this->end_date && $this->end_date < $now) return false;
        
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        if ($cartTotal > 0 && $this->min_cart_value > 0 && $cartTotal < $this->min_cart_value) return false;

        return true;
    }
    
    public function calculateDiscount(float $cartTotal): float
    {
        if (!$this->isValid($cartTotal)) return 0;
        
        $discount = 0;
        if ($this->discount_type === 'percentage') {
            $discount = ($cartTotal * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }
        
        if ($this->max_discount > 0 && $discount > $this->max_discount) {
            $discount = $this->max_discount;
        }
        
        return min($discount, $cartTotal); // Discount cannot exceed cart total
    }
}
