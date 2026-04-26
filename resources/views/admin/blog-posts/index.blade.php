<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Blog Posts</h2>
            <a href="{{ route('admin.blog-posts.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm text-sm">
                Add New Post
            </a>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Post</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Category</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Published</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($posts as $post)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 flex items-center space-x-3">
                            @if($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-12 h-12 rounded object-cover">
                            @else
                                <div class="w-12 h-12 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-[10px]">No Img</div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($post->title, 40) }}</div>
                                <div class="text-xs text-gray-500">{{ $post->slug }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $post->category->name ?? 'Uncategorized' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $post->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $post->status ? 'Active' : 'Draft' }}
                            </span>
                            @if($post->is_featured)
                                <span class="ml-1 px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Featured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Not set' }}
                        </td>
                        <td class="px-6 py-4 space-x-2 flex items-center">
                            <a href="{{ route('admin.blog-posts.edit', $post->id) }}" class="text-blue-500 hover:text-blue-700 text-sm">Edit</a>
                            <button onclick="deletePost({{ $post->id }})" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No posts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function deletePost(id) {
            if (!confirm('Are you sure you want to delete this post?')) return;
            try {
                let res = await axios.delete(`/admin/blog-posts/${id}`, {
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
