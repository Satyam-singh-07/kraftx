<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Bulk Order Inquiries</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review, contact, and track customers requesting larger quantities.</p>
            </div>
            <span class="hidden rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 sm:inline-flex dark:bg-blue-900/30 dark:text-blue-300">{{ $inquiries->total() }} shown</span>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">
            @foreach([
                ['label' => 'New', 'status' => 'new', 'color' => 'blue'],
                ['label' => 'Contacted', 'status' => 'contacted', 'color' => 'amber'],
                ['label' => 'Quoted', 'status' => 'quoted', 'color' => 'violet'],
                ['label' => 'Converted', 'status' => 'converted', 'color' => 'green'],
                ['label' => 'Closed', 'status' => 'closed', 'color' => 'gray'],
            ] as $summary)
                <a href="{{ route('admin.bulk-orders.index', ['status' => $summary['status']]) }}" class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $summary['label'] }}</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $statusCounts[$summary['status']] ?? 0 }}</p>
                </a>
            @endforeach
        </div>

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

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">Requests</h3>
                    <p class="mt-1 text-xs text-gray-500">Newest enquiries appear first.</p>
                </div>
                <span class="text-xs font-semibold text-gray-500">{{ $inquiries->total() }} result{{ $inquiries->total() === 1 ? '' : 's' }}</span>
            </div>
            <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">Name</th>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">Contact</th>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">Product</th>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">SKU</th>
                        <th class="whitespace-nowrap px-5 py-4 text-center text-xs font-bold uppercase tracking-wide text-gray-500">Qty.</th>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="whitespace-nowrap px-5 py-4 text-xs font-bold uppercase tracking-wide text-gray-500">Received</th>
                        <th class="whitespace-nowrap px-5 py-4 text-right text-xs font-bold uppercase tracking-wide text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $inquiry->name }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <a href="mailto:{{ $inquiry->email }}" class="mt-1 block text-xs text-blue-600 hover:underline">{{ $inquiry->email }}</a>
                                <a href="tel:{{ $inquiry->phone }}" class="mt-0.5 block text-xs text-gray-500 hover:text-gray-700">{{ $inquiry->phone }}</a>
                            </td>
                            <td class="px-5 py-4">
                                @php($shortProductName = mb_substr($inquiry->product_name, 0, 10))
                                <p class="whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white" title="{{ $inquiry->product_name }}">
                                    {{ $shortProductName }}{{ mb_strlen($inquiry->product_name) > 10 ? '...' : '' }}
                                </p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="whitespace-nowrap font-mono text-xs text-gray-600 dark:text-gray-300">{{ $inquiry->product_sku ?: 'No SKU' }}</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-bold text-gray-900 dark:text-white">{{ number_format($inquiry->quantity) }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ match($inquiry->status) {
                                    'new' => 'bg-blue-100 text-blue-700',
                                    'contacted' => 'bg-amber-100 text-amber-700',
                                    'quoted' => 'bg-violet-100 text-violet-700',
                                    'converted' => 'bg-green-100 text-green-700',
                                    default => 'bg-gray-100 text-gray-700',
                                } }}">{{ ucfirst($inquiry->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-500">{{ $inquiry->created_at->format('d M Y, h:i A') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.bulk-orders.show', $inquiry) }}" class="inline-flex items-center rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100">View details <span class="ml-1">→</span></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-sm text-gray-500">No bulk order inquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-700">{{ $inquiries->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
