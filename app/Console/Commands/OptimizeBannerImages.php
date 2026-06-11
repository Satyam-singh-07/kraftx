<?php

namespace App\Console\Commands;

use App\Models\Banner;
use App\Services\BannerImageOptimizer;
use Illuminate\Console\Command;

class OptimizeBannerImages extends Command
{
    protected $signature = 'images:optimize-banners';

    protected $description = 'Convert existing banner images to desktop and mobile WebP variants.';

    public function handle(BannerImageOptimizer $optimizer): int
    {
        $optimized = 0;
        $skipped = 0;

        Banner::query()
            ->orderBy('id')
            ->chunkById(100, function ($banners) use ($optimizer, &$optimized, &$skipped) {
                foreach ($banners as $banner) {
                    if ($optimizer->optimizeExisting($banner)) {
                        $optimized++;
                        continue;
                    }

                    $skipped++;
                }
            });

        $this->info("Optimized {$optimized} banner image(s).");
        $this->info("Skipped {$skipped} banner image(s).");

        return self::SUCCESS;
    }
}
