<?php

namespace App\Console\Commands;

use App\Models\ProductImage;
use App\Services\ProductImageOptimizer;
use Illuminate\Console\Command;

class OptimizeProductImages extends Command
{
    protected $signature = 'images:optimize-products';

    protected $description = 'Convert existing product images to WebP derivatives and update product image records.';

    public function handle(ProductImageOptimizer $optimizer): int
    {
        $processed = 0;
        $skipped = 0;

        ProductImage::query()
            ->with('product')
            ->orderBy('id')
            ->chunkById(100, function ($images) use ($optimizer, &$processed, &$skipped) {
                foreach ($images as $image) {
                    if ($optimizer->optimizeExisting($image)) {
                        $processed++;
                        continue;
                    }

                    $skipped++;
                }
            });

        $this->info("Optimized {$processed} product image(s).");
        $this->info("Skipped {$skipped} product image(s).");

        return self::SUCCESS;
    }
}
