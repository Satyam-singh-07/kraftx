<?php

namespace App\Services;

use App\Models\BlogPost;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class BlogImageOptimizer
{
    public const THUMB_WIDTH = 450;
    public const THUMB_HEIGHT = 281;
    public const MEDIUM_WIDTH = 900;
    public const MEDIUM_HEIGHT = 563;
    public const HERO_WIDTH = 1400;
    public const QUALITY = 80;

    public function storeUpload(BlogPost $post, UploadedFile $file): string
    {
        $this->storeVariants(Image::decode($file), $post);
        $this->deleteTemporaryUpload($file);

        return $this->heroPath($post);
    }

    public function optimizeExisting(BlogPost $post): bool
    {
        $source = $this->sourcePath($post->featured_image);

        if (! $source || ! is_file($source)) {
            return false;
        }

        $this->storeVariants(Image::decode($source), $post);
        $this->deleteLegacyPath($post->featured_image);

        $heroPath = $this->heroPath($post);
        if ($post->featured_image !== $heroPath) {
            $post->update(['featured_image' => $heroPath]);
        }

        return true;
    }

    public function deleteVariants(BlogPost $post): void
    {
        $this->deleteLegacyPath($post->featured_image);

        Storage::disk('public')->delete([
            $this->thumbPath($post),
            $this->mediumPath($post),
            $this->heroPath($post),
        ]);
    }

    public function deleteLegacyPath(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        if (! in_array(basename($path), ['thumb.webp', 'medium.webp', 'hero.webp'], true)) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storeVariants(ImageInterface $image, BlogPost $post): void
    {
        $disk = Storage::disk('public');
        $directory = $this->directory($post);

        $disk->makeDirectory($directory);

        $disk->put(
            $this->thumbPath($post),
            (string) (clone $image)
                ->cover(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );

        $disk->put(
            $this->mediumPath($post),
            (string) (clone $image)
                ->cover(self::MEDIUM_WIDTH, self::MEDIUM_HEIGHT)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );

        $disk->put(
            $this->heroPath($post),
            (string) $this->resizeForWidth($image, self::HERO_WIDTH)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );
    }

    private function resizeForWidth(ImageInterface $image, int $width): ImageInterface
    {
        return (clone $image)->scaleDown(width: $width);
    }

    private function thumbPath(BlogPost $post): string
    {
        return $this->directory($post) . '/thumb.webp';
    }

    private function mediumPath(BlogPost $post): string
    {
        return $this->directory($post) . '/medium.webp';
    }

    private function heroPath(BlogPost $post): string
    {
        return $this->directory($post) . '/hero.webp';
    }

    private function directory(BlogPost $post): string
    {
        return "blog/posts/{$post->id}";
    }

    private function sourcePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'assets/')) {
            return public_path($path);
        }

        return Storage::disk('public')->path($path);
    }

    private function deleteTemporaryUpload(UploadedFile $file): void
    {
        $path = $file->getRealPath();

        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}
