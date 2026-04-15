<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'deal_id',
        'rule_type',
        'rule_value',
    ];

    protected $casts = [
        'rule_value' => 'array',
    ];

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
