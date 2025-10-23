<header class="bg-[#1e293b] border-b border-gray-700 h-16 fixed top-0 left-20 right-0 z-30">
    <div class="h-full px-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-xl font-bold text-white">HRM</span>
            @if(isset($breadcrumb))
            <span class="text-gray-400">/</span>
            <span class="text-gray-300">{{ $breadcrumb }}</span>
            @endif
        </div>

        <div class="flex items-center space-x-3">
            <button class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>

            <a href="{{ route('notifications.index') }}" class="relative text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @if(auth()->user()->unreadNotifications()->count() > 0)
                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                @endif
            </a>

            <button id="dark-mode-toggle" class="text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
            </button>

            <button class="bg-[#fbbf24] hover:bg-yellow-500 text-gray-900 p-2 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </button>

            <div class="relative">
                <button id="profile-menu-btn" class="flex items-center space-x-2 text-gray-300 hover:text-white p-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <div class="flex items-center justify-center w-8 h-8 bg-teal-600 rounded-full text-white font-semibold text-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="hidden md:block font-medium">{{ Auth::user()->name }}</span>
                    <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->mobile }}</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-1 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileMenuBtn = document.getElementById('profile-menu-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        if (profileMenuBtn && profileDropdown) {
            profileMenuBtn.addEventListener('click', () => {
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!profileMenuBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const html = document.documentElement;

        if (darkModeToggle) {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                html.classList.add('dark');
            } else {
                html.classList.remove('dark');
            }

            darkModeToggle.addEventListener('click', () => {
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.theme = 'light';
                } else {
                    html.classList.add('dark');
                    localStorage.theme = 'dark';
                }
            });
        }
    });
</script>
