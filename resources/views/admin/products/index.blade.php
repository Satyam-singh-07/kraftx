<x-layouts.admin>
    <div class="space-y-6" x-data="productListing()">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Products</h2>
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                Add New Product
            </a>
        </div>

        <!-- Filters & Search -->
        <x-admin.card>
            <form action="{{ route('admin.products.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or SKU..." 
                           class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Status</label>
                    <select name="status" 
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sort By</label>
                    <select name="sort" 
                            class="block w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all text-sm">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest First</option>
                        <option value="price_low_high" {{ request('sort') === 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_high_low" {{ request('sort') === 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                    </select>
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-bold text-sm shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Reset
                    </a>
                </div>
            </form>
        </x-admin.card>

        <!-- Bulk Actions -->
        <div class="flex items-center space-x-2" x-show="selected.length > 0" x-cloak>
            <span class="text-sm text-gray-600 dark:text-gray-400"><span x-text="selected.length"></span> selected</span>
            <button @click="bulkDelete()" class="px-3 py-1 text-sm bg-red-100 text-red-600 rounded hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors">
                Delete Selected
            </button>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 w-10">
                            <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Product</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Price</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Stock</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $product->id }}" x-model="selected" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="px-6 py-4 flex items-center space-x-3">
                            @if($product->images->count() > 0)
                                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded object-cover">
                            @else
                                <div class="w-10 h-10 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-400">No Img</div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-semibold">
                            @if($product->sale_price)
                                <span class="text-red-500">₹{{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-gray-400 line-through text-xs ml-1">₹{{ number_format($product->price, 2) }}</span>
                            @else
                                ₹{{ number_format($product->price, 2) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $product->stock }}</td>
                        <td class="px-6 py-4">
                            <button @click="toggleStatus({{ $product->id }})" class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 {{ $product->status ? 'bg-blue-600' : 'bg-gray-200' }}" role="switch" aria-checked="true">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $product->status ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <td class="px-6 py-4 space-x-2 flex items-center">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <button @click="deleteProduct({{ $product->id }})" class="text-red-500 hover:text-red-700">Delete</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productListing', () => ({
                selected: [],
                toggleAll(e) {
                    if (e.target.checked) {
                        this.selected = Array.from(document.querySelectorAll('tbody input[type="checkbox"]')).map(el => el.value);
                    } else {
                        this.selected = [];
                    }
                },
                async toggleStatus(id) {
                    try {
                        let res = await axios.post(`/admin/products/${id}/toggle-status`, {}, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', text: 'Status updated!' } }));
                            setTimeout(() => window.location.reload(), 500);
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', text: 'Failed to update status.' } }));
                    }
                },
                async deleteProduct(id) {
                    if (!confirm('Are you sure you want to delete this product?')) return;
                    try {
                        let res = await axios.delete(`/admin/products/${id}`, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', text: 'Product deleted!' } }));
                            setTimeout(() => window.location.reload(), 500);
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', text: 'Failed to delete.' } }));
                    }
                },
                async bulkDelete() {
                    if (!confirm(`Are you sure you want to delete ${this.selected.length} products?`)) return;
                    try {
                        let res = await axios.post(`/admin/products/bulk-delete`, { ids: this.selected }, {
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (res.data.success) {
                            window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'success', text: 'Products deleted!' } }));
                            setTimeout(() => window.location.reload(), 500);
                        }
                    } catch (e) {
                        window.dispatchEvent(new CustomEvent('notify', { detail: { type: 'error', text: 'Failed to bulk delete.' } }));
                    }
                }
            }));
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @endpush
</x-layouts.admin>
