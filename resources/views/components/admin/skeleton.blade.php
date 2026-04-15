@props(['type' => 'card'])

@if($type === 'card')
<div class="animate-pulse bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between">
        <div class="space-y-3 flex-1">
            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
            <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/4 mt-4"></div>
        </div>
        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
    </div>
</div>
@endif

@if($type === 'table')
<div class="animate-pulse space-y-4">
    <div class="h-10 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
    <div class="h-16 bg-gray-300 dark:bg-gray-600 rounded w-full"></div>
    <div class="h-16 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
    <div class="h-16 bg-gray-300 dark:bg-gray-600 rounded w-full"></div>
</div>
@endif

@if($type === 'chart')
<div class="animate-pulse bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="h-6 bg-gray-200 dark:bg-gray-700 rounded w-1/3 mb-6"></div>
    <div class="h-48 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
</div>
@endif
