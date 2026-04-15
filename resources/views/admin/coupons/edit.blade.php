<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Coupon: {{ $coupon->code }}</h2>
            <a href="{{ route('admin.coupons.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Back</a>
        </div>

        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <x-admin.card title="Coupon Details">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                        <div class="flex">
                            <input type="text" name="code" id="coupon_code" required value="{{ old('code', $coupon->code) }}" placeholder="e.g. SUMMER50"
                                   class="block w-full px-4 py-2.5 rounded-l-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white font-mono uppercase focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            <button type="button" onclick="document.getElementById('coupon_code').value = Math.random().toString(36).substring(2, 10).toUpperCase()" class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 rounded-r-lg border-y border-r border-gray-300 dark:border-gray-600 font-semibold hover:bg-gray-200 transition-colors">Generate</button>
                        </div>
                        @error('code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Discount Type <span class="text-red-500">*</span></label>
                            <select name="discount_type" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                                <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Discount Value <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="discount_value" required value="{{ old('discount_value', $coupon->discount_value) }}" placeholder="e.g. 15"
                                   class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                            @error('discount_value') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Usage Restrictions">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Minimum Cart Value ($)</label>
                        <input type="number" step="0.01" name="min_cart_value" value="{{ old('min_cart_value', $coupon->min_cart_value) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        <p class="text-[10px] text-gray-500 mt-1">Leave 0 for no minimum requirement.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Maximum Discount ($)</label>
                        <input type="number" step="0.01" name="max_discount" value="{{ old('max_discount', $coupon->max_discount) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        <p class="text-[10px] text-gray-500 mt-1">Useful for capping percentage discounts.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Usage Limit</label>
                        <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="e.g. 100"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        <p class="text-[10px] text-gray-500 mt-1">Total times this coupon can be used across the store. Current usage: {{ $coupon->used_count }}</p>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Schedule & Status">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Start Date & Time</label>
                        <input type="datetime-local" name="start_date" value="{{ old('start_date', $coupon->start_date?->format('Y-m-d\TH:i')) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">End Date & Time</label>
                        <input type="datetime-local" name="end_date" value="{{ old('end_date', $coupon->end_date?->format('Y-m-d\TH:i')) }}"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center space-x-3">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', $coupon->status) ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Coupon is Active</label>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Update Coupon
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
