@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">
                {{ $currentDate->format('F Y') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">View birthdays and document expiry reminders</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('calendar', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
               class="p-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <a href="{{ route('calendar') }}" 
               class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors">
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
            <div class="p-2 sm:p-4 text-center font-semibold text-gray-700 dark:text-gray-300 text-xs sm:text-sm">
                <span class="hidden sm:inline">{{ $day }}</span>
                <span class="sm:hidden">{{ substr($day, 0, 1) }}</span>
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
                    
                    <div class="min-h-16 sm:min-h-32 p-1 sm:p-2 border-b border-r border-gray-200 dark:border-gray-700 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }}">
                        @if($isCurrentMonth)
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs sm:text-sm font-medium {{ $isToday ? 'bg-teal-600 text-white w-5 h-5 sm:w-6 sm:h-6 rounded-full flex items-center justify-center' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ $day }}
                                </span>
                            </div>
                            
                            @foreach($dayEvents as $event)
                                <div class="mb-1 px-1 sm:px-2 py-0.5 sm:py-1 rounded text-xs truncate {{ $event['color'] == 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : ($event['color'] == 'red' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : ($event['color'] == 'orange' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300')) }}">
                                    <span class="hidden sm:inline">{{ $event['title'] }}</span>
                                    <span class="sm:hidden">{{ Str::limit($event['title'], 5) }}</span>
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

    <div class="mt-6 flex flex-wrap items-center gap-4 sm:gap-6 text-sm">
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
@endsection
