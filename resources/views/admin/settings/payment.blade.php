<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Payment Strategy</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Control COD availability, handling fees, and prepaid incentives.</p>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.payment.update') }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <x-admin.card title="Cash on Delivery">
                <div class="space-y-5">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="cod_enabled" value="1" class="mt-1 rounded border-gray-300" @checked(old('cod_enabled', $settings['cod_enabled']))>
                        <span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-100">Enable COD</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">Customers can place Cash on Delivery orders when enabled.</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="cod_fee_enabled" value="1" class="mt-1 rounded border-gray-300" @checked(old('cod_fee_enabled', $settings['cod_fee_enabled']))>
                        <span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-100">Add Cash Handling Fee</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">Shown as a convenience fee in checkout and order summaries.</span>
                        </span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="cod_fee_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cash Handling Fee</label>
                            <input id="cod_fee_amount" name="cod_fee_amount" type="number" min="0" step="0.01" value="{{ old('cod_fee_amount', $settings['cod_fee_amount']) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @error('cod_fee_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="cod_free_above" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Waive COD Fee Above</label>
                            <input id="cod_free_above" name="cod_free_above" type="number" min="0" step="0.01" value="{{ old('cod_free_above', $settings['cod_free_above']) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            <p class="text-xs text-gray-500 mt-1">Use 0 to never auto-waive.</p>
                            @error('cod_free_above') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="Prepaid Incentives">
                <div class="space-y-5">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="prepaid_discount_enabled" value="1" class="mt-1 rounded border-gray-300" @checked(old('prepaid_discount_enabled', $settings['prepaid_discount_enabled']))>
                        <span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-100">Enable prepaid discount</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">Encourage online payment with a clear savings message.</span>
                        </span>
                    </label>

                    <div>
                        <label for="prepaid_discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prepaid Discount Amount</label>
                        <input id="prepaid_discount_amount" name="prepaid_discount_amount" type="number" min="0" step="0.01" value="{{ old('prepaid_discount_amount', $settings['prepaid_discount_amount']) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @error('prepaid_discount_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="prepaid_free_shipping" value="1" class="mt-1 rounded border-gray-300" @checked(old('prepaid_free_shipping', $settings['prepaid_free_shipping']))>
                        <span>
                            <span class="block font-semibold text-gray-800 dark:text-gray-100">Free shipping on prepaid orders</span>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">When base shipping is added later, prepaid orders can automatically waive it.</span>
                        </span>
                    </label>
                </div>
            </x-admin.card>

            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Save Settings</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
