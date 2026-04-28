<x-layouts.admin>
    <div class="space-y-6" x-data="inventoryManager()">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Inventory Management</h2>
            <div class="flex items-center space-x-2">
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                    Low Stock Threshold: {{ \App\Http\Controllers\Admin\InventoryController::LOW_STOCK_THRESHOLD }}
                </span>
            </div>
        </div>

        <!-- Filters & Search -->
        <x-admin.card>
            <form action="{{ route('admin.inventory.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search Products/SKUs</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or SKU..." 
                           class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock Status</label>
                    <select name="status" 
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Inventory</option>
                        <option value="low" {{ $status === 'low' ? 'selected' : '' }}>Low Stock Alerts</option>
                        <option value="out" {{ $status === 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-bold text-sm shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </x-admin.card>

        <!-- Inventory Table -->
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Product Info</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Type/Variant</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 text-center">Current Stock</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Quick Update</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($inventory as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3">
                                @if($item['image'])
                                    <img src="{{ $item['image'] }}" class="w-10 h-10 rounded-lg object-cover border border-gray-100 dark:border-gray-600">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-400 text-xs">No Img</div>
                                @endif
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $item['name'] }}</div>
                                    <div class="text-xs text-gray-500 font-mono">SKU: {{ $item['sku'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded text-[10px] font-bold uppercase tracking-tight">
                                {{ $item['label'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $stockClass = 'text-gray-900 dark:text-white';
                                if($item['stock'] <= 0) $stockClass = 'text-red-600 font-bold';
                                elseif($item['stock'] <= \App\Http\Controllers\Admin\InventoryController::LOW_STOCK_THRESHOLD) $stockClass = 'text-orange-600 font-bold';
                            @endphp
                            <span class="text-lg {{ $stockClass }}" id="stock-display-{{ $item['type'] }}-{{ $item['id'] }}">
                                {{ $item['stock'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <input type="number" 
                                       id="input-{{ $item['type'] }}-{{ $item['id'] }}" 
                                       value="{{ $item['stock'] }}" 
                                       class="w-20 px-2 py-1 rounded border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm outline-none focus:ring-1 focus:ring-blue-500">
                                <button @click="updateStock('{{ $item['id'] }}', '{{ $item['type'] }}')" 
                                        class="p-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors shadow-sm"
                                        title="Save Stock">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">
                            ₹{{ number_format($item['price'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                            No inventory items found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function inventoryManager() {
            return {
                async updateStock(id, type) {
                    const input = document.getElementById(`input-${type}-${id}`);
                    const display = document.getElementById(`stock-display-${type}-${id}`);
                    const newStock = input.value;

                    try {
                        const response = await axios.post('{{ route('admin.inventory.update') }}', {
                            id: id,
                            type: type,
                            stock: newStock
                        });

                        if (response.data.success) {
                            // Update display with feedback
                            display.textContent = newStock;
                            
                            // Highlight update
                            display.classList.add('scale-125', 'transition-transform');
                            setTimeout(() => display.classList.remove('scale-125'), 300);

                            // Trigger dynamic alert (assuming you have a notification system)
                            window.dispatchEvent(new CustomEvent('notify', { 
                                detail: { type: 'success', text: response.data.message } 
                            }));

                            // Update stock color based on threshold
                            const threshold = {{ \App\Http\Controllers\Admin\InventoryController::LOW_STOCK_THRESHOLD }};
                            display.classList.remove('text-red-600', 'text-orange-600', 'text-gray-900', 'dark:text-white', 'font-bold');
                            
                            if (newStock <= 0) {
                                display.classList.add('text-red-600', 'font-bold');
                            } else if (newStock <= threshold) {
                                display.classList.add('text-orange-600', 'font-bold');
                            } else {
                                display.classList.add('text-gray-900', 'dark:text-white');
                            }
                        }
                    } catch (error) {
                        alert('Error updating stock: ' + (error.response?.data?.message || error.message));
                    }
                }
            }
        }
    </script>
    @endpush
</x-layouts.admin>
