<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Payments - HRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden ml-20">
            @include('components.header')

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <a href="{{ route('payments.salaries') }}" class="px-3 py-2 border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-medium">
                                Salaries
                            </a>
                            <a href="{{ route('payments.index') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Loans
                            </a>
                            <a href="{{ route('payments.transactions') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Transactions
                            </a>
                            <a href="{{ route('payments.employees') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Employees
                            </a>
                        </nav>
                    </div>

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Salary Payments - {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}</h3>
                            <a href="{{ route('payments.salary.history') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                View Payment History
                            </a>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Select a team to process salary payments for its members</p>
                        
                        @if($teams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teams as $team)
                            <a href="{{ route('payments.salary.team', $team->id) }}" class="block p-6 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h4>
                                    <span class="px-3 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 rounded-full">
                                        {{ $team->employees_count }} {{ Str::plural('employee', $team->employees_count) }}
                                    </span>
                                </div>
                                
                                @php
                                    $paidCount = $team->employees->filter(function($emp) use ($currentMonth, $currentYear) {
                                        return $emp->salaryPayments->where('month', $currentMonth)->where('year', $currentYear)->where('status', 'paid')->isNotEmpty();
                                    })->count();
                                    $pendingCount = $team->employees_count - $paidCount;
                                @endphp
                                
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-green-600 dark:text-green-400">
                                        {{ $paidCount }} Paid
                                    </span>
                                    <span class="text-orange-600 dark:text-orange-400">
                                        {{ $pendingCount }} Pending
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12 text-gray-600 dark:text-gray-400">
                            No teams available
                        </div>
                        @endif
                    </div>
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
