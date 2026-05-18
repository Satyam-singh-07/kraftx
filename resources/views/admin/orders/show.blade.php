<x-layouts.admin>
    <div class="space-y-6">
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>
        @endif

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
                        <option value="pending_payment" {{ $order->status === 'pending_payment' ? 'selected' : '' }}>Pending Payment</option>
                        <option value="cod_confirmed" {{ $order->status === 'cod_confirmed' ? 'selected' : '' }}>COD Confirmed</option>
                        <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="payment_failed" {{ $order->status === 'payment_failed' ? 'selected' : '' }}>Payment Failed</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </form>
                <form action="{{ route('admin.orders.update-status', $order->id) }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    @method('PATCH')
                    <select name="fulfillment_status" onchange="this.form.submit()" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-blue-500">
                        <option value="pending" {{ $order->fulfillment_status === 'pending' ? 'selected' : '' }}>Fulfillment Pending</option>
                        <option value="ready_to_ship" {{ $order->fulfillment_status === 'ready_to_ship' ? 'selected' : '' }}>Ready to Ship</option>
                        <option value="shipped" {{ $order->fulfillment_status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option value="in_transit" {{ $order->fulfillment_status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ $order->fulfillment_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="rto" {{ $order->fulfillment_status === 'rto' ? 'selected' : '' }}>RTO</option>
                        <option value="cancelled" {{ $order->fulfillment_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
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

                <x-admin.card title="Fulfillment Readiness">
                    <div class="mt-4 space-y-4">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Shipment eligibility</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Centralized checks for future provider shipment creation.</div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $shipmentEligibility->eligible ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $shipmentEligibility->eligible ? 'Eligible' : 'Needs Review' }}
                            </span>
                        </div>

                        @if($shipmentEligibility->reasons)
                            <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4">
                                <div class="text-xs font-bold text-yellow-900 uppercase mb-2">Blocking items</div>
                                <ul class="list-disc pl-5 text-sm text-yellow-900 space-y-1">
                                    @foreach($shipmentEligibility->reasons as $reason)
                                        <li>{{ $reason }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($shipmentEligibility->warnings)
                            <div class="rounded-lg bg-gray-50 dark:bg-gray-900/40 border border-gray-200 dark:border-gray-700 p-4">
                                <div class="text-xs font-bold text-gray-500 uppercase mb-2">Warnings</div>
                                <ul class="list-disc pl-5 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                    @foreach($shipmentEligibility->warnings as $warning)
                                        <li>{{ $warning }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                <div class="text-xs text-gray-500 uppercase font-bold">Fulfillment Status</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ \Illuminate\Support\Str::headline($order->fulfillment_status ?? 'pending') }}</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                <div class="text-xs text-gray-500 uppercase font-bold">Payment Gate</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $order->payment_method === 'Prepaid' ? \Illuminate\Support\Str::headline($order->payment_status) : 'COD Review' }}</div>
                            </div>
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                <div class="text-xs text-gray-500 uppercase font-bold">Shipments</div>
                                <div class="font-semibold text-gray-900 dark:text-white">{{ $order->shipments->count() }}</div>
                            </div>
                        </div>
                    </div>
                </x-admin.card>

                <x-admin.card title="Delhivery Serviceability">
                    <div class="mt-4 space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">Pincode {{ $order->shipping_pincode }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Used for fulfillment readiness only. No shipment is created here.</div>
                            </div>
                            <form method="POST" action="{{ route('admin.orders.serviceability.recheck', $order) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-semibold">
                                    Recheck Serviceability
                                </button>
                            </form>
                        </div>

                        @if($serviceability)
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-3 text-sm">
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                    <div class="text-xs text-gray-500 uppercase font-bold">Delivery</div>
                                    <div class="font-semibold {{ $serviceability->isServiceable ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $serviceability->isServiceable ? 'Serviceable' : 'Not Serviceable' }}
                                    </div>
                                </div>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                    <div class="text-xs text-gray-500 uppercase font-bold">COD</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $serviceability->codAvailable === null ? 'Unknown' : ($serviceability->codAvailable ? 'Available' : 'Unavailable') }}</div>
                                </div>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                    <div class="text-xs text-gray-500 uppercase font-bold">Prepaid</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $serviceability->prepaidAvailable === null ? 'Unknown' : ($serviceability->prepaidAvailable ? 'Available' : 'Unavailable') }}</div>
                                </div>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                    <div class="text-xs text-gray-500 uppercase font-bold">ETA</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $serviceability->estimatedDays ? $serviceability->estimatedDays.' days' : 'Unknown' }}</div>
                                </div>
                                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3">
                                    <div class="text-xs text-gray-500 uppercase font-bold">Last Checked</div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $serviceability->checkedAt ? $serviceability->checkedAt->format('d M Y H:i') : 'Never' }}</div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $serviceability->message }}</p>
                        @else
                            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-5 text-sm text-gray-500">
                                Serviceability has not been checked yet. Recheck before marking this order ready to ship.
                            </div>
                        @endif
                    </div>
                </x-admin.card>

                <x-admin.card title="Shipment Foundation">
                    <div class="mt-4 space-y-5">
                        @forelse($order->shipments as $shipment)
                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ ucfirst($shipment->provider) }} Shipment #{{ $shipment->id }}</div>
                                        <div class="text-xs text-gray-500">AWB: {{ $shipment->awb ?: 'Not generated' }}</div>
                                    </div>
                                    <span class="px-3 py-1 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-bold">
                                        {{ \Illuminate\Support\Str::headline($shipment->shipment_status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm mb-4">
                                    <div><span class="text-gray-500">Payment:</span> {{ $shipment->payment_mode }}</div>
                                    <div><span class="text-gray-500">COD:</span> ₹{{ number_format($shipment->cod_amount, 2) }}</div>
                                    <div><span class="text-gray-500">Invoice:</span> ₹{{ number_format($shipment->invoice_value, 2) }}</div>
                                    <div><span class="text-gray-500">Serviceability:</span> {{ $shipment->serviceability_status ?: 'Not checked' }}</div>
                                    <div><span class="text-gray-500">Pickup:</span> {{ $shipment->pickup_location_name ?: 'Not selected' }}</div>
                                    <div><span class="text-gray-500">Label:</span> {{ $shipment->label_path ? 'Generated' : 'Not generated' }}</div>
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                                    <div class="text-xs font-bold text-gray-500 uppercase mb-2">Package Details</div>
                                    @forelse($shipment->packages as $package)
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            Package {{ $package->package_number }}:
                                            {{ $package->weight_kg }} kg,
                                            {{ $package->length_cm }} x {{ $package->width_cm }} x {{ $package->height_cm }} cm
                                            (Volumetric {{ $package->volumetric_weight_kg }} kg)
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500">No package rows yet. Future shipment creation must capture packed box details here.</div>
                                    @endforelse
                                </div>

                                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4">
                                    <div class="text-xs font-bold text-gray-500 uppercase mb-2">Shipment Timeline</div>
                                    @forelse($shipment->events->take(5) as $event)
                                        <div class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ optional($event->event_time)->format('d M Y H:i') ?: 'No time' }} -
                                            {{ $event->normalized_status ?: $event->raw_status ?: $event->event_type }}
                                            @if($event->location) · {{ $event->location }} @endif
                                        </div>
                                    @empty
                                        <div class="text-sm text-gray-500">No shipment events yet. Webhook/tracking integration will append immutable events here later.</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-700 p-5 text-sm text-gray-500">
                                No shipment has been created. This section is ready for future serviceability checks, package capture, shipment creation, labels, pickup requests, and tracking events.
                            </div>
                        @endforelse
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
                        @if($order->payment_provider)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Payment Provider</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($order->payment_provider) }}</div>
                        </div>
                        @endif
                        @if($order->payment_transaction_id)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Transaction ID</label>
                            <div class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $order->payment_transaction_id }}</div>
                        </div>
                        @endif
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Order ID</label>
                            <div class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $order->id }}</div>
                        </div>
                        @if($order->checkout_status)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Checkout Status</label>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->checkout_status }}</div>
                        </div>
                        @endif
                    </div>
                </x-admin.card>

                @if($order->shipping_plan || $order->rto_prediction || $order->estimated_delivery_date || $order->coupon_codes)
                <x-admin.card title="Checkout Details">
                    <div class="mt-4 space-y-4">
                        @if($order->shipping_plan)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Shipping Plan</label>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $order->shipping_plan }}</div>
                        </div>
                        @endif
                        @if($order->estimated_delivery_date)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Estimated Delivery</label>
                            <div class="text-sm text-gray-900 dark:text-white">{{ $order->estimated_delivery_date->format('d M, Y') }}</div>
                        </div>
                        @endif
                        @if($order->rto_prediction)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">RTO Prediction</label>
                            <div class="text-sm text-gray-900 dark:text-white">{{ ucfirst($order->rto_prediction) }}</div>
                        </div>
                        @endif
                        @if($order->coupon_codes)
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Coupons</label>
                            <div class="text-sm text-gray-900 dark:text-white">{{ implode(', ', $order->coupon_codes) }}</div>
                        </div>
                        @endif
                    </div>
                </x-admin.card>
                @endif

                @if($order->notes)
                <x-admin.card title="Order Notes">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-4">{{ $order->notes }}</p>
                </x-admin.card>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
