<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - Payments - HRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 ease-in-out">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">HRM</h1>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Employees</span>
                </a>
                <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Teams</span>
                </a>
                <a href="{{ route('loans.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Loans</span>
                </a>
                <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 px-4 py-3 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Payments</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <button id="profile-menu-btn" class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ auth()->user()->mobile }}</p>
                        </div>
                    </button>

                    <div id="profile-dropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Payments - Employees</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">View employee financial details and payment history</p>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <a href="{{ route('payments.salaries') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Salaries
                            </a>
                            <a href="{{ route('payments.index') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Loans
                            </a>
                            <a href="{{ route('payments.transactions') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Transactions
                            </a>
                            <a href="{{ route('payments.employees') }}" class="px-3 py-2 border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-medium">
                                Employees
                            </a>
                        </nav>
                    </div>

                    @if($employees->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($employees as $employee)
                        <a href="{{ route('payments.employee.detail', $employee->id) }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200 dark:border-gray-700 hover:border-blue-500 dark:hover:border-blue-400">
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full text-white font-semibold text-lg md:text-xl">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Team:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            {{ $employee->team ? $employee->team->name : 'No Team' }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                        @if($employee->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                            Active
                                        </span>
                                        @elseif($employee->status === 'inactive')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">
                                            Inactive
                                        </span>
                                        @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                            Resigned
                                        </span>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">Salary:</span>
                                        <span class="font-medium text-gray-900 dark:text-white">
                                            ₹{{ number_format($employee->salary ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Add employees to get started.</p>
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        
        const savedState = localStorage.getItem('sidebarExpanded');
        if (savedState === 'false') {
            sidebar.classList.add('collapsed');
        }
        
        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            const isExpanded = !sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarExpanded', isExpanded);
        }
        
        sidebarToggle?.addEventListener('click', toggleSidebar);
        sidebarClose?.addEventListener('click', toggleSidebar);

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
    </script>
</body>
</html>
