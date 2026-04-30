<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
}
