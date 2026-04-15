@props(['title' => null, 'value' => null, 'icon' => null, 'color' => 'blue', 'trend' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-all duration-200 hover:shadow-md']) }}>
    <div class="flex items-center justify-between">
        <div>
            @if($title)
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $title }}</p>
            @endif
            @if($value)
                <h3 class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ $value }}</h3>
            @endif

            @if($trend)
                <div class="mt-2 flex items-center text-sm {{ str_starts_with($trend, '+') ? 'text-green-600' : 'text-red-600' }}">
                    <span class="font-semibold">{{ $trend }}</span>
                    <span class="ml-1 text-gray-500 dark:text-gray-400 italic text-xs">from last week</span>
                </div>
            @endif
        </div>

        @if($icon)
            @php
                $colorClasses = [
                    'blue' => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
                    'green' => 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                    'yellow' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'purple' => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
                ];
                $currentColor = $colorClasses[$color] ?? $colorClasses['blue'];
            @endphp
            <div class="p-3 rounded-lg {{ $currentColor }}">
                @include('components.admin.icons.' . $icon)
            </div>
        @endif
    </div>

    {{ $slot }}
</div>
