<?php

namespace App\Observers;

use App\Models\ProductImage;
use App\Services\ShiprocketService;

class ProductImageObserver
{
    public function __construct(
        protected ShiprocketService $shiprocketService
    ) {
    }

    public function saved(ProductImage $image): void
    {
        $this->syncParentProduct($image);
    }

    public function deleted(ProductImage $image): void
    {
        $this->syncParentProduct($image);
    }

    protected function syncParentProduct(ProductImage $image): void
    {
        $product = $image->product;

        if ($product) {
            $this->shiprocketService->syncProduct($product);
        }
    }
}
