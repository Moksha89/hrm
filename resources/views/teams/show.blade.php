<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $team->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 overflow-hidden">
    <div class="flex h-screen">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col ml-20">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 h-16 flex-shrink-0">
                <div class="h-full px-4 flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <button id="mobile-sidebar-toggle" class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-8 h-8 bg-teal-600 rounded-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span class="text-lg md:text-xl font-bold text-gray-900 dark:text-white hidden sm:block">HRM Portal</span>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button id="dark-mode-toggle" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>

                        <a href="{{ route('notifications.index') }}" class="relative text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if(auth()->user()->unreadNotifications()->count() > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            @endif
                        </a>

                        <div class="relative">
                            <button id="profile-menu-btn" class="flex items-center space-x-3 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="flex items-center justify-center w-8 h-8 bg-teal-600 rounded-full text-white font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span class="hidden md:block font-medium">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50">
                                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->mobile }}</p>
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Settings</a>
                                <div class="border-t border-gray-200 dark:border-gray-700 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('teams.index') }}" class="text-teal-600 hover:text-teal-700">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ $team->name }}</h1>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $team->employees->count() }} employees</p>
                        </div>
                    </div>

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <button onclick="showTab('employees')" id="employees-tab" class="px-3 py-2 border-b-2 border-teal-600 text-teal-600 dark:text-teal-400 font-medium">
                                Employees
                            </button>
                            <button onclick="showTab('salary')" id="salary-tab" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Salary Management
                            </button>
                        </nav>
                    </div>

                    <div id="employees-content" class="tab-content">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Employees</p>
                                    <p class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-1">{{ $team->employees->count() }}</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 bg-teal-100 dark:bg-teal-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gross Salary</p>
                                    <p class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mt-1">₹{{ number_format($totalSalary, 2) }}</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 bg-gray-100 dark:bg-gray-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay (After WD)</p>
                                    <p class="text-2xl md:text-3xl lg:text-4xl font-bold text-green-600 dark:text-green-400 mt-1">₹{{ number_format($totalNetPay, 2) }}</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total EMI</p>
                                    <p class="text-2xl md:text-3xl lg:text-4xl font-bold text-orange-600 dark:text-orange-400 mt-1">₹{{ number_format($totalEmi, 2) }}</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay (After EMI)</p>
                                    <p class="text-2xl md:text-3xl lg:text-4xl font-bold text-teal-600 dark:text-teal-400 mt-1">₹{{ number_format($totalFinalPay, 2) }}</p>
                                </div>
                                <div class="flex items-center justify-center w-12 h-12 bg-teal-100 dark:bg-teal-900/20 rounded-lg">
                                    <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($unassignedEmployees->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-4">Assign Employee</h2>
                        <form action="{{ route('teams.assign', $team->id) }}" method="POST" class="flex items-end space-x-4">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Employee</label>
                                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Choose an employee...</option>
                                    @foreach($unassignedEmployees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->user->mobile }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                                Assign
                            </button>
                        </form>
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Team Members</h2>
                        </div>
                        
                        @if($team->employees->count() > 0)
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($team->employees as $employee)
                            @php
                                $workingDay = $employee->workingDays->first();
                                $currentWorkingDays = $workingDay ? $workingDay->working_days : 30;
                                $netPay = $employee->getNetPay($currentMonth, $currentYear);
                                $monthlyEmi = $employee->getTotalMonthlyEmi();
                                $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
                            @endphp
                            <div class="p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center justify-center w-12 h-12 bg-teal-600 rounded-full text-white font-semibold text-base md:text-lg">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Gross Salary</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($employee->salary ?? 0, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <form action="{{ route('teams.updateWorkingDays', $team->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                            @csrf
                                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                            <input type="hidden" name="month" value="{{ $currentMonth }}">
                                            <input type="hidden" name="year" value="{{ $currentYear }}">
                                            <div>
                                                <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Working Days</label>
                                                <input 
                                                    type="number" 
                                                    name="working_days" 
                                                    value="{{ $currentWorkingDays }}" 
                                                    min="0" 
                                                    max="31" 
                                                    class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white"
                                                    onchange="this.form.submit()"
                                                >
                                            </div>
                                        </form>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                                        <p class="font-semibold text-green-600 dark:text-green-400">₹{{ number_format($netPay, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">EMI Deduction</p>
                                        <p class="font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($monthlyEmi, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay</p>
                                        <p class="font-semibold text-teal-600 dark:text-teal-400">₹{{ number_format($finalPay, 2) }}</p>
                                    </div>
                                    <form action="{{ route('teams.remove', [$team->id, $employee->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No team members yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Assign employees to this team to get started.</p>
                        </div>
                        @endif
                    </div>
                </div>
                    </div>

                    <div id="salary-content" class="tab-content hidden">
                        @php
                            $currentMonth = now()->month;
                            $currentYear = now()->year;
                        @endphp
                        
                        <form action="{{ route('teams.updateBulkSalarySettings', $team->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $currentMonth }}">
                            <input type="hidden" name="year" value="{{ $currentYear }}">
                            
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Salary Settings - {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}</h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update working days and EMI deduction settings for all team members</p>
                                    </div>
                                    <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2 rounded-lg transition-colors">
                                        Save All Changes
                                    </button>
                                </div>
                                
                                @if($team->employees->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Salary</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Working Days</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Pay</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deduct EMI</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">EMI Amount</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Final Pay</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($team->employees as $employee)
                                            @php
                                                $workingDayRecord = $employee->workingDays()
                                                    ->where('month', $currentMonth)
                                                    ->where('year', $currentYear)
                                                    ->first();
                                                $currentWorkingDays = $workingDayRecord ? $workingDayRecord->working_days : 30;
                                                $shouldDeductEmi = $workingDayRecord ? ($workingDayRecord->deduct_emi ?? true) : true;
                                                $netPay = $employee->getNetPay($currentMonth, $currentYear);
                                                $monthlyEmi = $employee->getTotalMonthlyEmi();
                                                $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex items-center justify-center w-10 h-10 bg-teal-600 rounded-full text-white font-semibold text-sm mr-3">
                                                            {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->user->name }}</div>
                                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₹{{ number_format($employee->salary ?? 0, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="number" 
                                                           name="employees[{{ $employee->id }}][working_days]" 
                                                           value="{{ $currentWorkingDays }}" 
                                                           min="0" 
                                                           max="31" 
                                                           class="w-20 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-teal-500 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                        ₹{{ number_format($netPay, 2) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" 
                                                               name="employees[{{ $employee->id }}][deduct_emi]" 
                                                               value="1"
                                                               {{ $shouldDeductEmi ? 'checked' : '' }}
                                                               class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-teal-300 dark:peer-focus:ring-teal-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-teal-600"></div>
                                                    </label>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">
                                                        ₹{{ number_format($monthlyEmi, 2) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-semibold text-teal-600 dark:text-teal-400">
                                                        ₹{{ number_format($finalPay, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="p-12 text-center text-gray-600 dark:text-gray-400">
                                    No employees in this team
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            document.querySelectorAll('[id$="-tab"]').forEach(btn => {
                btn.classList.remove('border-teal-600', 'text-teal-600', 'dark:text-teal-400', 'font-medium');
                btn.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
            });
            
            document.getElementById(tabName + '-tab').classList.add('border-teal-600', 'text-teal-600', 'dark:text-teal-400', 'font-medium');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
        }

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
