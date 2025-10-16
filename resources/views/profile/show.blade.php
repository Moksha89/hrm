<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - HRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 w-64 flex-shrink-0">
            <div class="flex flex-col h-full">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">HRM Portal</h2>
                </div>

                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 p-3 rounded-lg bg-blue-50 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>My Profile</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('requests.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('requests.*') ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Requests</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('notifications.index') }}" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('notifications.*') ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <span>Notifications</span>
                                @if(auth()->user()->unreadNotifications()->count() > 0)
                                <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                    {{ auth()->user()->unreadNotifications()->count() }}
                                </span>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('calendar') }}" class="flex items-center space-x-3 p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>Calendar</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 p-3 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center space-x-4">
                        <img src="/images/logo.png" alt="HRM Logo" class="h-8" onerror="this.style.display='none'">
                        <h1 class="text-xl font-semibold text-gray-800 dark:text-white">My Profile</h1>
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
                            <button onclick="toggleProfileDropdown()" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth()->user()->name }}</span>
                            </button>

                            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                                <a href="{{ route('password.change') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Change Password</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900">Logout</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded-lg">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Monthly Salary</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">₹{{ number_format($employee->salary, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Salary Paid</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">₹{{ number_format($totalSalaryPaid, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-lg p-3">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Loan Balance</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-white">₹{{ number_format($totalRemainingBalance, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Full Name</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->user->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mobile Number</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->user->mobile }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->user->email ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Team</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->team->name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Date of Birth</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d M Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Date of Joining</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Aadhar Number</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->aadhar ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">PAN Number</p>
                                    <p class="text-base text-gray-900 dark:text-white">{{ $employee->pan ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Bank Accounts</h3>
                        </div>
                        <div class="p-6">
                            @if($employee->bankAccounts->count() > 0)
                                <div class="space-y-4">
                                    @foreach($employee->bankAccounts as $bankAccount)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex items-center justify-between mb-2">
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $bankAccount->bank_name }}</p>
                                                @if($bankAccount->is_default)
                                                    <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-200 rounded-full">Default</span>
                                                @endif
                                            </div>
                                            <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                                <p><span class="font-medium">Account Holder:</span> {{ $bankAccount->account_holder_name }}</p>
                                                <p><span class="font-medium">Account Number:</span> {{ $bankAccount->account_number }}</p>
                                                <p><span class="font-medium">IFSC Code:</span> {{ $bankAccount->ifsc_code }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No bank accounts added.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 mt-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Loans</h3>
                        </div>
                        <div class="p-6">
                            @if($employee->loans->count() > 0)
                                <div class="space-y-4">
                                    @foreach($employee->loans as $loan)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="font-medium text-gray-900 dark:text-white">Loan #{{ $loan->id }}</h4>
                                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                                    @if($loan->status === 'active') bg-green-200 text-green-800
                                                    @elseif($loan->status === 'completed') bg-blue-200 text-blue-800
                                                    @else bg-yellow-200 text-yellow-800
                                                    @endif">
                                                    {{ ucfirst($loan->status) }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400">Total Amount</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->total_amount, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400">Remaining Balance</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->remaining_balance, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400">Monthly EMI</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->monthly_emi, 2) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-600 dark:text-gray-400">Remaining Months</p>
                                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->remaining_months }}/{{ $loan->total_months }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No loans taken.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                        </div>
                        <div class="p-6">
                            @if($employee->documents->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($employee->documents as $document)
                                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $document->document_name }}</p>
                                                @if($document->expiry_date)
                                                    <p class="text-sm text-gray-600 dark:text-gray-400">Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('d M Y') }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No documents uploaded.</p>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Activity History</h3>
                        </div>
                        <div class="p-6">
                            @if($employee->activities->count() > 0)
                                <div class="space-y-4">
                                    @foreach($employee->activities as $activity)
                                        <div class="flex items-start space-x-3">
                                            <div class="flex-shrink-0 w-2 h-2 mt-2 bg-blue-500 rounded-full"></div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->format('d M Y, h:i A') }}</p>
                                                @if($activity->performedBy)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">By: {{ $activity->performedBy->name }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">No activity history.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const button = event.target.closest('button');
            
            if (!button || button.getAttribute('onclick') !== 'toggleProfileDropdown()') {
                dropdown.classList.add('hidden');
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
