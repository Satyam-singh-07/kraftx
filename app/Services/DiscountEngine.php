<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Deal;
use Illuminate\Support\Facades\Cache;

class DiscountEngine
{
    public function getBestDealForProduct(Product $product): ?Deal
    {
        $activeDeals = Cache::remember('active_deals', 3600, function () {
            return Deal::with(['products'])
                ->where('status', true)
                ->get()
                ->filter->isValid();
        });

        $applicableDeals = collect();

        foreach ($activeDeals as $deal) {
            if ($deal->products->contains($product->id)) {
                $applicableDeals->push($deal);
                continue;
            }
        }

        if ($applicableDeals->isEmpty()) {
            return null;
        }

        // Return the deal that gives the highest discount, tie break by priority
        return $applicableDeals->sortByDesc(function ($deal) use ($product) {
            return [$this->calculateDiscountAmount($product->price, $deal), $deal->priority];
        })->first();
    }

    public function calculateDiscountAmount(float $price, Deal $deal): float
    {
        if ($deal->discount_type === 'percentage') {
            return ($price * $deal->discount_value) / 100;
        }
        return min($price, $deal->discount_value);
    }
    
    public function getDiscountedPrice(Product $product): float
    {
        $bestDeal = $this->getBestDealForProduct($product);
        if (!$bestDeal) {
            return $product->sale_price ?? $product->price;
        }
        
        $basePrice = $product->price;
        $discountAmount = $this->calculateDiscountAmount($basePrice, $bestDeal);
        return max(0, $basePrice - $discountAmount);
    }
}
