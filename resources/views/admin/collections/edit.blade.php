<x-layouts.admin>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Collection: {{ $collection->name }}</h2>
            <a href="{{ route('admin.collections.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Back</a>
        </div>

        <form action="{{ route('admin.collections.update', $collection->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <x-admin.card>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Collection Image</label>
                        <div x-data="{ photoName: null, photoPreview: '{{ $collection->image ? asset('storage/' . $collection->image) : '' }}' }" class="col-span-6 sm:col-span-4">
                            <input type="file" name="image" class="hidden" x-ref="photo"
                                   x-on:change="
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
                                    <span class="text-gray-500 text-sm">Click to upload collection image</span>
                                </div>
                            </div>
                            <div class="mt-2 relative" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview" class="w-full h-40 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button" class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1 shadow-md hover:bg-blue-700"
                                        x-on:click.prevent="$refs.photo.click()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Collection Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $collection->name) }}" placeholder="e.g. Summer Sale"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $collection->slug) }}" placeholder="summer-sale"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="4" placeholder="Describe this collection..."
                                  class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('description', $collection->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', $collection->sort_order) }}"
                                   class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            @error('sort_order') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" id="status" value="1" {{ old('status', $collection->status) == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Collection</label>
                        </div>

                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="show_on_home" value="0">
                            <input type="checkbox" name="show_on_home" id="show_on_home" value="1" {{ old('show_on_home', $collection->show_on_home) == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="show_on_home" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show on Home Page</label>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Advanced SEO">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                        <input type="text" name="seo[meta_title]" value="{{ old('seo.meta_title', $collection->seoMeta->meta_title ?? '') }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Canonical URL</label>
                        <input type="url" name="seo[canonical_url]" value="{{ old('seo.canonical_url', $collection->seoMeta->canonical_url ?? '') }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                        <textarea name="seo[meta_description]" rows="3" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('seo.meta_description', $collection->seoMeta->meta_description ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Keywords</label>
                        <input type="text" name="seo[meta_keywords]" value="{{ old('seo.meta_keywords', $collection->seoMeta->meta_keywords ?? '') }}" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Robots</label>
                        <select name="seo[meta_robots]" class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                            <option value="index,follow" {{ old('seo.meta_robots', $collection->seoMeta->meta_robots ?? 'index,follow') === 'index,follow' ? 'selected' : '' }}>index,follow</option>
                            <option value="noindex,follow" {{ old('seo.meta_robots', $collection->seoMeta->meta_robots ?? '') === 'noindex,follow' ? 'selected' : '' }}>noindex,follow</option>
                            <option value="noindex,nofollow" {{ old('seo.meta_robots', $collection->seoMeta->meta_robots ?? '') === 'noindex,nofollow' ? 'selected' : '' }}>noindex,nofollow</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Open Graph Image</label>
                        @if($collection->seoMeta?->og_image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($collection->seoMeta->og_image) }}" alt="{{ $collection->name }} SEO image" class="w-20 h-20 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="seo[og_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Update Collection
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
