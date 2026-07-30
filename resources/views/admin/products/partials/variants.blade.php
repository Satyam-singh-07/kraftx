@php
    $variantRows = old('variants');
    if ($variantRows === null && isset($product)) {
        $variantRows = $product->variants
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'sku' => $variant->sku,
                'image_paths' => $variant->image_paths ?? [],
            ])
            ->values()
            ->all();
    }
    $variantRows = $variantRows ?: [];
@endphp

<x-admin.card title="Product Variations">
    <div class="mt-4 space-y-4">
        <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-200">
            Variations are optional. Add rows only for extra sellable options; the main product remains available as Original and uses the product stock above.
        </div>

        <div id="variant-rows" class="space-y-3">
            @foreach($variantRows as $index => $variant)
                <div class="variant-row grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                    <input type="hidden" name="variants[{{ $index }}][existing_image_paths]" value="{{ json_encode($variant['image_paths'] ?? []) }}">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Size</label>
                        <input type="text" name="variants[{{ $index }}][size]" value="{{ $variant['size'] ?? '' }}" placeholder="Small"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Color</label>
                        <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant['color'] ?? '' }}" placeholder="Walnut"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price</label>
                        <input type="number" step="0.01" min="0" name="variants[{{ $index }}][price]" value="{{ $variant['price'] ?? '' }}" placeholder="Optional"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock</label>
                        <input type="number" min="0" name="variants[{{ $index }}][stock]" value="{{ $variant['stock'] ?? 0 }}"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">SKU</label>
                        <input type="text" name="variants[{{ $index }}][sku]" value="{{ $variant['sku'] ?? '' }}" placeholder="Optional"
                            class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Photos</label>
                        <input type="file" name="variants[{{ $index }}][images][]" accept=".jpg,.jpeg,.png,.webp,image/*" multiple
                            class="variant-image-input block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @if(!empty($variant['image_paths']))
                            <div class="variant-image-preview mt-2 flex flex-wrap gap-2">
                                @foreach($variant['image_paths'] as $imagePath)
                                    <img src="{{ \App\Models\ProductImage::urlForVariant($imagePath, 'thumb') }}" alt="Variant image" class="h-14 w-14 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                @endforeach
                            </div>
                        @else
                            <div class="variant-image-preview mt-2 flex flex-wrap gap-2"></div>
                        @endif
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" class="remove-variant-row w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold">Remove</button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-variant-row" class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800 text-sm font-semibold">
            Add Variation
        </button>
    </div>
</x-admin.card>

<template id="variant-row-template">
    <div class="variant-row grid grid-cols-1 md:grid-cols-12 gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="hidden" data-name="id">
        <input type="hidden" data-name="existing_image_paths">
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Size</label>
            <input type="text" data-name="size" placeholder="Small"
                class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Color</label>
            <input type="text" data-name="color" placeholder="Walnut"
                class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price</label>
            <input type="number" step="0.01" min="0" data-name="price" placeholder="Optional"
                class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
        </div>
        <div class="md:col-span-1">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock</label>
            <input type="number" min="0" data-name="stock" value="0"
                class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">SKU</label>
            <input type="text" data-name="sku" placeholder="Optional"
                class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Photos</label>
            <input type="file" data-name="images" accept=".jpg,.jpeg,.png,.webp,image/*" multiple
                class="variant-image-input block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            <div class="variant-image-preview mt-2 flex flex-wrap gap-2"></div>
        </div>
        <div class="md:col-span-1 flex items-end">
            <button type="button" class="remove-variant-row w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 text-sm font-semibold">Remove</button>
        </div>
    </div>
</template>
