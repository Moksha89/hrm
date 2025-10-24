<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Calendar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden ml-20">
            @include('components.header')

            <div class="bg-white dark:bg-gray-800 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Calendar</h2>
            </div>

            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ $currentDate->format('F Y') }}
                        </h1>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('calendar', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
                               class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </a>
                            <a href="{{ route('calendar') }}" 
                               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                                Today
                            </a>
                            <a href="{{ route('calendar', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
                               class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                            <div class="p-4 text-center font-semibold text-gray-700 dark:text-gray-300 text-sm">
                                {{ $day }}
                            </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-7">
                            @php
                                $dayCounter = 1 - $firstDayOfWeek;
                            @endphp
                            @for($week = 0; $week < 6; $week++)
                                @for($dayOfWeek = 0; $dayOfWeek < 7; $dayOfWeek++)
                                    @php
                                        $day = $dayCounter++;
                                        $isCurrentMonth = $day > 0 && $day <= $daysInMonth;
                                        $isToday = $isCurrentMonth && $day == now()->day && $month == now()->month && $year == now()->year;
                                        $dayEvents = $events->get($day, collect());
                                    @endphp
                                    
                                    <div class="min-h-32 p-2 border-b border-r border-gray-200 dark:border-gray-700 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }}">
                                        @if($isCurrentMonth)
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-sm font-medium {{ $isToday ? 'bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center' : 'text-gray-700 dark:text-gray-300' }}">
                                                    {{ $day }}
                                                </span>
                                            </div>
                                            
                                            @foreach($dayEvents as $event)
                                                <div class="mb-1 px-2 py-1 rounded text-xs {{ $event['color'] == 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : ($event['color'] == 'red' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : ($event['color'] == 'orange' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300')) }}">
                                                    {{ $event['title'] }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                @endfor
                                @if($dayCounter > $daysInMonth)
                                    @break
                                @endif
                            @endfor
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-blue-500 rounded"></div>
                            <span class="text-gray-700 dark:text-gray-300">Birthdays</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-500 rounded"></div>
                            <span class="text-gray-700 dark:text-gray-300">Urgent (1-3 days)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-orange-500 rounded"></div>
                            <span class="text-gray-700 dark:text-gray-300">Medium (7 days)</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                            <span class="text-gray-700 dark:text-gray-300">Early Warning (15-30 days)</span>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');

        function toggleSidebar() {
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-20');
            const isCollapsed = sidebar.classList.contains('w-20');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        mobileSidebarToggle?.addEventListener('click', toggleSidebar);

        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.remove('w-64');
            sidebar.classList.add('w-20');
        }

        const profileMenuBtn = document.getElementById('profile-menu-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        profileMenuBtn?.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!profileMenuBtn?.contains(e.target) && !profileDropdown?.contains(e.target)) {
                profileDropdown?.classList.add('hidden');
            }
        });

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const html = document.documentElement;

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        darkModeToggle?.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>
</html>
