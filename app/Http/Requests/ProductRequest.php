<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Handle proper auth in middleware
    }

    public function rules(): array
    {
        $productId = $this->route('product'); // assuming the route parameter is 'product'
        
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:products,slug,' . $productId],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'perfect_placement' => ['nullable', 'string'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'length' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'height' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku,' . $productId],
            'hsn_code' => ['nullable', 'string', 'max:20'],
            'status' => ['boolean'],
            'featured' => ['boolean'],
            'is_trending' => ['boolean'],
            'collection_ids' => ['nullable', 'array'],
            'collection_ids.*' => ['exists:collections,id'],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['exists:tags,id'],
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.size' => ['nullable', 'string', 'max:80'],
            'variants.*.color' => ['nullable', 'string', 'max:80'],
            'variants.*.items_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'variants.*.linked_skus' => ['nullable', 'array'],
            'variants.*.linked_skus.*' => ['nullable', 'string', 'max:255', 'exists:products,sku'],
            'seo_meta' => ['nullable', 'array'],
            'seo_meta.meta_title' => ['nullable', 'string', 'max:255'],
            'seo_meta.meta_description' => ['nullable', 'string'],
            'seo_meta.meta_keywords' => ['nullable', 'string'],
            'seo_meta.canonical_url' => ['nullable', 'url'],
            'seo_meta.meta_robots' => ['nullable', 'string', 'max:50'],
            'seo_meta.og_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'main_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'size_weight_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $seen = [];
                $skus = [];

                foreach ((array) $this->input('variants', []) as $index => $variant) {
                    $size = trim((string) ($variant['size'] ?? ''));
                    $color = trim((string) ($variant['color'] ?? ''));
                    $itemsCount = (int) ($variant['items_count'] ?? 0);
                    $linkedSkus = array_values(array_unique(array_filter(array_map('trim', (array) ($variant['linked_skus'] ?? [])))));

                    if ($size === '' && $color === '' && empty($linkedSkus)) {
                        continue;
                    }

                    if ($size === '' && $color === '') {
                        $validator->errors()->add("variants.{$index}.size", 'Each variation needs a size or color.');
                        continue;
                    }

                    if ($itemsCount < 1) {
                        $validator->errors()->add("variants.{$index}.items_count", 'Enter how many items this option contains.');
                    }

                    if (empty($linkedSkus)) {
                        $validator->errors()->add("variants.{$index}.linked_skus", 'Link at least one product SKU.');
                    }

                    if (! empty($variant['id']) && ! $this->variantBelongsToCurrentProduct((int) $variant['id'])) {
                        $validator->errors()->add("variants.{$index}.id", 'Invalid product variation.');
                    }

                    $key = mb_strtolower($size . '|' . $color);
                    if (isset($seen[$key])) {
                        $validator->errors()->add("variants.{$index}.size", 'Duplicate variation size/color combinations are not allowed.');
                    }

                    $seen[$key] = true;

                    foreach ($linkedSkus as $linkedSku) {
                        $skuKey = mb_strtolower($linkedSku);
                        if (isset($skus[$skuKey])) {
                            $validator->errors()->add("variants.{$index}.linked_skus", 'A linked SKU can only be used once in this product.');
                        }
                        $skus[$skuKey] = true;

                        if ($this->productSkuBelongsToCurrentProduct($linkedSku)) {
                            $validator->errors()->add("variants.{$index}.linked_skus", 'A product cannot be linked to itself.');
                        }
                    }
                }
            },
        ];
    }

    protected function variantBelongsToCurrentProduct(int $variantId): bool
    {
        $product = $this->route('product');
        $productId = is_object($product) ? (int) $product->getKey() : (int) $product;

        return \App\Models\ProductVariant::whereKey($variantId)
            ->where('product_id', $productId)
            ->exists();
    }

    protected function productSkuBelongsToCurrentProduct(string $sku): bool
    {
        $product = $this->route('product');
        $productId = is_object($product) ? (int) $product->getKey() : (int) $product;

        return \App\Models\Product::whereKey($productId)->where('sku', $sku)->exists();
    }
}
