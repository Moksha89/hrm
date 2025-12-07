@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 gap-3">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                {{ $currentDate->format('F Y') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Birthdays & document expiry reminders</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('calendar', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
               class="p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <a href="{{ route('calendar') }}" 
               class="px-3 py-1 bg-amber-500 hover:bg-amber-400 text-black rounded-lg text-xs font-medium transition-colors">
                Today
            </a>
            <a href="{{ route('calendar', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
               class="p-1.5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Calendar Grid -->
        <div class="lg:col-span-3">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700">
                    @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                    <div class="p-1.5 text-center font-semibold text-gray-700 dark:text-gray-300 text-xs">
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
                            
                            <div class="min-h-10 sm:min-h-16 p-0.5 sm:p-1 border-b border-r border-gray-200 dark:border-gray-700 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }}">
                                @if($isCurrentMonth)
                                    <div class="flex items-center justify-center mb-0.5">
                                        <span class="text-xs font-medium {{ $isToday ? 'bg-amber-500 text-black w-5 h-5 rounded-full flex items-center justify-center' : 'text-gray-700 dark:text-gray-300' }}">
                                            {{ $day }}
                                        </span>
                                    </div>
                                    
                                    @foreach($dayEvents->take(2) as $event)
                                        <div class="mb-0.5 mx-auto w-2 h-2 rounded-full {{ $event['color'] == 'blue' ? 'bg-blue-500' : ($event['color'] == 'red' ? 'bg-red-500' : ($event['color'] == 'orange' ? 'bg-orange-500' : 'bg-yellow-500')) }}" title="{{ $event['title'] }}"></div>
                                    @endforeach
                                    @if($dayEvents->count() > 2)
                                        <div class="text-center text-xs text-gray-400">+{{ $dayEvents->count() - 2 }}</div>
                                    @endif
                                @endif
                            </div>
                        @endfor
                        @if($dayCounter > $daysInMonth)
                            @break
                        @endif
                    @endfor
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs">
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                    <span class="text-gray-600 dark:text-gray-400">Birthdays</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                    <span class="text-gray-600 dark:text-gray-400">Urgent</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    <span class="text-gray-600 dark:text-gray-400">Medium</span>
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                    <span class="text-gray-600 dark:text-gray-400">Early</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Lists -->
        <div class="space-y-4">
            <!-- Birthdays This Month -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.704 2.704 0 003 15.546V12a9 9 0 0118 0v3.546z"></path>
                    </svg>
                    Birthdays ({{ $birthdays->count() }})
                </h3>
                @if($birthdays->count() > 0)
                    <ul class="space-y-1.5 max-h-40 overflow-y-auto">
                        @foreach($birthdays as $birthday)
                            <li class="flex items-center justify-between text-xs">
                                <a href="{{ route('employees.show', $birthday['employee_id']) }}" class="text-gray-700 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 truncate">
                                    {{ $birthday['employee_name'] }}
                                </a>
                                <span class="text-gray-500 dark:text-gray-400 ml-1 flex-shrink-0">{{ $birthday['date'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">No birthdays this month</p>
                @endif
            </div>

            <!-- Document Expiries -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Expiring Soon ({{ $upcomingExpiries->count() }})
                </h3>
                @if($upcomingExpiries->count() > 0)
                    <ul class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($upcomingExpiries as $expiry)
                            <li class="text-xs">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('employees.show', $expiry['employee_id']) }}" class="text-gray-700 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 truncate">
                                        {{ $expiry['employee_name'] }}
                                    </a>
                                    <span class="text-xs px-1 py-0.5 rounded {{ $expiry['color'] == 'red' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : ($expiry['color'] == 'orange' ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300') }} flex-shrink-0 ml-1">
                                        {{ $expiry['days_until_expiry'] }}d
                                    </span>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 truncate">{{ $expiry['document_name'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400">No documents expiring soon</p>
                @endif
            </div>

            <!-- Quick Stats -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Summary
                </h3>
                <div class="space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Birthdays this month</span>
                        <span class="font-medium text-gray-900 dark:text-white">{{ $birthdays->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Urgent (7 days)</span>
                        <span class="font-medium text-red-600 dark:text-red-400">{{ $upcomingExpiries->where('days_until_expiry', '<=', 7)->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total expiring</span>
                        <span class="font-medium text-orange-600 dark:text-orange-400">{{ $upcomingExpiries->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
