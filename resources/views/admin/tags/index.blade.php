<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Tags</h2>
            <a href="{{ route('admin.tags.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                Add New Tag
            </a>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Name</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Slug</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tags as $tag)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $tag->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $tag->slug }}</td>
                        <td class="px-6 py-4 space-x-2 flex items-center">
                            <a href="{{ route('admin.tags.edit', $tag->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">Edit</a>
                            <button onclick="deleteTag({{ $tag->id }})" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No tags found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $tags->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function deleteTag(id) {
            if (!confirm('Are you sure?')) return;
            try {
                let res = await axios.delete(`/admin/tags/${id}`, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.data.success) {
                    window.location.reload();
                }
            } catch (e) {
                alert('Failed to delete.');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @endpush
</x-layouts.admin>
