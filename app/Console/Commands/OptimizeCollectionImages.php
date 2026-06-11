<?php

namespace App\Console\Commands;

use App\Models\Collection;
use App\Services\CollectionImageOptimizer;
use Illuminate\Console\Command;

class OptimizeCollectionImages extends Command
{
    protected $signature = 'images:optimize-collections';

    protected $description = 'Convert existing collection images to 420px WebP thumbnails.';

    public function handle(CollectionImageOptimizer $optimizer): int
    {
        $optimized = 0;
        $skipped = 0;

        Collection::query()
            ->whereNotNull('image')
            ->orderBy('id')
            ->chunkById(100, function ($collections) use ($optimizer, &$optimized, &$skipped) {
                foreach ($collections as $collection) {
                    if ($optimizer->optimizeExisting($collection)) {
                        $optimized++;
                        continue;
                    }

                    $skipped++;
                }
            });

        $this->info("Optimized {$optimized} collection image(s).");
        $this->info("Skipped {$skipped} collection image(s).");

        return self::SUCCESS;
    }
}
