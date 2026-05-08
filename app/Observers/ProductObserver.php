<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\ShiprocketService;

class ProductObserver
{
    protected $shiprocketService;

    public function __construct(ShiprocketService $shiprocketService)
    {
        $this->shiprocketService = $shiprocketService;
    }

    public function saved(Product $product)
    {
        $this->shiprocketService->syncProduct($product);
    }

    public function deleted(Product $product)
    {
        // Optionally handle deletion if Shiprocket supports it
    }
}
