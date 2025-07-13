<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    <!-- ADMIN LAYOUT LOADED - CALENDAR SHOULD SHOW ADMIN NAVBAR -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Admin Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-900">
                            Laravel Admin
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.booking.calendar') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.booking.calendar*') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium transition duration-150 ease-in-out">
                            Calendar
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="hidden sm:block text-sm text-gray-700">
                        Welcome, <span class="font-medium">{{ Auth::user()->name }}</span>
                    </div>

                    <!-- Public Site Link -->
                    <a href="{{ route('home') }}"
                       class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                        View Site
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium transition duration-150 ease-in-out">
                            Logout
                        </button>
                    </form>

                    <!-- Mobile menu button -->
                    <div class="sm:hidden">
                        <button type="button" onclick="toggleMobileMenu()" class="text-gray-500 hover:text-gray-700 p-2 rounded-md">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="sm:hidden hidden border-t border-gray-200">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.dashboard*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                    Dashboard
                </a>
                <a href="{{ route('admin.booking.calendar') }}"
                   class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('admin.booking.calendar*') ? 'border-indigo-500 text-indigo-700 bg-indigo-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                    Calendar
                </a>
            </div>
            <div class="pt-4 pb-3 border-t border-gray-200">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                    <div class="text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="py-6">
        {{ $slot }}
    </main>

    <!-- Scripts -->
    @livewireScripts
    @stack('scripts')

    <!-- JavaScript functions -->
    <script>
        // Toggle mobile menu
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Auto-hide success/error messages after 5 seconds
            const messages = document.querySelectorAll('[class*="fixed bottom-4 right-4"]');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.opacity = '0';
                    setTimeout(() => {
                        message.remove();
                    }, 300);
                }, 5000);
            });

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const mobileMenu = document.getElementById('mobile-menu');
                const mobileMenuButton = event.target.closest('[onclick="toggleMobileMenu()"]');

                if (!mobileMenuButton && !mobileMenu.contains(event.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
