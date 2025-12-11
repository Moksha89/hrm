@php
    $navItems = [
        [
            'route' => 'dashboard',
            'title' => 'Dashboard',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>',
            'roles' => ['all'],
        ],
        [
            'route' => 'profile.show',
            'title' => 'My Profile',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
            'roles' => ['employee_only'],
        ],
        [
            'route' => 'employees.index',
            'title' => 'Employees',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>',
            'roles' => ['Admin', 'Team Leader', 'Manager', 'HR'],
        ],
        [
            'route' => 'teams.index',
            'title' => 'Teams',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
            'roles' => ['Admin', 'Manager', 'Team Leader'],
        ],
        [
            'route' => 'payments.index',
            'title' => 'Payments',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>',
            'roles' => ['Admin', 'Accountant', 'Manager'],
        ],
        [
            'route' => 'loans.index',
            'title' => 'Loans',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
            'roles' => ['Admin', 'Manager', 'Team Leader', 'Accountant'],
        ],
        [
            'route' => 'requests.index',
            'title' => 'Requests',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>',
            'roles' => ['all'],
        ],
        [
            'route' => 'notifications.index',
            'title' => 'Notifications',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>',
            'roles' => ['all'],
            'badge' => true,
        ],
        [
            'route' => 'calendar',
            'title' => 'Calendar',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
            'roles' => ['all'],
        ],
        [
            'route' => 'logs.index',
            'title' => 'Logs',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>',
            'roles' => ['Admin', 'Manager', 'Team Leader'],
        ],
    ];
@endphp

<nav class="flex-1 overflow-y-auto py-4">
    <ul class="space-y-1 px-2">
        @foreach($navItems as $item)
            @php
                $show = false;
                if (in_array('all', $item['roles'])) {
                    $show = true;
                } elseif (in_array('employee_only', $item['roles'])) {
                    $show = auth()->user()->employee !== null;
                } else {
                    foreach ($item['roles'] as $role) {
                        if (auth()->user()->hasRole($role)) {
                            $show = true;
                            break;
                        }
                    }
                }
                $isActive = request()->routeIs($item['route']) || request()->routeIs($item['route'] . '.*');
            @endphp
            
            @if($show)
            <li>
                <a href="{{ route($item['route']) }}" 
                   class="group relative flex items-center {{ $expanded ? 'px-3' : 'justify-center px-2' }} py-3 rounded-lg text-sm font-medium transition-all duration-200
                          {{ $isActive 
                              ? 'bg-amber-500/20 text-amber-400 border-l-2 border-amber-500' 
                              : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}"
                   @if(!$expanded) title="{{ $item['title'] }}" @endif>
                    <svg class="w-6 h-6 flex-shrink-0 {{ $isActive ? 'text-amber-400' : 'text-neutral-400 group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $item['icon'] !!}
                    </svg>
                    @if($expanded)
                    <span class="ml-3">{{ $item['title'] }}</span>
                    @endif
                    
                    @if(isset($item['badge']) && $item['badge'] && auth()->user()->unreadNotifications()->count() > 0)
                    <span class="absolute {{ $expanded ? 'right-3' : 'top-2 right-2' }} flex h-5 w-5 items-center justify-center">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-xs text-black font-medium">
                            {{ auth()->user()->unreadNotifications()->count() > 9 ? '9+' : auth()->user()->unreadNotifications()->count() }}
                        </span>
                    </span>
                    @endif
                </a>
            </li>
            @endif
        @endforeach
    </ul>
</nav>

<!-- User info at bottom (mobile only) -->
@if($expanded)
<div class="border-t border-neutral-800 p-4">
    <div class="flex items-center">
        <div class="flex items-center justify-center w-10 h-10 bg-amber-500 rounded-full text-black font-semibold">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
            <p class="text-xs text-neutral-400">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
        </div>
    </div>
</div>
@endif
