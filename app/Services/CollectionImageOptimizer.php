<?php

namespace App\Services;

use App\Models\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class CollectionImageOptimizer
{
    public const THUMB_SIZE = 420;
    public const QUALITY = 80;

    public function storeUpload(Collection $collection, UploadedFile $file): string
    {
        $this->storeThumb(Image::decode($file), $collection);
        $this->deleteTemporaryUpload($file);

        return $this->thumbPath($collection);
    }

    public function optimizeExisting(Collection $collection): bool
    {
        $source = $this->sourcePath($collection->image);

        if (! $source || ! is_file($source)) {
            return false;
        }

        $this->storeThumb(Image::decode($source), $collection);
        $this->deleteOriginal($collection->image);

        $collection->update(['image' => $this->thumbPath($collection)]);

        return true;
    }

    public function thumbPath(Collection $collection): string
    {
        return "collections/{$collection->id}/thumb.webp";
    }

    public function deleteImage(Collection $collection): void
    {
        $this->deleteOriginal($collection->image);
        Storage::disk('public')->delete($this->thumbPath($collection));
    }

    public function deleteLegacyPath(?string $path): void
    {
        $this->deleteOriginal($path);
    }

    private function storeThumb($image, Collection $collection): void
    {
        Storage::disk('public')->put(
            $this->thumbPath($collection),
            (string) $image->cover(self::THUMB_SIZE, self::THUMB_SIZE)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );
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

    private function deleteOriginal(?string $path): void
    {
        if (! $path || str_starts_with($path, 'assets/')) {
            return;
        }

        if (basename($path) !== 'thumb.webp') {
            Storage::disk('public')->delete($path);
        }
    }

    private function deleteTemporaryUpload(UploadedFile $file): void
    {
        $path = $file->getRealPath();

        if ($path && is_file($path)) {
            @unlink($path);
        }
    }
}
