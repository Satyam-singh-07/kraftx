<x-layouts.admin>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Product Reviews</h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                Total: {{ $reviews->total() }}
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name, email, comment..."
                    class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">

                <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Status</option>
                    <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                <select name="product_id" class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ (string) ($filters['product_id'] ?? '') === (string) $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Apply Filters
                </button>
            </form>
        </div>

        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Reviewer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Product</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Rating</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Comment</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Images</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $review->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $review->email }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $review->created_at->format('d M Y, h:i A') }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($review->product)
                                    <a href="{{ route('product.show', $review->product->slug) }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                        {{ $review->product->name }}
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Product deleted</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <div class="text-sm font-bold text-yellow-600 dark:text-yellow-400">
                                    {{ $review->rating }}/5
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <p class="text-sm text-gray-600 dark:text-gray-300 max-w-md">{{ $review->comment }}</p>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if(!empty($review->images))
                                    <div class="flex items-center gap-2">
                                        @foreach($review->images as $imagePath)
                                            <a href="{{ asset('storage/' . $imagePath) }}" target="_blank" rel="noopener noreferrer">
                                                <img src="{{ asset('storage/' . $imagePath) }}" alt="Review image"
                                                    class="w-12 h-12 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500">No images</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 align-top">
                                <form method="POST" action="{{ route('admin.reviews.status', $review->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="text-xs px-2 py-1 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 dark:text-white">
                                        <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ $review->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Are you sure you want to delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reviews->hasPages())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
