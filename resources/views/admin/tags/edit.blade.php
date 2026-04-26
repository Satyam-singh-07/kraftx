<x-layouts.admin>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Tag: {{ $tag->name }}</h2>
            <a href="{{ route('admin.tags.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">Back to List</a>
        </div>

        <form action="{{ route('admin.tags.update', $tag->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <x-admin.card title="Tag Details">
                <div class="space-y-4 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $tag->name) }}" placeholder="Tag name"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $tag->slug) }}" placeholder="tag-slug"
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white outline-none">
                        @error('slug') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold">
                    Update Tag
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
