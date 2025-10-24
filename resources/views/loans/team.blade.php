<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $team->name }} Loans - HRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden ml-20">
            @include('components.header')

            <div class="bg-white dark:bg-gray-800 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
                <nav class="text-sm text-gray-600 dark:text-gray-400">
                    <a href="{{ route('loans.index') }}" class="hover:text-gray-900 dark:hover:text-white">Loans</a>
                    <span class="mx-2">→</span>
                    <span class="text-gray-900 dark:text-white">{{ $team->name }}</span>
                </nav>
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $team->name }} - Employee Loans</h2>
            </div>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($team->employees as $employee)
                        @php
                            $activeLoansCount = $employee->loans->where('status', 'active')->where('remaining_balance', '>', 0)->count();
                            $totalLoanAmount = $employee->loans->where('status', 'active')->where('remaining_balance', '>', 0)->sum('remaining_balance');
                            $monthlyEmi = $employee->getTotalMonthlyEmi();
                        @endphp
                        <a href="{{ route('loans.employee', $employee->id) }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="flex items-center justify-center w-12 h-12 bg-blue-600 rounded-full text-white font-semibold text-base md:text-lg">
                                    {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Active Loans</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $activeLoansCount }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Loan Amount</span>
                                    <span class="font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($totalLoanAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Monthly EMI</span>
                                    <span class="font-semibold text-red-600 dark:text-red-400">₹{{ number_format($monthlyEmi, 2) }}</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No employees in this team</h3>
                            <p class="text-gray-600 dark:text-gray-400">Assign employees to this team to view their loans.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
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
