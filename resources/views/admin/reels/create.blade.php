<x-layouts.admin>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Add New Reel</h2>
            <a href="{{ route('admin.reels.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Back</a>
        </div>

        <form action="{{ route('admin.reels.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            
            <x-admin.card title="Reel Source">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Reel Video Link <span class="text-red-500">*</span></label>
                        <input type="url" name="video_url" required value="{{ old('video_url') }}" placeholder="https://www.instagram.com/reels/..."
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        <p class="text-[10px] text-gray-500 mt-1">Paste the full URL of the reel (Instagram, TikTok, YouTube Shorts, etc.)</p>
                        @error('video_url') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Custom Thumbnail (Optional)</label>
                        <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
                            <input type="file" name="thumbnail" class="hidden" x-ref="photo"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   ">
                            <div class="mt-2" x-show="!photoPreview">
                                <div class="w-40 h-64 bg-gray-100 dark:bg-gray-700 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                     x-on:click.prevent="$refs.photo.click()">
                                    <div class="text-center p-4">
                                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <span class="mt-2 block text-[10px] text-gray-500">Upload 9:16 thumbnail</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2 relative w-40" x-show="photoPreview" x-cloak>
                                <img :src="photoPreview" class="w-full h-64 object-cover rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                                <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600"
                                        x-on:click="photoPreview = null; $refs.photo.value = ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('thumbnail') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Reel Details">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Title (Display Name)</label>
                        <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g. Unboxing our new sneakers"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Link to Product</label>
                        <select name="product_id" 
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            <option value="">No product linked</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active (Visible on Store)</label>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Save Reel
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
