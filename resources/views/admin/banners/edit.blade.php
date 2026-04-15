<x-layouts.admin>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Banner</h2>
            <a href="{{ route('admin.banners.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Back</a>
        </div>

        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <x-admin.card title="Banner Media">
                <div class="space-y-4 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Banner Image</label>
                        <div x-data="{ photoName: null, photoPreview: '{{ asset('storage/' . $banner->image) }}' }" class="col-span-6 sm:col-span-4">
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
                                <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                     x-on:click.prevent="$refs.photo.click()">
                                    <span class="text-gray-500 text-sm">Click to upload banner</span>
                                </div>
                            </div>
                            <div class="mt-2 relative" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview" class="w-full h-48 object-cover rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button" class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1.5 shadow-md hover:bg-blue-700"
                                        x-on:click.prevent="$refs.photo.click()">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Mobile Banner Image</label>
                        <div x-data="{ photoName: null, photoPreview: '{{ $banner->mobile_image ? asset('storage/' . $banner->mobile_image) : '' }}' }" class="col-span-6 sm:col-span-4">
                            <input type="file" name="mobile_image" class="hidden" x-ref="mobilePhoto"
                                   x-on:change="
                                        photoName = $refs.mobilePhoto.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.mobilePhoto.files[0]);
                                   ">
                            <div class="mt-2" x-show="!photoPreview">
                                <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                     x-on:click.prevent="$refs.mobilePhoto.click()">
                                    <span class="text-gray-500 text-sm">Click to upload mobile banner</span>
                                </div>
                            </div>
                            <div class="mt-2 relative" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview" class="w-full h-48 object-cover rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button" class="absolute top-2 right-2 bg-blue-600 text-white rounded-full p-1.5 shadow-md hover:bg-blue-700"
                                        x-on:click.prevent="$refs.mobilePhoto.click()">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('mobile_image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Banner Details">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Main Title</label>
                        <input type="text" name="title" value="{{ old('title', $banner->title) }}" placeholder="e.g. New Summer Collection"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Subtitle / Small Text</label>
                        <input type="text" name="subtitle" value="{{ old('subtitle', $banner->subtitle) }}" placeholder="e.g. Up to 50% Off"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Target Link (URL)</label>
                        <input type="text" name="link" value="{{ old('link', $banner->link) }}" placeholder="https://..."
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Placement</label>
                        <select name="placement" required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            <option value="home_main" {{ old('placement', $banner->placement) == 'home_main' ? 'selected' : '' }}>Home Main Slider</option>
                            <option value="home_sidebar" {{ old('placement', $banner->placement) == 'home_sidebar' ? 'selected' : '' }}>Home Sidebar</option>
                            <option value="promotional" {{ old('placement', $banner->placement) == 'promotional' ? 'selected' : '' }}>Promotional Strip</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $banner->sort_order) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div class="flex items-center space-x-3 pt-6">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', $banner->status) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Banner</label>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Update Banner
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
