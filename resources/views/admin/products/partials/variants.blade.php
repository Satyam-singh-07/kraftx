@php
    $variantRows = old('variants');
    if ($variantRows === null && isset($product)) {
        $variantRows = $product->variants
            ->filter(fn ($variant) => ! empty($variant->linked_skus))
            ->map(fn ($variant) => [
                'id' => $variant->id,
                'size' => $variant->size,
                'color' => $variant->color,
                'items_count' => $variant->items_count ?? 1,
                'linked_skus' => $variant->linked_skus ?? [],
            ])
            ->values()
            ->all();
    }
    $variantRows = $variantRows ?: [];
@endphp

<x-admin.card title="Linked Product Variations">
    <div class="mt-4 space-y-4">
        <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:border-blue-900/50 dark:bg-blue-900/20 dark:text-blue-200">
            Link other products by SKU. Their price, stock, and product images will be used for this option, and the reverse link will be created automatically.
        </div>

        <div id="variant-rows" class="space-y-3">
            @foreach($variantRows as $index => $variant)
                @php($linkedSkus = array_values(array_filter((array) ($variant['linked_skus'] ?? []))))
                <div class="variant-row rounded-xl border border-gray-200 p-4 dark:border-gray-700" data-variant-index="{{ $index }}">
                    <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant['id'] ?? '' }}">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Color</label>
                            <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant['color'] ?? '' }}" placeholder="Black"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Size</label>
                            <input type="text" name="variants[{{ $index }}][size]" value="{{ $variant['size'] ?? '' }}" placeholder="Medium"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-3">
                            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Number Of Items</label>
                            <input type="number" min="1" name="variants[{{ $index }}][items_count]" value="{{ $variant['items_count'] ?? 1 }}"
                                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="flex items-end md:col-span-3">
                            <button type="button" class="remove-variant-row w-full rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-100">Remove Variation</button>
                        </div>
                    </div>

                    <div class="relative mt-4">
                        <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Linked Product SKUs</label>
                        <input type="search" data-role="variant-sku-search" autocomplete="off" placeholder="Search by product name or SKU..."
                            class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <div data-role="variant-sku-results" class="absolute inset-x-0 top-full z-20 mt-1 hidden max-h-56 overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"></div>
                        <div data-role="variant-sku-chips" class="mt-2 flex flex-wrap gap-2">
                            @foreach($linkedSkus as $sku)
                                <span data-sku-chip="{{ $sku }}" class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">
                                    {{ $sku }}
                                    <button type="button" data-role="remove-sku" class="text-blue-100 hover:text-white" aria-label="Remove {{ $sku }}">&times;</button>
                                    <input type="hidden" name="variants[{{ $index }}][linked_skus][]" value="{{ $sku }}">
                                </span>
                            @endforeach
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Search and select one or more products. Each linked product gets the reverse link automatically.</p>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-variant-row" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">
            Add Linked Variation
        </button>
    </div>
</x-admin.card>

<template id="variant-row-template">
    <div class="variant-row rounded-xl border border-gray-200 p-4 dark:border-gray-700">
        <input type="hidden" data-name="id">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-3">
                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Color</label>
                <input type="text" data-name="color" placeholder="Black"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="md:col-span-3">
                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Size</label>
                <input type="text" data-name="size" placeholder="Medium"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="md:col-span-3">
                <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Number Of Items</label>
                <input type="number" min="1" value="1" data-name="items_count"
                    class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            </div>
            <div class="flex items-end md:col-span-3">
                <button type="button" class="remove-variant-row w-full rounded-lg bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-100">Remove Variation</button>
            </div>
        </div>
        <div class="relative mt-4">
            <label class="mb-1 block text-xs font-bold uppercase text-gray-500">Linked Product SKUs</label>
            <input type="search" data-role="variant-sku-search" autocomplete="off" placeholder="Search by product name or SKU..."
                class="block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <div data-role="variant-sku-results" class="absolute inset-x-0 top-full z-20 mt-1 hidden max-h-56 overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"></div>
            <div data-role="variant-sku-chips" class="mt-2 flex flex-wrap gap-2"></div>
            <p class="mt-1 text-xs text-gray-500">Search and select one or more products.</p>
        </div>
    </div>
</template>
