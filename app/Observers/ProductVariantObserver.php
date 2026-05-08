<?php

namespace App\Observers;

use App\Models\ProductVariant;
use App\Services\ShiprocketService;

class ProductVariantObserver
{
    public function __construct(
        protected ShiprocketService $shiprocketService
    ) {
    }

    public function saved(ProductVariant $variant): void
    {
        $this->syncParentProduct($variant);
    }

    public function deleted(ProductVariant $variant): void
    {
        $this->syncParentProduct($variant);
    }

    protected function syncParentProduct(ProductVariant $variant): void
    {
        $product = $variant->product;

        if ($product) {
            $this->shiprocketService->syncProduct($product);
        }
    }
}
