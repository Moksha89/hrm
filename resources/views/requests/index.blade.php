<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val)); $watch('sidebarOpen', val => localStorage.setItem('sidebarOpen', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests - HRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex h-screen overflow-hidden">
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out flex flex-col">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h1 x-show="sidebarOpen" class="text-xl md:text-2xl font-bold text-blue-600 dark:text-blue-400">HRM</h1>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Dashboard</span>
                </a>

                @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader() || auth()->user()->isManager() || auth()->user()->isHR() || auth()->user()->isAccountant())
                <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Employees</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isTeamLeader())
                <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Teams</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isAccountant())
                <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Payments</span>
                </a>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isTeamLeader() || auth()->user()->isAccountant())
                <a href="{{ route('loans.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Loans</span>
                </a>
                @endif

                <a href="{{ route('requests.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-400 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Requests</span>
                </a>

                <a href="{{ route('notifications.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Notifications</span>
                    @if(auth()->user()->unreadNotifications()->count() > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1">{{ auth()->user()->unreadNotifications()->count() }}</span>
                    @endif
                </a>

                <a href="{{ route('calendar') }}" class="flex items-center space-x-3 px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-show="sidebarOpen" class="text-sm md:text-base">Calendar</span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 md:px-6 py-3 md:py-4 flex items-center justify-between">
                <h2 class="text-lg md:text-xl lg:text-2xl font-semibold">Requests</h2>

                <div class="flex items-center space-x-2 md:space-x-4">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg x-show="!darkMode" class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center space-x-2 px-3 md:px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm md:text-base">
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                @if(session('success'))
                <div class="mb-4 md:mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 md:mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($requests as $request)
                                <tr>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        <span class="px-2 py-1 text-xs md:text-sm font-semibold rounded-full
                                            @if($request->type === 'promotion') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @elseif($request->type === 'salary_hike') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($request->type === 'loan') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($request->type === 'resign') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->employee->user->name }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->requestedBy->name }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        <span class="px-2 py-1 text-xs md:text-sm font-semibold rounded-full
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->created_at->format('M d, Y') }}</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('requests.approve', $request->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs md:text-sm">Approve</button>
                                        </form>
                                        <button onclick="showRejectModal({{ $request->id }})" class="ml-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs md:text-sm">Reject</button>
                                        @else
                                        <span class="text-gray-500 dark:text-gray-400 text-xs md:text-sm">{{ $request->status === 'approved' ? 'Approved' : 'Rejected' }}</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 md:px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm md:text-base">
                                        No requests found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($requests->hasPages())
                    <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $requests->links() }}
                    </div>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg md:text-xl font-semibold mb-4">Reject Request</h3>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm md:text-base font-medium mb-2">Rejection Reason</label>
                    <textarea name="rejection_reason" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm md:text-base" rows="4"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 rounded-lg text-sm md:text-base">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm md:text-base">Reject</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showRejectModal(requestId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/requests/${requestId}/reject`;
            modal.style.display = 'flex';
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.style.display = 'none';
        }
    </script>
</body>
</html>
