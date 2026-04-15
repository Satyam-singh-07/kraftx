<x-layouts.admin>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Create Collection</h2>
            <a href="{{ route('admin.collections.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Back</a>
        </div>

        <form action="{{ route('admin.collections.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            
            <x-admin.card>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Collection Image</label>
                        <div x-data="{ photoName: null, photoPreview: null }" class="col-span-6 sm:col-span-4">
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
                                <button type="button" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600"
                                        x-on:click="photoPreview = null; $refs.photo.value = ''">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </div>
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Collection Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Summer Sale"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug (Optional)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" placeholder="summer-sale"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="4" placeholder="Describe this collection..."
                                  class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('description') }}</textarea>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Collection</label>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Save Collection
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
