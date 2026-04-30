<x-layouts.admin>
    @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .editor-toolbar {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
                padding: 8px;
                background: #f8f9fa;
                border: 1px solid #dee2e6;
                border-bottom: none;
                border-radius: 8px 8px 0 0;
                align-items: center;
            }
            .editor-toolbar button, .editor-toolbar select {
                padding: 4px 8px;
                background: white;
                border: 1px solid #ced4da;
                border-radius: 4px;
                cursor: pointer;
                transition: all 0.2s;
                height: 32px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 13px;
            }
            .editor-toolbar button:hover {
                background: #e9ecef;
            }
            .editor-toolbar select {
                padding: 0 4px;
                outline: none;
            }
            .editor-area {
                min-height: 250px;
                padding: 16px;
                border: 1px solid #ced4da;
                border-radius: 0 0 8px 8px;
                background: white;
                outline: none;
                font-size: 16px;
                line-height: 1.5;
            }
            .editor-area:focus {
                border-color: #86b7fe;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }
            /* Styling for common tags inside editor */
            .editor-area h1 { font-size: 2em; font-weight: bold; }
            .editor-area h2 { font-size: 1.5em; font-weight: bold; }
            .editor-area h3 { font-size: 1.17em; font-weight: bold; }
            .editor-area ul { list-style-type: disc; padding-left: 20px; }
            .editor-area ol { list-style-type: decimal; padding-left: 20px; }

            .dark .editor-toolbar { background: #374151; border-color: #4b5563; }
            .dark .editor-toolbar button, .dark .editor-toolbar select { background: #1f2937; border-color: #4b5563; color: white; }
            .dark .editor-area { background: #1f2937; border-color: #4b5563; color: white; }
        </style>
    @endpush

    <div class="max-w-4xl mx-auto space-y-6" x-data="productEdit()">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Product: {{ $product->name }}</h2>
            <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Back to List</a>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data" id="productForm">
            @csrf
            @method('PUT')
            
            <x-admin.card title="Basic Information">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $product->name) }}" placeholder="Enter product name"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" placeholder="product-slug"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Short Description</label>
                        <textarea name="short_description" rows="2" placeholder="Brief summary of the product..."
                                  class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>

                    <!-- Custom Rich Text Editor for Full Description -->
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Description *</label>
                        <div class="editor-toolbar" data-target="description">
                            <select class="editor-font-size" data-command="fontSize">
                                <option value="3">Normal</option>
                                <option value="1">Small</option>
                                <option value="2">Medium</option>
                                <option value="4">Large</option>
                                <option value="5">Extra Large</option>
                                <option value="6">Huge</option>
                                <option value="7">Giant</option>
                            </select>
                            <select class="editor-format" data-command="formatBlock">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="justifyLeft" title="Align Left"><i class="fas fa-align-left"></i></button>
                            <button type="button" data-command="justifyCenter" title="Align Center"><i class="fas fa-align-center"></i></button>
                            <button type="button" data-command="justifyRight" title="Align Right"><i class="fas fa-align-right"></i></button>
                        </div>
                        <div class="editor-area" contenteditable="true" id="editor-description" placeholder="Enter detailed information here..."></div>
                        <input type="hidden" name="description" id="input-description" value="{{ old('description', $product->description) }}">
                    </div>

                    <!-- Custom Rich Text Editor for Perfect Placement -->
                    <div class="mb-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Perfect Placement</label>
                        <div class="editor-toolbar" data-target="perfect_placement">
                            <select class="editor-font-size" data-command="fontSize">
                                <option value="3">Normal</option>
                                <option value="1">Small</option>
                                <option value="2">Medium</option>
                                <option value="4">Large</option>
                                <option value="5">Extra Large</option>
                                <option value="6">Huge</option>
                                <option value="7">Giant</option>
                            </select>
                            <select class="editor-format" data-command="formatBlock">
                                <option value="p">Paragraph</option>
                                <option value="h1">Heading 1</option>
                                <option value="h2">Heading 2</option>
                                <option value="h3">Heading 3</option>
                                <option value="h4">Heading 4</option>
                            </select>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                            <button type="button" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                            <button type="button" data-command="underline" title="Underline"><i class="fas fa-underline"></i></button>
                            <div class="mx-1 h-6 border-l border-gray-300"></div>
                            <button type="button" data-command="insertUnorderedList" title="Bullet List"><i class="fas fa-list-ul"></i></button>
                            <button type="button" data-command="insertOrderedList" title="Numbered List"><i class="fas fa-list-ol"></i></button>
                        </div>
                        <div class="editor-area" contenteditable="true" id="editor-perfect_placement" placeholder="Where would this product look best?..."></div>
                        <input type="hidden" name="perfect_placement" id="input-perfect_placement" value="{{ old('perfect_placement', $product->perfect_placement) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Product Video URL</label>
                        <input type="text" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="YouTube or Vimeo link"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Product Media">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Main Image (Primary)</label>
                        <div x-data="{ photoName: null, photoPreview: '{{ $product->images->where('is_primary', true)->first() ? asset('storage/' . $product->images->where('is_primary', true)->first()->image_path) : '' }}' }" class="col-span-6 sm:col-span-4">
                            <input type="file" name="main_image" class="hidden" x-ref="photo"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   ">
                            <div class="mt-2 relative" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview" class="w-full h-40 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button" class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1 shadow-md hover:bg-blue-700"
                                        x-on:click.prevent="$refs.photo.click()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('main_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gallery Images</label>
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            @foreach($product->images as $image)
                                <div class="relative group" id="image-container-{{ $image->id }}">
                                    <img src="{{ str_starts_with($image->image_path, 'assets/') ? asset($image->image_path) : Storage::url($image->image_path) }}" 
                                         class="w-full h-20 object-cover rounded-lg {{ $image->is_primary ? 'ring-2 ring-blue-500' : '' }}">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                        <button type="button" @click="deleteImage({{ $image->id }})" class="p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <input type="file" name="gallery_images[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('gallery_images.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Size & Weight Image</label>
                        @if($product->size_weight_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->size_weight_image) }}" class="w-32 h-32 object-cover rounded-lg border">
                            </div>
                        @endif
                        <input type="file" name="size_weight_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('size_weight_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Pricing & Inventory">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Regular Price (₹) *</label>
                        <input type="number" step="0.01" name="price" required value="{{ old('price', $product->price) }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sale Price (₹)</label>
                        <input type="number" step="0.01" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Stock *</label>
                        <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Shipping Information (Shiprocket)">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Weight (Kg) *</label>
                        <input type="number" step="0.001" name="weight" required value="{{ old('weight', $product->weight) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        <p class="text-xs text-gray-500 mt-1">Example: 0.500 for 500g</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">HSN Code</label>
                        <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Length (Cm) *</label>
                        <input type="number" step="0.1" name="length" required value="{{ old('length', $product->length) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Width (Cm) *</label>
                        <input type="number" step="0.1" name="width" required value="{{ old('width', $product->width) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Height (Cm) *</label>
                        <input type="number" step="0.1" name="height" required value="{{ old('height', $product->height) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Shipping Information (Shiprocket)">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Weight (Kg) *</label>
                        <input type="number" step="0.001" name="weight" required value="{{ old('weight', $product->weight) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        <p class="text-xs text-gray-500 mt-1">Example: 0.500 for 500g</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">HSN Code</label>
                        <input type="text" name="hsn_code" value="{{ old('hsn_code', $product->hsn_code) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Length (Cm) *</label>
                        <input type="number" step="0.1" name="length" required value="{{ old('length', $product->length) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Width (Cm) *</label>
                        <input type="number" step="0.1" name="width" required value="{{ old('width', $product->width) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Height (Cm) *</label>
                        <input type="number" step="0.1" name="height" required value="{{ old('height', $product->height) }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Organization">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Collections</label>
                        <select name="collection_ids[]" multiple class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}" {{ $product->collections->contains($collection->id) ? 'selected' : '' }}>
                                    {{ $collection->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple collections.</p>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="status" id="status" value="1" {{ $product->status ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="status" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active (Visible on store)</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="featured" id="featured" value="1" {{ $product->featured ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="featured" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Featured Product</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_trending" id="is_trending" value="1" {{ $product->is_trending ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_trending" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Trending Product</label>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Advanced SEO">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                        <input type="text" name="seo_meta[meta_title]" value="{{ old('seo_meta.meta_title', $product->seoMeta->meta_title ?? '') }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Canonical URL</label>
                        <input type="url" name="seo_meta[canonical_url]" value="{{ old('seo_meta.canonical_url', $product->seoMeta->canonical_url ?? '') }}"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                        <textarea name="seo_meta[meta_description]" rows="3"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('seo_meta.meta_description', $product->seoMeta->meta_description ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Keywords</label>
                        <input type="text" name="seo_meta[meta_keywords]" value="{{ old('seo_meta.meta_keywords', $product->seoMeta->meta_keywords ?? '') }}"
                            placeholder="keyword1, keyword2"
                            class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Robots</label>
                        <select name="seo_meta[meta_robots]" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                            <option value="">Default</option>
                            <option value="index,follow" {{ old('seo_meta.meta_robots', $product->seoMeta->meta_robots ?? 'index,follow') === 'index,follow' ? 'selected' : '' }}>index,follow</option>
                            <option value="noindex,follow" {{ old('seo_meta.meta_robots', $product->seoMeta->meta_robots ?? '') === 'noindex,follow' ? 'selected' : '' }}>noindex,follow</option>
                            <option value="noindex,nofollow" {{ old('seo_meta.meta_robots', $product->seoMeta->meta_robots ?? '') === 'noindex,nofollow' ? 'selected' : '' }}>noindex,nofollow</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Open Graph Image</label>
                        @if($product->seoMeta?->og_image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($product->seoMeta->og_image) }}" alt="{{ $product->name }} SEO image" class="w-20 h-20 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="seo_meta[og_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl hover:bg-blue-700 font-bold text-lg">
                    Update Product
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editors = ['description', 'perfect_placement'];
            
            editors.forEach(id => {
                const editor = document.getElementById('editor-' + id);
                const input = document.getElementById('input-' + id);
                const toolbar = document.querySelector(`.editor-toolbar[data-target="${id}"]`);
                
                if (input.value) {
                    editor.innerHTML = input.value;
                }

                // Button commands
                toolbar.querySelectorAll('button').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const command = this.getAttribute('data-command');
                        document.execCommand(command, false, null);
                        editor.focus();
                    });
                });

                // Select commands (Font Size / Format)
                toolbar.querySelectorAll('select').forEach(select => {
                    select.addEventListener('change', function() {
                        const command = this.getAttribute('data-command');
                        const value = this.value;
                        
                        if (command === 'formatBlock') {
                            document.execCommand(command, false, `<${value}>`);
                        } else {
                            document.execCommand(command, false, value);
                        }
                        editor.focus();
                    });
                });

                editor.addEventListener('input', function() {
                    input.value = this.innerHTML;
                });
            });

            document.getElementById('productForm').addEventListener('submit', function() {
                editors.forEach(id => {
                    document.getElementById('input-' + id).value = document.getElementById('editor-' + id).innerHTML;
                });
            });
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('productEdit', () => ({
                async deleteImage(imageId) {
                    if (!confirm('Are you sure you want to delete this image?')) return;
                    try {
                        let res = await axios.delete(`/admin/products/images/${imageId}`, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (res.data.success) {
                            document.getElementById(`image-container-${imageId}`).remove();
                        }
                    } catch (e) {
                        alert('Failed to delete image.');
                    }
                }
            }));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @endpush
</x-layouts.admin>
