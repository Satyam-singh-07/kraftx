<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Blog Category: {{ $blogCategory->name }}</h2>
            <a href="{{ route('admin.blog-categories.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to List</a>
        </div>

        <form action="{{ route('admin.blog-categories.update', $blogCategory->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <x-admin.card title="Category Details">
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $blogCategory->name) }}" placeholder="Category name"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug (URL)</label>
                        <input type="text" name="slug" value="{{ old('slug', $blogCategory->slug) }}" placeholder="category-slug"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="3" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('description', $blogCategory->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Image</label>
                            @if($blogCategory->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($blogCategory->image) }}" alt="" class="w-20 h-20 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div class="flex items-center pt-6">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" id="status" value="1" {{ $blogCategory->status ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="status" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Advanced SEO">
                <div class="grid grid-cols-1 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Title</label>
                        <input type="text" name="seo[meta_title]" value="{{ old('seo.meta_title', $blogCategory->seoMeta->meta_title ?? '') }}" placeholder="SEO title"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Description</label>
                        <textarea name="seo[meta_description]" rows="2" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">{{ old('seo.meta_description', $blogCategory->seoMeta->meta_description ?? '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Meta Keywords</label>
                        <input type="text" name="seo[meta_keywords]" value="{{ old('seo.meta_keywords', $blogCategory->seoMeta->meta_keywords ?? '') }}" placeholder="keyword1, keyword2"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Canonical URL</label>
                        <input type="url" name="seo[canonical_url]" value="{{ old('seo.canonical_url', $blogCategory->seoMeta->canonical_url ?? '') }}" placeholder="https://..."
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">OG Image</label>
                        @if(isset($blogCategory->seoMeta->og_image))
                            <div class="mb-2">
                                <img src="{{ Storage::url($blogCategory->seoMeta->og_image) }}" alt="" class="w-20 h-20 object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="seo[og_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
