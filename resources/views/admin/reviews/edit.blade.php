<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Review</h2>
            <a href="{{ route('admin.reviews.index') }}" class="text-sm text-blue-600 hover:underline">Back to Reviews</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form action="{{ route('admin.reviews.update', $review->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="product_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                        <select name="product_id" id="product_id" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('product_id') border-red-500 @enderror">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $review->product_id) == $product->id ? 'selected' : '' }}>
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
                                <option value="{{ $rate }}" {{ old('rating', $review->rating) == $rate ? 'selected' : '' }}>{{ $rate }} Stars</option>
                            @endforeach
                        </select>
                        @error('rating') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="name" class="text-sm font-medium text-gray-700 dark:text-gray-300">Reviewer Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $review->name) }}" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1">
                        <label for="email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Email (Optional)</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $review->email) }}"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('email') border-red-500 @enderror">
                        @error('email') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="comment" class="text-sm font-medium text-gray-700 dark:text-gray-300">Comment</label>
                    <textarea name="comment" id="comment" rows="4" required
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none @error('comment') border-red-500 @enderror">{{ old('comment', $review->comment) }}</textarea>
                    @error('comment') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" id="status" required
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <option value="approved" {{ old('status', $review->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="pending" {{ old('status', $review->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ old('status', $review->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2 pt-6">
                        <input type="checkbox" name="show_on_home" id="show_on_home" value="1" {{ old('show_on_home', $review->show_on_home) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-900 dark:border-gray-600">
                        <label for="show_on_home" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show on Home Page</label>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="images" class="text-sm font-medium text-gray-700 dark:text-gray-300">Add Images</label>
                    <input type="file" name="images[]" id="images" multiple accept="image/*"
                        class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Existing images will be kept. You can add more.</p>
                </div>

                @if(!empty($review->images))
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Current Images</p>
                        <div class="flex flex-wrap gap-4">
                            @foreach($review->images as $imagePath)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $imagePath) }}" alt="Review image"
                                        class="w-24 h-24 rounded-lg object-cover border border-gray-200 dark:border-gray-700">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                        Update Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
