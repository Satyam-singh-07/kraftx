<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Customer Details: {{ $customer->name }}</h2>
            <div class="space-x-2">
                <a href="{{ route('admin.customers.edit', $customer->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-bold">
                    Edit Customer
                </a>
                <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors shadow-sm font-bold">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Basic Info -->
            <x-admin.card class="md:col-span-1">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Basic Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $customer->email }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Phone</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $customer->phone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</label>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $customer->status ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                            {{ $customer->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Joined Date</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $customer->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Address</label>
                        <p class="text-sm text-gray-900 dark:text-white font-medium">{{ $customer->address ?? 'N/A' }}</p>
                    </div>
                </div>
            </x-admin.card>

            <!-- Statistics -->
            <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-admin.card class="flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-2">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->orders->count() }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Orders</div>
                </x-admin.card>

                <x-admin.card class="flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 mb-2">
                        <i class="fas fa-shopping-cart text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->carts->count() }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Carts</div>
                </x-admin.card>

                <x-admin.card class="flex flex-col items-center justify-center p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400 mb-2">
                        <i class="fas fa-heart text-xl"></i>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->wishlists->count() }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Wishlist Items</div>
                </x-admin.card>
            </div>
        </div>

        <!-- Orders Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Order ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Date</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Total</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customer->orders->take(10) as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-sm">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#{{ $order->order_number }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-semibold">₹{{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($order->status === 'delivered') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($order->status === 'cancelled') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                                    @else bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 @endif">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline">View Details</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Carts Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Carts</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Cart ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Items</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Last Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customer->carts->sortByDesc('updated_at')->take(10) as $cart)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-sm">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#{{ $cart->id }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                <div class="flex flex-col">
                                    @foreach($cart->items as $item)
                                        <span>{{ $item->product->name ?? 'Unknown Product' }} (x{{ $item->quantity }})</span>
                                    @endforeach
                                    @if($cart->items->isEmpty())
                                        <span class="text-xs italic text-gray-400">Empty Cart</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cart->status === 'active' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400' }}">
                                    {{ ucfirst($cart->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $cart->updated_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No carts found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Wishlist Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Wishlist Items</h3>
            </div>
            <div class="p-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($customer->wishlists as $wishlist)
                <div class="flex flex-col items-center">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-900/30 rounded-lg overflow-hidden mb-2">
                        @if($wishlist->product && $wishlist->product->images->isNotEmpty())
                            <img src="{{ Storage::url($wishlist->product->images->first()->image_path) }}" alt="{{ $wishlist->product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400 italic text-[10px] text-center px-1">No Image</div>
                        @endif
                    </div>
                    <span class="text-xs text-center text-gray-700 dark:text-gray-300 truncate w-full" title="{{ $wishlist->product->name ?? 'Unknown' }}">
                        {{ $wishlist->product->name ?? 'Unknown Product' }}
                    </span>
                </div>
                @empty
                <div class="col-span-full text-center py-4 text-gray-500 dark:text-gray-400 text-sm">
                    No items in wishlist.
                </div>
                @endforelse
            </div>
        </div>
        <!-- Blog Comments Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Blog Comments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Post</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Comment</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($customer->blogComments as $comment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-sm">
                            <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">
                                {{ $comment->post->title ?? 'Unknown Post' }}
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400 max-w-xs truncate" title="{{ $comment->comment }}">
                                {{ $comment->comment }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $comment->status === 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                    {{ ucfirst($comment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $comment->created_at->format('M d, Y') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No blog comments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
