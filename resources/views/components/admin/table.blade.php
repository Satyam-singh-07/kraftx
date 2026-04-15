@props(['headers' => []])

<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 dark:bg-gray-900/50">
            <tr>
                @foreach($headers as $header)
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
