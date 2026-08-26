<x-layouts.admin>
    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">Bulk update result</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Complete processing details for the uploaded workbook.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Back to products</a>
        </div>

        <div class="rounded-xl border border-green-200 bg-green-50 p-5 text-green-800 dark:border-green-900/50 dark:bg-green-900/20 dark:text-green-200">
            <h3 class="font-semibold">Success</h3>
            <p class="mt-1">{{ $result['updated'] }} product(s) updated successfully.</p>
        </div>

        @if(!empty($result['errors']))
            <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-red-800 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-200">
                <h3 class="font-semibold">Errors ({{ count($result['errors']) }})</h3>
                <ul class="mt-3 max-h-[32rem] list-disc space-y-2 overflow-y-auto pl-5 text-sm">
                    @foreach($result['errors'] as $error)
                        <li class="break-words font-mono">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="rounded-xl border border-gray-200 bg-white p-5 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">No errors were reported.</div>
        @endif
    </div>
</x-layouts.admin>
