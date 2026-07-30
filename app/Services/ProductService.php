<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository,
        protected ProductDemandService $productDemandService,
        protected ProductImageOptimizer $productImageOptimizer
    ) {
    }

    public function createProduct(ProductDTO $dto)
    {
        DB::beginTransaction();
        try {
            // Check for duplicate slug
            if ($this->productRepository->findBySlug($dto->slug)) {
                throw new Exception("Product slug '{$dto->slug}' already exists.");
            }

            $productData = [
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'video_url' => $dto->video_url,
                'perfect_placement' => $dto->perfect_placement,
                'price' => $dto->price,
                'weight' => $dto->weight,
                'length' => $dto->length,
                'width' => $dto->width,
                'height' => $dto->height,
                'sale_price' => $dto->sale_price,
                'stock' => $dto->stock,
                'sku' => $dto->sku,
                'hsn_code' => $dto->hsn_code,
                'status' => $dto->status,
                'featured' => $dto->featured,
                'is_trending' => $dto->is_trending,
            ];

            if ($dto->size_weight_image) {
                $productData['size_weight_image'] = $this->uploadSimpleImage($dto->size_weight_image);
            }

            $product = $this->productRepository->create($productData);

            // Handle Images
            if ($dto->main_image) {
                $this->uploadImage($product, $dto->main_image, true);
            }

            foreach ($dto->gallery_images as $image) {
                $this->uploadImage($product, $image, false);
            }

            if (!empty($dto->collection_ids)) {
                $this->productRepository->syncRelations($product, 'collections', $dto->collection_ids);
            }

            if (!empty($dto->tag_ids)) {
                $this->productRepository->syncRelations($product, 'tags', $dto->tag_ids);
            }

            $filesToDelete = [];
            $this->productRepository->createVariants($product, $this->prepareVariants($product, $dto->variants, $filesToDelete));

            if (!empty($dto->seo_meta)) {
                $seoData = [
                    'meta_title' => $dto->seo_meta['meta_title'] ?? $dto->name,
                    'meta_description' => $dto->seo_meta['meta_description'] ?? $dto->short_description,
                    'meta_keywords' => $dto->seo_meta['meta_keywords'] ?? null,
                    'canonical_url' => $dto->seo_meta['canonical_url'] ?? url('/product/' . $dto->slug),
                    'meta_robots' => $dto->seo_meta['meta_robots'] ?? 'index,follow',
                ];
                if (!empty($dto->seo_meta['og_image'])) {
                    $seoData['og_image'] = $this->uploadSeoImage($dto->seo_meta['og_image']);
                }
                $this->productRepository->createSeoMeta($product, $seoData);
            } else {
                $this->productRepository->createSeoMeta($product, [
                    'meta_title' => $dto->name,
                    'meta_description' => $dto->short_description,
                    'canonical_url' => url('/product/' . $dto->slug),
                    'meta_robots' => 'index,follow',
                ]);
            }

            DB::commit();

            return $product;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateProduct(int $id, ProductDTO $dto)
    {
        Log::info('ProductService@updateProduct started for ID: ' . $id);
        $filesToDelete = [];
        DB::beginTransaction();
        try {
            $product = $this->productRepository->find($id, relations: ['images', 'collections', 'tags', 'variants', 'seoMeta']);
            if (!$product) {
                Log::error('Product not found in ProductService: ' . $id);
                throw new Exception('Product not found.');
            }
            $oldStock = (int) $product->stock;
            
            $productData = [
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'video_url' => $dto->video_url,
                'perfect_placement' => $dto->perfect_placement,
                'price' => $dto->price,
                'weight' => $dto->weight,
                'length' => $dto->length,
                'width' => $dto->width,
                'height' => $dto->height,
                'sale_price' => $dto->sale_price,
                'stock' => $dto->stock,
                'sku' => $dto->sku,
                'hsn_code' => $dto->hsn_code,
                'status' => $dto->status,
                'featured' => $dto->featured,
                'is_trending' => $dto->is_trending,
            ];

            if ($dto->size_weight_image) {
                Log::info('Updating size_weight_image');
                if ($product->size_weight_image) {
                    $filesToDelete[] = $product->size_weight_image;
                }
                $productData['size_weight_image'] = $this->uploadSimpleImage($dto->size_weight_image);
            }

            $this->productRepository->update($id, $productData);
            $product = $this->productRepository->find($id, relations: ['images', 'collections', 'tags', 'variants', 'seoMeta']);

            // Handle Media Updates
            if ($dto->main_image) {
                Log::info('Updating main_image');
                $oldPrimaries = $product->images()->where('is_primary', true)->get();
                foreach ($oldPrimaries as $oldPrimary) {
                    $filesToDelete[] = $oldPrimary->image_path;
                    $oldPrimary->delete();
                }
                $this->uploadImage($product, $dto->main_image, true);
            }

            if (!empty($dto->gallery_images)) {
                Log::info('Adding gallery images: ' . count($dto->gallery_images));
                foreach ($dto->gallery_images as $image) {
                    $this->uploadImage($product, $image, false);
                }
            }

            $this->productRepository->syncRelations($product, 'collections', $dto->collection_ids);
            $this->productRepository->syncRelations($product, 'tags', $dto->tag_ids);

            $this->productRepository->createVariants($product, $this->prepareVariants($product, $dto->variants, $filesToDelete));

            if (!empty($dto->seo_meta)) {
                $seoData = [
                    'meta_title' => $dto->seo_meta['meta_title'] ?? $dto->name,
                    'meta_description' => $dto->seo_meta['meta_description'] ?? $dto->short_description,
                    'meta_keywords' => $dto->seo_meta['meta_keywords'] ?? null,
                    'canonical_url' => $dto->seo_meta['canonical_url'] ?? url('/product/' . $dto->slug),
                    'meta_robots' => $dto->seo_meta['meta_robots'] ?? 'index,follow',
                ];

                if (!empty($dto->seo_meta['og_image'])) {
                    if ($product->seoMeta?->og_image) {
                        $filesToDelete[] = $product->seoMeta->og_image;
                    }
                    $seoData['og_image'] = $this->uploadSeoImage($dto->seo_meta['og_image']);
                }

                $this->productRepository->updateSeoMeta($product, $seoData);
            }

            DB::commit();
            Log::info('Product update transaction committed successfully for ID: ' . $id);

            $this->productDemandService->handleStockTransition($product->fresh(['images']), $oldStock, (int) $dto->stock);

            // Delete old files only AFTER successful commit
            foreach ($filesToDelete as $file) {
                if ($file && !str_starts_with($file, 'assets/')) {
                    $this->productImageOptimizer->deleteVariants($file);
                }
            }

            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product update failed in ProductService: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function prepareVariants($product, array $variants, array &$filesToDelete): array
    {
        $existingVariants = $product->variants()->get()->keyBy('id');
        $incomingIds = collect($variants)->pluck('id')->filter()->map(fn ($id) => (int) $id)->all();

        $existingVariants
            ->reject(fn ($variant) => in_array((int) $variant->id, $incomingIds, true))
            ->each(function ($variant) use (&$filesToDelete) {
                foreach ((array) $variant->image_paths as $path) {
                    if ($path) {
                        $filesToDelete[] = $path;
                    }
                }
            });

        return collect($variants)
            ->map(function (array $variant) use ($product, $existingVariants, &$filesToDelete) {
                $existing = ! empty($variant['id']) ? $existingVariants->get((int) $variant['id']) : null;
                $imagePaths = $existing?->image_paths ?? $variant['existing_image_paths'] ?? [];

                if (! empty($variant['images'])) {
                    foreach ((array) $imagePaths as $path) {
                        if ($path) {
                            $filesToDelete[] = $path;
                        }
                    }

                    $imagePaths = collect($variant['images'])
                        ->filter(fn ($image) => $image instanceof UploadedFile)
                        ->map(fn (UploadedFile $image) => $this->productImageOptimizer->storeVariantUpload($product, $image))
                        ->values()
                        ->all();
                }

                return [
                    'id' => $variant['id'] ?? null,
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                    'sku' => $variant['sku'],
                    'image_paths' => $imagePaths,
                ];
            })
            ->all();
    }

    protected function uploadSimpleImage($imageFile)
    {
        $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $path = 'products/details/' . $filename;

        $img = Image::decode($imageFile);
        Storage::disk('public')->put(
            $path,
            (string) $img->encodeUsingFileExtension($imageFile->getClientOriginalExtension(), quality: 80)
        );

        return $path;
    }

    protected function uploadImage($product, $imageFile, bool $isPrimary)
    {
        $path = $this->productImageOptimizer->storeUpload($product, $imageFile, $isPrimary);

        $product->images()->create([
            'image_path' => $path,
            'is_primary' => $isPrimary
        ]);
    }

    protected function uploadSeoImage($imageFile): string
    {
        $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $path = 'seo/' . $filename;
        $img = Image::decode($imageFile);

        Storage::disk('public')->put(
            $path,
            (string) $img->encodeUsingFileExtension($imageFile->getClientOriginalExtension(), quality: 82)
        );

        return $path;
    }

    public function toggleStatus(int $id)
    {
        $product = $this->productRepository->find($id);
        if ($product) {
            $this->productRepository->update($id, ['status' => !$product->status]);
            return true;
        }
        return false;
    }
}
