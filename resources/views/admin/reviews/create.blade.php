<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Add New Review</h2>
            <a href="{{ route('admin.reviews.index') }}" class="text-sm text-blue-600 hover:underline">Back to Reviews</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form action="{{ route('admin.reviews.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="product_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                        <select name="product_id" id="product_id" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('product_id') border-red-500 @enderror">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="rating" class="text-sm font-medium text-gray-700 dark:text-gray-300">Rating (1-5)</label>
                        <select name="rating" id="rating" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('rating') border-red-500 @enderror">
                            @foreach(range(5, 1) as $rate)
                                <option value="{{ $rate }}" {{ old('rating') == $rate ? 'selected' : '' }}>{{ $rate }} Stars</option>
                            @endforeach
                        </select>
                        @error('rating') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Reviewer Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email (Optional)</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="comment" class="text-sm font-medium text-gray-700 dark:text-gray-300">Comment</label>
                    <textarea name="comment" id="comment" rows="4" required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('comment') border-red-500 @enderror">{{ old('comment') }}</textarea>
                    @error('comment') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1">
                    <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="space-y-1">
                    <label for="images" class="text-sm font-medium text-gray-700 dark:text-gray-300">Images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">You can select multiple images.</p>
                </div>

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Create Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
