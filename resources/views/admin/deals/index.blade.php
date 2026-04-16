<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Active Deals & Flash Sales</h2>
            <a href="{{ route('admin.deals.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-bold">
                Add Deal
            </a>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Title</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Discount</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Duration</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($deals as $deal)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $deal->title }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Priority: {{ $deal->priority }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-blue-600 dark:text-blue-400">
                            {{ $deal->discount_type === 'percentage' ? $deal->discount_value . '%' : '₹' . number_format($deal->discount_value, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-600 dark:text-gray-400">Starts: {{ $deal->start_date ? $deal->start_date->format('M d, Y H:i') : 'Immediate' }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Ends: {{ $deal->end_date ? $deal->end_date->format('M d, Y H:i') : 'Never' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if(!$deal->status)
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400">INACTIVE</span>
                            @elseif(!$deal->isValid())
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">EXPIRED</span>
                            @else
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">ACTIVE</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="{{ route('admin.deals.edit', $deal->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <form action="{{ route('admin.deals.destroy', $deal->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this deal?')" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No deals found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
