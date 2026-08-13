<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Bulk Order Inquiries</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage customer requests for larger quantities.</p>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">{{ $inquiries->total() }} total</span>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
        @endif

        <x-admin.card>
            <form method="GET" action="{{ route('admin.bulk-orders.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search customer, email, phone, product or SKU"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <select name="status" class="rounded-lg border border-gray-300 px-4 py-2 text-sm outline-none dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">All statuses</option>
                    @foreach(['new', 'contacted', 'quoted', 'converted', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">Apply filters</button>
                <a href="{{ route('admin.bulk-orders.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-center text-sm font-semibold text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">Reset</a>
            </form>
        </x-admin.card>

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-5 py-4 text-xs font-bold uppercase text-gray-500">Customer</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase text-gray-500">Product</th>
                        <th class="px-5 py-4 text-center text-xs font-bold uppercase text-gray-500">Quantity</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase text-gray-500">Status</th>
                        <th class="px-5 py-4 text-xs font-bold uppercase text-gray-500">Received</th>
                        <th class="px-5 py-4 text-right text-xs font-bold uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inquiry->name }}</p>
                                <p class="text-xs text-gray-500">{{ $inquiry->email }} · {{ $inquiry->phone }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="max-w-sm truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $inquiry->product_name }}</p>
                                <p class="text-xs font-mono text-gray-500">{{ $inquiry->product_sku ?: 'No SKU' }}</p>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-gray-900 dark:text-white">{{ number_format($inquiry->quantity) }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $inquiry->status === 'new' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">{{ ucfirst($inquiry->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500">{{ $inquiry->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.bulk-orders.show', $inquiry) }}" class="font-semibold text-blue-600 hover:text-blue-800">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">No bulk order inquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">{{ $inquiries->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
