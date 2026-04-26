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
    class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 lg:static lg:translate-x-0 flex flex-col"
    :class="{
        'w-64 translate-x-0': sidebarOpen,
        'w-20 -translate-x-full lg:translate-x-0': !sidebarOpen
    }"
>
    <!-- Logo Section -->
    <div class="flex items-center h-16 px-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shrink-0"
         :class="sidebarOpen ? 'justify-between' : 'justify-center px-0'">
        <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="text-white font-bold text-lg">K</span>
            </div>
            <span class="text-xl font-bold dark:text-white truncate" x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">KraftX</span>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" x-show="sidebarOpen">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 custom-scrollbar">
        @php
            $groups = [
                'Main' => [
                    ['label' => 'Dashboard', 'icon' => 'home', 'route' => 'admin.dashboard'],
                ],
                'Catalog' => [
                    ['label' => 'Products', 'icon' => 'shopping-bag', 'route' => 'admin.products.index'],
                    ['label' => 'Collections', 'icon' => 'layers', 'route' => 'admin.collections.index'],
                    ['label' => 'Tags', 'icon' => 'tag', 'route' => 'admin.tags.index'],
                ],
                'Sales' => [
                    ['label' => 'Orders', 'icon' => 'shopping-cart', 'route' => 'admin.orders.index'],
                    ['label' => 'Customers', 'icon' => 'users', 'route' => 'admin.customers.index'],
                ],
                'Marketing' => [
                    ['label' => 'Deals', 'icon' => 'tag', 'route' => 'admin.deals.index'],
                    ['label' => 'Coupons', 'icon' => 'ticket', 'route' => 'admin.coupons.index'],
                    ['label' => 'Banners', 'icon' => 'image', 'route' => 'admin.banners.index'],
                    ['label' => 'Reels', 'icon' => 'video', 'route' => 'admin.reels.index'],
                ],
                'Communication' => [
                    ['label' => 'Reviews', 'icon' => 'star', 'route' => 'admin.reviews.index'],
                    ['label' => 'Messages', 'icon' => 'mail', 'route' => 'admin.contact-messages.index'],
                    ['label' => 'Newsletters', 'icon' => 'mail', 'route' => 'admin.newsletters.index'],
                ],
            ];
        @endphp

        @foreach($groups as $groupName => $items)
            <div class="mb-6">
                <p x-show="sidebarOpen" class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">{{ $groupName }}</p>
                <div x-show="!sidebarOpen" class="border-t border-gray-100 dark:border-gray-700 my-4 mx-2"></div>
                <div class="space-y-1">
                    @foreach($items as $menu)
                        <a href="{{ Route::has($menu['route']) ? route($menu['route']) : '#' }}"
                           class="flex items-center px-4 py-2.5 rounded-lg transition-colors group {{ request()->routeIs($menu['route']) ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                           :class="sidebarOpen ? '' : 'justify-center px-2'"
                           title="{{ $menu['label'] }}">
                            <span class="flex items-center justify-center shrink-0">
                                @include('components.admin.icons.' . $menu['icon'])
                            </span>
                            <span class="ml-3 font-medium text-sm" x-show="sidebarOpen">{{ $menu['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Content Group -->
        <div class="mb-6">
            <p x-show="sidebarOpen" class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Content</p>
            <div x-show="!sidebarOpen" class="border-t border-gray-100 dark:border-gray-700 my-4 mx-2"></div>
            <div x-data="{ open: {{ request()->routeIs('admin.blog-*') ? 'true' : 'false' }} }">
                <button @click="open = !open" 
                   class="flex items-center w-full px-4 py-2.5 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group"
                   :class="sidebarOpen ? '' : 'justify-center px-2'"
                   title="Blog">
                    <span class="flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </span>
                    <span class="ml-3 font-medium text-sm flex-1 text-left" x-show="sidebarOpen">Blog</span>
                    <svg x-show="sidebarOpen" class="w-4 h-4 transition-transform shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <div x-show="open && sidebarOpen" x-transition class="ml-9 space-y-1 mt-1">
                    <a href="{{ route('admin.blog-categories.index') }}" class="block px-4 py-2 text-xs {{ request()->routeIs('admin.blog-categories.*') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">Categories</a>
                    <a href="{{ route('admin.blog-posts.index') }}" class="block px-4 py-2 text-xs {{ request()->routeIs('admin.blog-posts.*') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">Posts</a>
                    <a href="{{ route('admin.blog-comments.index') }}" class="block px-4 py-2 text-xs {{ request()->routeIs('admin.blog-comments.*') ? 'text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">Comments</a>
                </div>
            </div>
        </div>

        <!-- System Group -->
        <div class="mb-6">
            <p x-show="sidebarOpen" class="px-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">System</p>
            <div x-show="!sidebarOpen" class="border-t border-gray-100 dark:border-gray-700 my-4 mx-2"></div>
            <div class="space-y-1">
                <a href="{{ route('admin.settings.index') }}"
                   class="flex items-center px-4 py-2.5 rounded-lg transition-colors group {{ request()->routeIs('admin.settings.index') ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}"
                   :class="sidebarOpen ? '' : 'justify-center px-2'"
                   title="Settings">
                    <span class="flex items-center justify-center shrink-0">
                        @include('components.admin.icons.settings')
                    </span>
                    <span class="ml-3 font-medium text-sm" x-show="sidebarOpen">Settings</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Logout Section -->
    <div class="p-2 border-t border-gray-200 dark:border-gray-700 shrink-0">
        <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">@csrf</form>
        <button onclick="document.getElementById('logout-form').submit()"
           class="flex items-center w-full px-4 py-3 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors group"
           :class="sidebarOpen ? '' : 'justify-center px-2'"
           title="Logout">
            <span class="flex items-center justify-center shrink-0">
                @include('components.admin.icons.logout')
            </span>
            <span class="ml-3 font-medium text-sm" x-show="sidebarOpen">Logout</span>
        </button>
    </div>
</aside>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.2);
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: rgba(156, 163, 175, 0.4);
    }
</style>
