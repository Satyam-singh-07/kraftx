<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{
        sidebarOpen: window.innerWidth > 1024,
        darkMode: localStorage.getItem('dark') === 'true'
      }"
      x-init="$watch('sidebarOpen', val => { if (window.innerWidth <= 1024 && val) document.body.classList.add('overflow-hidden'); else document.body.classList.remove('overflow-hidden') })"
      @resize.window="if (window.innerWidth > 1024) sidebarOpen = true"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel Admin') }} - Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <x-admin.sidebar />

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Navbar -->
            <x-admin.navbar />

            <!-- Main Content -->
            <main class="flex-grow p-6">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="p-6 text-center text-sm text-gray-500 border-t border-gray-200 dark:border-gray-800">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </footer>
        </div>
    </div>

    <!-- Toast Notifications -->
    <x-admin.toasts />

    <script>
        // Observe dark mode changes
        window.addEventListener('dark-mode-changed', (e) => {
            localStorage.setItem('dark', e.detail);
        });
    </script>
    @stack('scripts')
</body>
</html>
