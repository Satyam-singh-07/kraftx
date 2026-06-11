<?php

namespace App\Services;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Laravel\Facades\Image;

class BannerImageOptimizer
{
    public const DESKTOP_WIDTH = 1920;
    public const MOBILE_WIDTH = 900;
    public const QUALITY = 80;

    public function storeDesktopUpload(Banner $banner, UploadedFile $file): array
    {
        $image = Image::decode($file);

        $this->storeDesktop($image, $banner);
        $this->storeMobile($image, $banner);
        $this->deleteTemporaryUpload($file);

        return [
            'image' => $this->desktopPath($banner),
            'mobile_image' => $this->mobilePath($banner),
        ];
    }

    public function storeMobileUpload(Banner $banner, UploadedFile $file): string
    {
        $this->storeMobile(Image::decode($file), $banner);
        $this->deleteTemporaryUpload($file);

        return $this->mobilePath($banner);
    }

    public function optimizeExisting(Banner $banner): bool
    {
        $source = $this->sourcePath($banner->image);

        if (! $source || ! is_file($source)) {
            return false;
        }

        $image = Image::decode($source);
        $this->storeDesktop($image, $banner);

        $mobileSource = $this->sourcePath($banner->mobile_image);
        if ($mobileSource && is_file($mobileSource)) {
            $this->storeMobile(Image::decode($mobileSource), $banner);
        } else {
            $this->storeMobile($image, $banner);
        }

        $this->deleteOriginal($banner->image);
        $this->deleteOriginal($banner->mobile_image);

        $banner->update([
            'image' => $this->desktopPath($banner),
            'mobile_image' => $this->mobilePath($banner),
        ]);

        return true;
    }

    public function deleteVariants(Banner $banner): void
    {
        $this->deleteOriginal($banner->image);
        $this->deleteOriginal($banner->mobile_image);

        Storage::disk('public')->delete([
            $this->desktopPath($banner),
            $this->mobilePath($banner),
        ]);
    }

    public function deleteLegacyPath(?string $path): void
    {
        $this->deleteOriginal($path);
    }

    private function storeDesktop(ImageInterface $image, Banner $banner): void
    {
        Storage::disk('public')->put(
            $this->desktopPath($banner),
            (string) $this->resizeForWidth($image, self::DESKTOP_WIDTH)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );
    }

    private function storeMobile(ImageInterface $image, Banner $banner): void
    {
        Storage::disk('public')->put(
            $this->mobilePath($banner),
            (string) $this->resizeForWidth($image, self::MOBILE_WIDTH)
                ->encodeUsingFileExtension('webp', quality: self::QUALITY, strip: true)
        );
    }

    private function resizeForWidth(ImageInterface $image, int $width): ImageInterface
    {
        return (clone $image)->scaleDown(width: $width);
    }

    private function desktopPath(Banner $banner): string
    {
        return "banners/{$banner->id}/desktop.webp";
    }

    private function mobilePath(Banner $banner): string
    {
        return "banners/{$banner->id}/mobile.webp";
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

        if (! in_array(basename($path), ['desktop.webp', 'mobile.webp'], true)) {
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
