@extends('layouts.app')

@section('title', 'Salary Payments')

@section('content')
                <div class="max-w-7xl mx-auto">
                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <a href="{{ route('payments.salaries') }}" class="px-3 py-2 border-b-2 border-amber-500 text-amber-600 dark:text-amber-400 font-medium">
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
                    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex items-center animate-fade-in">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg flex items-center animate-fade-in">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6 p-4 md:p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Salary Payments</h3>
                                <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">Select month and team to process salary payments</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <form method="GET" action="{{ route('payments.salaries') }}" class="flex items-center gap-2">
                                    <select name="month" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                        @endfor
                                    </select>
                                    <select name="year" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                        @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </form>
                                <a href="{{ route('payments.salary.history') }}" class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-all duration-200 hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    History
                                </a>
                            </div>
                        </div>
                        
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3 mb-4">
                            <p class="text-sm text-amber-800 dark:text-amber-300 font-medium">
                                Processing salaries for: {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}
                            </p>
                        </div>
                        
                        @if($teams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($teams as $team)
                            <a href="{{ route('payments.salary.team', ['teamId' => $team->id, 'month' => $currentMonth, 'year' => $currentYear]) }}" class="group block p-4 md:p-5 bg-gray-50 dark:bg-gray-700/50 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-amber-500 dark:hover:border-amber-400 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm md:text-base font-semibold text-gray-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">{{ $team->name }}</h4>
                                    <span class="px-2 py-1 text-xs font-medium bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 rounded-full">
                                        {{ $team->employees_count }} {{ Str::plural('employee', $team->employees_count) }}
                                    </span>
                                </div>
                                
                                @php
                                    $paidCount = $team->employees->filter(function($emp) use ($currentMonth, $currentYear) {
                                        return $emp->salaryPayments->where('month', $currentMonth)->where('year', $currentYear)->where('status', 'completed')->isNotEmpty();
                                    })->count();
                                    $pendingCount = $team->employees_count - $paidCount;
                                @endphp
                                
                                <div class="flex items-center justify-between text-xs md:text-sm">
                                    <span class="flex items-center text-green-600 dark:text-green-400">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ $paidCount }} Paid
                                    </span>
                                    <span class="flex items-center text-orange-600 dark:text-orange-400">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $pendingCount }} Pending
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No teams available</h3>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Create teams and assign employees to process salaries.</p>
                        </div>
                        @endif
                    </div>
                </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
@endsection
