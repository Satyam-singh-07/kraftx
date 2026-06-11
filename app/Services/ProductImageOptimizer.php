<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class ProductImageOptimizer
{
    public const THUMB_WIDTH = 400;
    public const MEDIUM_WIDTH = 900;
    public const ZOOM_WIDTH = 1600;
    public const QUALITY = 82;

    public function storeUpload(Product $product, UploadedFile $file, bool $isPrimary): string
    {
        $directory = $isPrimary
            ? "products/{$product->id}"
            : "products/{$product->id}/gallery/" . str_replace('.', '', uniqid('', true));

        $this->storeVariants(Image::decode($file), $directory);
        $this->deleteTemporaryUpload($file);

        return "{$directory}/zoom.webp";
    }

    public function optimizeExisting(ProductImage $productImage): bool
    {
        $sourcePath = $productImage->image_path;

        if (! $sourcePath) {
            return false;
        }

        $source = str_starts_with($sourcePath, 'assets/')
            ? public_path($sourcePath)
            : Storage::disk('public')->path($sourcePath);

        if (! is_file($source)) {
            return false;
        }

        $directory = $this->targetDirectory($productImage);
        $this->storeVariants(Image::decode($source), $directory);

        $zoomPath = "{$directory}/zoom.webp";
        if ($sourcePath !== $zoomPath && ! str_starts_with($sourcePath, 'assets/')) {
            Storage::disk('public')->delete($sourcePath);
        }

        if ($sourcePath !== $zoomPath) {
            $productImage->update(['image_path' => $zoomPath]);
        }

        return true;
    }

    public function deleteVariants(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        $directory = dirname($path);

        Storage::disk('public')->delete([
            "{$directory}/thumb.webp",
            "{$directory}/medium.webp",
            "{$directory}/zoom.webp",
            $path,
        ]);
    }

    private function storeVariants(ImageInterface $image, string $directory): void
    {
        Storage::disk('public')->put(
            "{$directory}/thumb.webp",
            (string) $this->resizeForWidth($image, self::THUMB_WIDTH)->encodeUsingFileExtension('webp', quality: self::QUALITY)
        );

        Storage::disk('public')->put(
            "{$directory}/medium.webp",
            (string) $this->resizeForWidth($image, self::MEDIUM_WIDTH)->encodeUsingFileExtension('webp', quality: self::QUALITY)
        );

        Storage::disk('public')->put(
            "{$directory}/zoom.webp",
            (string) $this->resizeForWidth($image, self::ZOOM_WIDTH)->encodeUsingFileExtension('webp', quality: self::QUALITY)
        );
    }

    private function resizeForWidth(ImageInterface $image, int $width): ImageInterface
    {
        return (clone $image)->scaleDown(width: $width);
    }

    private function targetDirectory(ProductImage $productImage): string
    {
        if ($productImage->is_primary) {
            return "products/{$productImage->product_id}";
        }

        $directory = dirname($productImage->image_path);

        if ($directory && $directory !== '.' && str_contains($directory, "/gallery/")) {
            return $directory;
        }

        return "products/{$productImage->product_id}/gallery/{$productImage->id}";
    }

    private function deleteTemporaryUpload(UploadedFile $file): void
    {
        $path = $file->getRealPath();

        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}
