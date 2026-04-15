<!-- Mobile Overlay -->
<div
    x-show="sidebarOpen && window.innerWidth < 1024"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
    x-cloak
></div>

<aside
    class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 lg:static lg:translate-x-0"
    :class="{
        'w-64 translate-x-0': sidebarOpen,
        'w-20 -translate-x-full lg:translate-x-0': !sidebarOpen
    }"
>
    <!-- Logo Section -->
    <div class="flex items-center justify-between h-16 px-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-lg">K</span>
            </div>
            <span class="text-xl font-bold dark:text-white truncate" x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">KraftX</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-4 px-2 space-y-2">
        @php
            $menus = [
                ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'admin.dashboard'],
                ['label' => 'Products', 'icon' => 'shopping-bag', 'route' => 'admin.products.index'],
                ['label' => 'Collections', 'icon' => 'layers', 'route' => 'admin.collections.index'],
                ['label' => 'Deals', 'icon' => 'tag', 'route' => 'admin.deals.index'],
                ['label' => 'Coupons', 'icon' => 'ticket', 'route' => 'admin.coupons.index'],
                ['label' => 'Banners', 'icon' => 'image', 'route' => 'admin.banners.index'],
                ['label' => 'Reels', 'icon' => 'video', 'route' => 'admin.reels.index'],
                ['label' => 'Orders', 'icon' => 'shopping-cart', 'route' => 'admin.orders.index'],
                ['label' => 'Customers', 'icon' => 'users', 'route' => 'admin.customers.index'],
                ['label' => 'Settings', 'icon' => 'settings', 'route' => 'admin.settings.index'],
            ];
        @endphp

        @foreach($menus as $menu)
            <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors group {{ request()->routeIs($menu['route']) ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
               :title="!sidebarOpen ? '{{ $menu['label'] }}' : ''">
                <span class="flex items-center justify-center">
                    @include('components.admin.icons.' . $menu['icon'])
                </span>
                <span class="ml-4 font-medium" x-show="sidebarOpen">{{ $menu['label'] }}</span>
            </a>
        @endforeach

        <!-- Logout -->
        <div class="absolute bottom-4 left-0 right-0 px-2">
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
            <button onclick="document.getElementById('logout-form').submit()"
               class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                <span class="flex items-center justify-center">
                    @include('components.admin.icons.logout')
                </span>
                <span class="ml-4 font-medium" x-show="sidebarOpen">Logout</span>
            </button>
        </div>
    </nav>
</aside>
