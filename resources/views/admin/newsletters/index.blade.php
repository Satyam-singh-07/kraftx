<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Newsletter Subscriptions</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $newsletters->total() }}</p>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Date Subscribed</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($newsletters as $newsletter)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $newsletter->email }}</p>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-gray-600 dark:text-gray-400">
                                {{ $newsletter->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <form method="POST" action="{{ route('admin.newsletters.destroy', $newsletter->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this subscription?')" class="text-red-500 hover:text-red-700">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No newsletter subscriptions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $newsletters->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
