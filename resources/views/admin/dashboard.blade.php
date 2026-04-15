<x-layouts.admin>
    <div class="space-y-6">
        <!-- Actions Demo -->
        <div class="flex space-x-4">
            <button @click="$dispatch('notify', { type: 'success', text: 'Settings saved successfully!' })" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                Test Success Toast
            </button>
            <button @click="location.reload()" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-white rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                Refresh Data
            </button>
        </div>

        <x-admin.modal name="sample-modal" title="Quick Product Edit">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                    <input type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" value="Sample Product">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input type="number" class="block w-full pl-7 pr-12 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="0.00">
                    </div>
                </div>
            </div>
            <x-slot:footer>
                <button @click="$dispatch('close-modal', 'sample-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-500 transition-colors">Cancel</button>
                <button @click="$dispatch('close-modal', 'sample-modal'); $dispatch('notify', { type: 'success', text: 'Product updated!' })" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">Save Changes</button>
            </x-slot:footer>
        </x-admin.modal>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-admin.card
                title="Total Orders"
                value="{{ $stats['total_orders'] }}"
                icon="shopping-cart"
                color="blue"
                trend="+12%"
            />
            <x-admin.card
                title="Total Revenue"
                value="${{ number_format($stats['total_revenue'], 2) }}"
                icon="shopping-bag"
                color="green"
                trend="+8.5%"
            />
            <x-admin.card
                title="Total Products"
                value="{{ $stats['total_products'] }}"
                icon="shopping-bag"
                color="yellow"
                trend="+3"
            />
            <x-admin.card
                title="Total Customers"
                value="{{ $stats['total_customers'] }}"
                icon="users"
                color="purple"
                trend="+45"
            />
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-admin.card title="Sales Overview (Last 7 Days)">
                <div class="h-64 mt-4">
                    <canvas id="salesChart"></canvas>
                </div>
            </x-admin.card>

            <x-admin.card title="Orders Trend (Last 7 Days)">
                <div class="h-64 mt-4">
                    <canvas id="ordersChart"></canvas>
                </div>
            </x-admin.card>
        </div>

        <!-- Recent Orders Table -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Recent Orders</h3>
                <a href="#" class="text-blue-600 hover:text-blue-700 font-medium text-sm">View all orders</a>
            </div>

            <x-admin.table :headers="['Order ID', 'Customer', 'Amount', 'Status', 'Date', 'Action']">
                @foreach($stats['recent_orders'] as $order)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $order['id'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $order['customer'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">${{ number_format($order['amount'], 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $order['status'] === 'Completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' :
                                   ($order['status'] === 'Pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' :
                                   ($order['status'] === 'Shipped' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' :
                                   'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400')) }}">
                                {{ $order['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $order['date'] }}</td>
                        <td class="px-6 py-4">
                            <button class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($stats['sales_data']['labels']) !!},
                    datasets: [{
                        label: 'Revenue ($)',
                        data: {!! json_encode($stats['sales_data']['data']) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Orders Chart
            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
            new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($stats['orders_data']['labels']) !!},
                    datasets: [{
                        label: 'Orders',
                        data: {!! json_encode($stats['orders_data']['data']) !!},
                        backgroundColor: '#7c3aed',
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        });
    </script>
    @endpush
</x-layouts.admin>
