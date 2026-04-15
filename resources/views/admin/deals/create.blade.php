<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Create Deal</h2>
            <a href="{{ route('admin.deals.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Back</a>
        </div>

        <form action="{{ route('admin.deals.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
            @csrf
            
            <x-admin.card title="Deal Setup">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deal Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title') }}" placeholder="e.g. Summer Flash Sale"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Brief description..."
                                  class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Discount Type <span class="text-red-500">*</span></label>
                            <select name="discount_type" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Discount Value <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="discount_value" required value="{{ old('discount_value') }}" placeholder="e.g. 20"
                                   class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            @error('discount_value') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Schedule & Scope">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date') }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Date & Time</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date') }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Target Products</label>
                        <select name="product_ids[]" multiple size="6"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-[10px] text-gray-500 mt-1">Apply this deal to specific products.</p>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Advanced Settings">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Priority Level</label>
                        <input type="number" name="priority" value="{{ old('priority', 0) }}" placeholder="e.g. 10"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        <p class="text-[10px] text-gray-500 mt-1">Higher number means higher priority if deals conflict.</p>
                    </div>
                    
                    <div class="flex flex-col justify-center space-y-4 pt-4">
                        <div class="flex items-center space-x-3">
                            <input type="hidden" name="status" value="0">
                            <input type="checkbox" name="status" id="status" value="1" {{ old('status', '1') == '1' ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Deal is Active</label>
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Save Deal
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
