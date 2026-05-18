<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $product->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Users interested in this product.</p>
            </div>
            <a href="{{ route('admin.product-demands.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Back</a>
        </div>

        <x-admin.card>
            <form method="GET" action="{{ route('admin.product-demands.show', $product) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
                        class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select name="notified" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        <option value="" {{ ($filters['notified'] ?? '') === '' ? 'selected' : '' }}>All</option>
                        <option value="0" {{ ($filters['notified'] ?? '') === '0' ? 'selected' : '' }}>Unnotified</option>
                        <option value="1" {{ ($filters['notified'] ?? '') === '1' ? 'selected' : '' }}>Notified</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold">Filter</button>
                    <a href="{{ route('admin.product-demands.show', $product) }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Reset</a>
                </div>
            </form>
        </x-admin.card>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">User</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Requested</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $request->user?->name ?? 'Deleted user' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->user?->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $request->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $request->is_notified ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $request->is_notified ? 'Notified' : 'Pending' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No users match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $requests->links() }}
    </div>
</x-layouts.admin>
