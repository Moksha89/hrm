<aside id="sidebar" class="bg-[#1e293b] border-r border-gray-700 transition-all duration-300 ease-in-out w-20 fixed left-0 top-0 h-screen z-40">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-center p-4 h-16 border-b border-gray-700">
            <div class="flex items-center justify-center w-10 h-10 bg-teal-600 rounded-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('dashboard') ? 'text-teal-400 bg-gray-700' : '' }}" title="Dashboard">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </a>
                </li>
                
                @if(auth()->user()->employee)
                <li>
                    <a href="{{ route('profile.show') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('profile.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="My Profile">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader() || auth()->user()->isManager() || auth()->user()->isHR())
                <li>
                    <a href="{{ route('employees.index') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('employees.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Employees">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isTeamLeader())
                <li>
                    <a href="{{ route('teams.index') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('teams.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Teams">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->isAdmin() || auth()->user()->isAccountant() || auth()->user()->isManager())
                <li>
                    <a href="{{ route('payments.index') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('payments.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Payments">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </a>
                </li>
                @endif
                
                @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isTeamLeader() || auth()->user()->isAccountant())
                <li>
                    <a href="{{ route('loans.index') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('loans.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Loans">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                </li>
                @endif
                
                <li>
                    <a href="{{ route('requests.index') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('requests.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Requests">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('notifications.index') }}" class="relative flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('notifications.*') ? 'text-teal-400 bg-gray-700' : '' }}" title="Notifications">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        @if(auth()->user()->unreadNotifications()->count() > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('calendar') }}" class="flex items-center justify-center px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-700 transition-colors {{ request()->routeIs('calendar') ? 'text-teal-400 bg-gray-700' : '' }}" title="Calendar">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
