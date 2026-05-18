<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Product Demands</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Back in stock notification demand by product.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <x-admin.card>
                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Most Requested Products</p>
                <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['most_requested_products']->count() }}</p>
            </x-admin.card>
            <x-admin.card>
                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Total Pending Notifications</p>
                <p class="mt-2 text-3xl font-bold text-orange-600">{{ $stats['total_pending_notifications'] }}</p>
            </x-admin.card>
            <x-admin.card>
                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Total Notified Users</p>
                <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['total_notified_users'] }}</p>
            </x-admin.card>
            <x-admin.card>
                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">Total Waiting Users</p>
                <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['total_waiting_users'] }}</p>
            </x-admin.card>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <x-admin.card title="Most Demanded Products">
                <div class="space-y-3">
                    @forelse($stats['most_requested_products'] as $product)
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">Stock: {{ $product->stock }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">{{ $product->notify_requests_count }} requests</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No demand yet.</p>
                    @endforelse
                </div>
            </x-admin.card>

            <x-admin.card title="Recently Requested Products">
                <div class="space-y-3">
                    @forelse($stats['recent_requests'] as $request)
                        <div class="flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $request->product?->name ?? 'Deleted product' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->user?->email }} · {{ $request->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="px-2 py-1 rounded-full {{ $request->is_notified ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }} text-xs font-bold">
                                {{ $request->is_notified ? 'Notified' : 'Waiting' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No recent requests.</p>
                    @endforelse
                </div>
            </x-admin.card>
        </div>

        <x-admin.card>
            <form method="GET" action="{{ route('admin.product-demands.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product</label>
                    <input type="text" name="product" value="{{ $filters['product'] ?? '' }}" placeholder="Name or SKU"
                        class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ $filters['date'] ?? '' }}"
                        class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select name="notified" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm">
                        <option value="" {{ ($filters['notified'] ?? '') === '' ? 'selected' : '' }}>All</option>
                        <option value="0" {{ ($filters['notified'] ?? '') === '0' ? 'selected' : '' }}>Unnotified</option>
                        <option value="1" {{ ($filters['notified'] ?? '') === '1' ? 'selected' : '' }}>Notified</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sort</label>
                    <select name="sort" class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none text-sm">
                        <option value="demand" {{ ($filters['sort'] ?? 'demand') === 'demand' ? 'selected' : '' }}>Highest Demand</option>
                        <option value="latest" {{ ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' }}>Latest Product</option>
                    </select>
                </div>
                <div class="md:col-span-5 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-bold">Filter</button>
                    <a href="{{ route('admin.product-demands.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 text-sm">Reset</a>
                </div>
            </form>
        </x-admin.card>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Product</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-center">Requests</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-center">Stock</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                        @php
                            $isHighDemandLowStock = $product->notify_requests_count >= 5 && $product->stock <= \App\Http\Controllers\Admin\InventoryController::LOW_STOCK_THRESHOLD;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 {{ $isHighDemandLowStock ? 'bg-orange-50 dark:bg-orange-900/10' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php $image = $product->images->first(); @endphp
                                    @if($image)
                                        <img src="{{ asset('storage/' . $image->image_path) }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 dark:border-gray-600" alt="{{ $product->name }}">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700"></div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-500 font-mono">SKU: {{ $product->sku }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $product->notify_requests_count }}</div>
                                @if($isHighDemandLowStock)
                                    <div class="mt-1 text-[10px] font-bold uppercase text-orange-600">High demand + low stock</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 rounded text-xs font-bold {{ $product->stock > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $product->stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.product-demands.show', $product) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">View Users</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">No demand records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $products->links() }}
    </div>
</x-layouts.admin>
