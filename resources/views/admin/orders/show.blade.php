<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Order Details: {{ $order->order_number }}</h2>
            </div>
            
            <div class="flex items-center space-x-2">
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Items Card -->
                <x-admin.card title="Order Items">
                    <div class="overflow-x-auto mt-4">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Price</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-center">Qty</th>
                                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="px-4 py-4 flex items-center space-x-3">
                                        @if($item->product && $item->product->primary_image)
                                            <img src="{{ asset('storage/' . $item->product->primary_image->image_path) }}" class="w-10 h-10 rounded object-cover">
                                        @else
                                            <div class="w-10 h-10 rounded bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-[10px]">No Img</div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-500">SKU: {{ $item->sku }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400">₹{{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-600 dark:text-gray-400 text-center">{{ $item->quantity }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 dark:text-white font-semibold text-right">₹{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 border-t border-gray-100 dark:border-gray-700 pt-4 space-y-2">
                        <div class="flex justify-end text-sm">
                            <span class="text-gray-500 w-32 text-right">Subtotal:</span>
                            <span class="text-gray-900 dark:text-white font-medium w-32 text-right">₹{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        @if($order->shipping_amount > 0)
                        <div class="flex justify-end text-sm">
                            <span class="text-gray-500 w-32 text-right">Shipping:</span>
                            <span class="text-gray-900 dark:text-white font-medium w-32 text-right">₹{{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->tax_amount > 0)
                        <div class="flex justify-end text-sm">
                            <span class="text-gray-500 w-32 text-right">Tax:</span>
                            <span class="text-gray-900 dark:text-white font-medium w-32 text-right">₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($order->discount_amount > 0)
                        <div class="flex justify-end text-sm text-red-500">
                            <span class="w-32 text-right">Discount:</span>
                            <span class="font-medium w-32 text-right">-₹{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-end text-lg font-bold border-t border-gray-100 dark:border-gray-700 mt-2 pt-2">
                            <span class="text-gray-800 dark:text-white w-32 text-right">Total:</span>
                            <span class="text-blue-600 dark:text-blue-400 w-32 text-right">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </x-admin.card>

                <!-- Shipping Address Card -->
                <x-admin.card title="Shipping Information">
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Delivery Address</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                {{ $order->customer_name }}<br>
                                {{ $order->shipping_address }}<br>
                                {{ $order->shipping_city }}, {{ $order->shipping_state }} - {{ $order->shipping_pincode }}<br>
                                {{ $order->shipping_country }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Contact Details</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <span class="text-gray-500">Email:</span> {{ $order->customer_email }}<br>
                                <span class="text-gray-500">Phone:</span> {{ $order->customer_phone }}
                            </p>
                        </div>
                    </div>
                </x-admin.card>
            </div>

            <!-- Customer & Payment Info -->
            <div class="space-y-6">
                <x-admin.card title="Payment Info">
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Method</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->payment_method ?? 'N/A' }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Payment Status</label>
                            @php
                                $pStatusClasses = [
                                    'pending' => 'text-yellow-600',
                                    'paid' => 'text-green-600',
                                    'failed' => 'text-red-600',
                                    'refunded' => 'text-gray-600',
                                ];
                                $pClass = $pStatusClasses[$order->payment_status] ?? 'text-gray-600';
                            @endphp
                            <div class="text-sm font-bold {{ $pClass }} uppercase">{{ $order->payment_status }}</div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Shiprocket Order ID</label>
                            <div class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $order->shiprocket_order_id }}</div>
                        </div>
                    </div>
                </x-admin.card>

                @if($order->notes)
                <x-admin.card title="Order Notes">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">{{ $order->notes }}</p>
                </x-admin.card>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
