<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $productRepository
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
                'sale_price' => $dto->sale_price,
                'stock' => $dto->stock,
                'sku' => $dto->sku,
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

            if (!empty($dto->variants)) {
                $this->productRepository->createVariants($product, $dto->variants);
            }

            if (!empty($dto->seo_meta)) {
                $seoData = [
                    'meta_title' => $dto->seo_meta['meta_title'] ?? $dto->name,
                    'meta_description' => $dto->seo_meta['meta_description'] ?? $dto->short_description,
                    'meta_keywords' => $dto->seo_meta['meta_keywords'] ?? null,
                    'canonical_url' => $dto->seo_meta['canonical_url'] ?? url('/product/' . $dto->slug),
                ];
                $this->productRepository->createSeoMeta($product, $seoData);
            } else {
                $this->productRepository->createSeoMeta($product, [
                    'meta_title' => $dto->name,
                    'meta_description' => $dto->short_description,
                    'canonical_url' => url('/product/' . $dto->slug),
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
        DB::beginTransaction();
        try {
            $product = $this->productRepository->findById($id);
            
            $productData = [
                'name' => $dto->name,
                'slug' => $dto->slug,
                'short_description' => $dto->short_description,
                'description' => $dto->description,
                'video_url' => $dto->video_url,
                'perfect_placement' => $dto->perfect_placement,
                'price' => $dto->price,
                'sale_price' => $dto->sale_price,
                'stock' => $dto->stock,
                'sku' => $dto->sku,
                'status' => $dto->status,
                'featured' => $dto->featured,
                'is_trending' => $dto->is_trending,
            ];

            if ($dto->size_weight_image) {
                // Remove old size_weight image if exists
                if ($product->size_weight_image) {
                    Storage::disk('public')->delete($product->size_weight_image);
                }
                $productData['size_weight_image'] = $this->uploadSimpleImage($dto->size_weight_image);
            }

            $this->productRepository->update($id, $productData);
            $product = $this->productRepository->findById($id);

            // Handle Media Updates
            if ($dto->main_image) {
                // Remove old primary image
                $oldPrimary = $product->images()->where('is_primary', true)->first();
                if ($oldPrimary) {
                    Storage::disk('public')->delete($oldPrimary->image_path);
                    $oldPrimary->delete();
                }
                $this->uploadImage($product, $dto->main_image, true);
            }

            foreach ($dto->gallery_images as $image) {
                $this->uploadImage($product, $image, false);
            }

            $this->productRepository->syncRelations($product, 'collections', $dto->collection_ids);
            $this->productRepository->syncRelations($product, 'tags', $dto->tag_ids);

            if (!empty($dto->variants)) {
                $this->productRepository->createVariants($product, $dto->variants);
            }

            if (!empty($dto->seo_meta)) {
                $this->productRepository->updateSeoMeta($product, $dto->seo_meta);
            }

            DB::commit();
            return $product;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            throw $e;
        }
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
        $filename = time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
        $path = 'products/' . $filename;

        // Resize and optimize using Intervention (simple resize for example)
        $img = Image::decode($imageFile);
        ;

        Storage::disk('public')->put(
            $path,
            (string) $img->encodeUsingFileExtension($imageFile->getClientOriginalExtension(), quality: 80)
        );

        $product->images()->create([
            'image_path' => $path,
            'is_primary' => $isPrimary
        ]);
    }

    public function toggleStatus(int $id)
    {
        $product = $this->productRepository->findById($id);
        if ($product) {
            $this->productRepository->update($id, ['status' => !$product->status]);
            return true;
        }
        return false;
    }
}
