<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Blog Comments</h2>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Author</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Comment</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">In Response To</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->name }}</div>
                            <div class="text-xs text-gray-500">{{ $comment->email }}</div>
                            <div class="text-[10px] text-gray-400">{{ $comment->created_at->format('M d, Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            <div class="max-w-xs truncate" title="{{ $comment->comment }}">{{ $comment->comment }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-blue-600">
                            <a href="{{ route('admin.blog-posts.edit', $comment->blog_post_id) }}" class="hover:underline">
                                {{ Str::limit($comment->post->title ?? 'Deleted Post', 30) }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <select onchange="updateStatus({{ $comment->id }}, this.value)" class="text-xs rounded-full px-2 py-1 border-none focus:ring-0
                                {{ $comment->status === 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $comment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $comment->status === 'spam' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                <option value="pending" {{ $comment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $comment->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="spam" {{ $comment->status === 'spam' ? 'selected' : '' }}>Spam</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 flex items-center">
                            <button onclick="deleteComment({{ $comment->id }})" class="text-red-500 hover:text-red-700 text-sm">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No comments found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $comments->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function updateStatus(id, status) {
            try {
                let res = await axios.patch(`/admin/blog-comments/${id}/status`, { status }, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (res.data.success) {
                    window.location.reload();
                }
            } catch (e) {
                alert('Failed to update status.');
            }
        }
        async function deleteComment(id) {
            if (!confirm('Are you sure?')) return;
            try {
                let res = await axios.delete(`/admin/blog-comments/${id}`, {
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
