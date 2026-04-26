<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Contact Messages</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $messages->total() }}</p>
        </div>

        <x-admin.card>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, phone or message..."
                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">

                <select name="is_read" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                    <option value="">All Status</option>
                    <option value="0" {{ ($filters['is_read'] ?? '') === '0' ? 'selected' : '' }}>Unread</option>
                    <option value="1" {{ ($filters['is_read'] ?? '') === '1' ? 'selected' : '' }}>Read</option>
                </select>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors font-bold text-sm">
                    Apply Filters
                </button>
            </form>
        </x-admin.card>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Sender</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Contact</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Message</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($messages as $message)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $message->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $message->ip_address ?: 'No IP' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-gray-900 dark:text-white">{{ $message->email }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $message->phone ?: 'No phone' }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-lg">{{ $message->message }}</p>
                            </td>
                            <td class="px-6 py-4 align-top text-sm text-gray-600 dark:text-gray-400">
                                {{ $message->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $message->is_read ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ $message->is_read ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top space-x-2">
                                <form method="POST" action="{{ route('admin.contact-messages.mark-read', $message->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-blue-500 hover:text-blue-700">
                                        {{ $message->is_read ? 'Mark Unread' : 'Mark Read' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.contact-messages.destroy', $message->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Delete this message?')" class="text-red-500 hover:text-red-700">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No contact messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</x-layouts.admin>
