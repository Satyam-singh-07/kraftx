<?php

namespace App\Console\Commands;

use App\Models\BlogPost;
use App\Services\BlogImageOptimizer;
use Illuminate\Console\Command;

class OptimizeBlogImages extends Command
{
    protected $signature = 'images:optimize-blogs';

    protected $description = 'Convert existing blog featured images to WebP thumbnail, medium, and hero variants.';

    public function handle(BlogImageOptimizer $optimizer): int
    {
        $optimized = 0;
        $skipped = 0;

        BlogPost::query()
            ->whereNotNull('featured_image')
            ->orderBy('id')
            ->chunkById(100, function ($posts) use ($optimizer, &$optimized, &$skipped) {
                foreach ($posts as $post) {
                    if ($optimizer->optimizeExisting($post)) {
                        $optimized++;
                        continue;
                    }

                    $skipped++;
                }
            });

        $this->info("Optimized {$optimized} blog image(s).");
        $this->info("Skipped {$skipped} blog image(s).");

        return self::SUCCESS;
    }
}
