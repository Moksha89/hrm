<div wire:poll.30s="loadStats">
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Employees -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Employees</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalEmployees }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-sm">
                <span class="text-green-600 dark:text-green-400 font-medium">{{ $activeEmployees }}</span>
                <span class="text-gray-500 dark:text-gray-400 ml-1">active</span>
            </div>
        </div>

        <!-- Total Teams -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Teams</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalTeams }}</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-sm">
                <a href="{{ route('teams.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">View all teams</a>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pending Requests</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $pendingRequests }}</p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-sm">
                @if($pendingRequests > 0)
                <a href="{{ route('requests.index') }}" class="text-yellow-600 dark:text-yellow-400 hover:underline">Review requests</a>
                @else
                <span class="text-gray-500 dark:text-gray-400">All caught up!</span>
                @endif
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Loans</p>
                    <p class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $activeLoans }}</p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center text-sm">
                <span class="text-gray-500 dark:text-gray-400">Outstanding: </span>
                <span class="text-green-600 dark:text-green-400 font-medium ml-1">₹{{ $totalLoanAmount }}</span>
            </div>
        </div>
    </div>
</div>
