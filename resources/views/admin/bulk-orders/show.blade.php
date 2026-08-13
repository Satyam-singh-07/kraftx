<x-layouts.admin>
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between gap-4">
            <div>
                <a href="{{ route('admin.bulk-orders.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">Back to bulk inquiries</a>
                <h2 class="mt-2 text-xl font-bold text-gray-800 dark:text-white">Bulk Order Inquiry</h2>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700">{{ ucfirst($inquiry->status) }}</span>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-admin.card title="Customer">
                <dl class="mt-4 space-y-4 text-sm">
                    <div><dt class="text-xs font-bold uppercase text-gray-500">Name</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $inquiry->name }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-gray-500">Phone</dt><dd class="mt-1"><a href="tel:{{ $inquiry->phone }}" class="text-blue-600">{{ $inquiry->phone }}</a></dd></div>
                    <div><dt class="text-xs font-bold uppercase text-gray-500">Email</dt><dd class="mt-1"><a href="mailto:{{ $inquiry->email }}" class="text-blue-600">{{ $inquiry->email }}</a></dd></div>
                </dl>
            </x-admin.card>
            <x-admin.card title="Requested Product">
                <dl class="mt-4 space-y-4 text-sm">
                    <div><dt class="text-xs font-bold uppercase text-gray-500">Product</dt><dd class="mt-1 font-semibold text-gray-900 dark:text-white">{{ $inquiry->product_name }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-gray-500">SKU</dt><dd class="mt-1 font-mono text-gray-700 dark:text-gray-300">{{ $inquiry->product_sku ?: 'N/A' }}</dd></div>
                    <div><dt class="text-xs font-bold uppercase text-gray-500">Quantity</dt><dd class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ number_format($inquiry->quantity) }}</dd></div>
                </dl>
                @if($inquiry->product_url)
                    <a href="{{ $inquiry->product_url }}" target="_blank" class="mt-5 inline-block text-sm font-semibold text-blue-600">Open product page</a>
                @endif
            </x-admin.card>
        </div>

        <x-admin.card title="Customer message">
            <p class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700 dark:text-gray-300">{{ $inquiry->message ?: 'No additional message provided.' }}</p>
        </x-admin.card>

        <x-admin.card title="Manage inquiry">
            <form method="POST" action="{{ route('admin.bulk-orders.status', $inquiry) }}" class="mt-4 flex flex-col gap-3 sm:flex-row">
                @csrf
                @method('PATCH')
                <select name="status" class="rounded-lg border border-gray-300 px-4 py-2 text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @foreach(['new', 'contacted', 'quoted', 'converted', 'closed'] as $status)
                        <option value="{{ $status }}" @selected($inquiry->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-bold text-white hover:bg-blue-700">Update status</button>
            </form>
            <form method="POST" action="{{ route('admin.bulk-orders.destroy', $inquiry) }}" class="mt-4" onsubmit="return confirm('Delete this bulk inquiry?')">
                @csrf
                @method('DELETE')
                <button class="text-sm font-semibold text-red-600 hover:text-red-800">Delete inquiry</button>
            </form>
        </x-admin.card>
    </div>
</x-layouts.admin>
