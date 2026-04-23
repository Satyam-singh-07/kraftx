<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Create Product</h2>
            <a href="{{ route('admin.products.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Back to List</a>
        </div>

        <!-- error print  -->

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6"
            enctype="multipart/form-data">
            @csrf

            <x-admin.card title="Basic Information">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Name
                            <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}"
                            placeholder="Enter product name"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug
                            (Optional)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="product-slug"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Short
                            Description</label>
                        <textarea name="short_description" rows="2" placeholder="Brief summary of the product..."
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('short_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Full
                            Description</label>
                        <textarea name="description" rows="5" placeholder="Detailed product specifications and info..."
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Video
                            URL</label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}"
                            placeholder="YouTube or Vimeo link"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Product Media">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Main Image
                            (Primary)</label>
                        <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                            <input type="file" name="main_image" class="hidden" x-ref="photo" x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   ">
                            <div class="mt-2" x-show="!photoPreview">
                                <div class="w-full h-40 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    x-on:click.prevent="$refs.photo.click()">
                                    <span class="text-gray-500 text-sm">Click to upload main image</span>
                                </div>
                            </div>
                            <div class="mt-2 relative" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview"
                                    class="w-full h-40 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button"
                                    class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600"
                                    x-on:click="photoPreview = null; $refs.photo.value = ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @error('main_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gallery
                            Images</label>
                        <input type="file" name="gallery_images[]" multiple
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-[10px] text-gray-500 mt-2">You can select multiple images for the gallery</p>
                        @error('gallery_images.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Pricing & Inventory">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Regular Price
                            (₹) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-400">₹</span>
                            <input type="number" step="0.01" name="price" required value="{{ old('price') }}"
                                placeholder="0.00"
                                class="block w-full pl-8 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        </div>
                        @error('price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sale Price
                            (₹)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-400">₹</span>
                            <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price') }}"
                                placeholder="0.00"
                                class="block w-full pl-8 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        </div>
                        @error('sale_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Stock Quantity
                            <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" required value="{{ old('stock', 0) }}" placeholder="0"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="AUTO-GENERATE"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Organization">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label
                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Collections</label>
                        <select name="collection_ids[]" multiple size="3"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">Hold Cmd/Ctrl to select multiple</p>
                    </div>
                </div>

                <div class="flex items-center space-x-8 mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <label class="flex items-center group cursor-pointer">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span
                            class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Active</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="hidden" name="featured" value="0">
                        <input type="checkbox" name="featured" value="1" {{ old('featured') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span
                            class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Featured
                            Product</span>
                    </label>
                    <label class="flex items-center group cursor-pointer">
                        <input type="hidden" name="is_trending" value="0">
                        <input type="checkbox" name="is_trending" value="1" {{ old('is_trending') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span
                            class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-blue-600 transition-colors">Trending
                            Product</span>
                    </label>
                </div>
            </x-admin.card>

            <x-admin.card title="SEO Settings">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta
                            Title</label>
                        <input type="text" name="seo_meta[meta_title]" value="{{ old('seo_meta.meta_title') }}"
                            placeholder="SEO Title"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta
                            Description</label>
                        <textarea name="seo_meta[meta_description]" rows="2"
                            placeholder="SEO Description for search engines..."
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('seo_meta.meta_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Canonical
                            URL</label>
                        <input type="text" name="seo_meta[canonical_url]" value="{{ old('seo_meta.canonical_url') }}"
                            placeholder="https://example.com/product-url"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit"
                    class="px-10 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 transition-all shadow-lg hover:shadow-blue-500/30 font-bold text-lg">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>