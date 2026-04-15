<x-layouts.admin>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Edit Customer: {{ $customer->name }}</h2>
            <a href="{{ route('admin.customers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 font-medium">Back</a>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <x-admin.card title="Account Information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name', $customer->name) }}" placeholder="John Doe"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="{{ old('email', $customer->email) }}" placeholder="john@example.com"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="+1 234 567 890"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('phone') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Leave password fields blank to keep the current password.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                        <input type="password" name="password"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation"
                               class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Additional Details">
                <div class="space-y-5 mt-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Shipping Address</label>
                        <textarea name="address" rows="3" placeholder="Enter primary shipping address..."
                                  class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all outline-none">{{ old('address', $customer->address) }}</textarea>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <input type="hidden" name="status" value="0">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', $customer->status) == '1' ? 'checked' : '' }}
                               class="w-5 h-5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="status" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active Account</label>
                    </div>
                </div>
            </x-admin.card>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all shadow-md hover:shadow-lg font-bold">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
