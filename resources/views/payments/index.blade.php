<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - HRM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 ease-in-out">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">HRM</h1>
            </div>

            <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('employees.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Employees</span>
                </a>
                <a href="{{ route('teams.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Teams</span>
                </a>
                <a href="{{ route('loans.index') }}" class="flex items-center space-x-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Loans</span>
                </a>
                <a href="{{ route('payments.index') }}" class="flex items-center space-x-3 px-4 py-3 text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Payments</span>
                </a>
            </nav>

            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <div class="relative">
                    <button id="profile-menu-btn" class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 text-left">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ auth()->user()->mobile }}</p>
                        </div>
                    </button>

                    <div id="profile-dropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Payments</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage loan disbursements and EMI collections</p>
            </header>

            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="mb-8">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Pending Loan Disbursements ({{ $pendingLoans->count() }})</h3>
                        
                        @forelse($pendingLoans as $loan)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->employee->user->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $loan->employee->team ? $loan->employee->team->name : 'No Team' }} • 
                                            {{ $loan->employee->user->mobile }}
                                        </p>
                                    </div>
                                    <button onclick="openDisburseModal({{ $loan->id }})" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        Disburse Loan
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Loan Amount</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->total_amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Monthly EMI</p>
                                        <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($loan->monthly_emi, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">₹{{ number_format($loan->employee->net_pay, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Current EMI</p>
                                        <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($loan->employee->total_emi, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay (After)</p>
                                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">₹{{ number_format($loan->employee->final_pay - $loan->monthly_emi, 2) }}</p>
                                    </div>
                                </div>

                                @if($loan->bankAccount)
                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">Bank Account Details:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Account Holder</p>
                                            <p class="text-gray-900 dark:text-white">{{ $loan->bankAccount->account_holder_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Account Number</p>
                                            <p class="text-gray-900 dark:text-white">{{ $loan->bankAccount->account_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">IFSC Code</p>
                                            <p class="text-gray-900 dark:text-white">{{ $loan->bankAccount->ifsc_code }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Bank Name</p>
                                            <p class="text-gray-900 dark:text-white">{{ $loan->bankAccount->bank_name }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No pending disbursements</h3>
                            <p class="text-gray-600 dark:text-gray-400">All loan applications have been processed.</p>
                        </div>
                        @endforelse
                    </div>

                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Pending EMI Collections ({{ $pendingEmis->count() }})</h3>
                        
                        @forelse($pendingEmis as $payment)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $payment->loan->employee->user->name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $payment->loan->employee->team ? $payment->loan->employee->team->name : 'No Team' }} • 
                                            {{ $payment->loan->employee->user->mobile }}
                                        </p>
                                    </div>
                                    <button onclick="openCollectModal({{ $payment->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                                        Collect EMI
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Installment #</p>
                                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $payment->installment_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">EMI Amount</p>
                                        <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Due Date</p>
                                        <p class="text-lg font-semibold {{ $payment->due_date->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-white' }}">
                                            {{ $payment->due_date->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">₹{{ number_format($payment->loan->employee->net_pay, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Total EMI</p>
                                        <p class="text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($payment->loan->employee->total_emi, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay</p>
                                        <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">₹{{ number_format($payment->loan->employee->final_pay, 2) }}</p>
                                    </div>
                                </div>

                                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Loan ID</p>
                                            <p class="text-gray-900 dark:text-white">#{{ $payment->loan->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Remaining Balance</p>
                                            <p class="text-gray-900 dark:text-white">₹{{ number_format($payment->loan->remaining_balance, 2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600 dark:text-gray-400">Months Left</p>
                                            <p class="text-gray-900 dark:text-white">{{ $payment->loan->remaining_months }} / {{ $payment->loan->total_months }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No pending EMI collections</h3>
                            <p class="text-gray-600 dark:text-gray-400">All EMI payments for this month have been collected.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="disburse-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm Loan Disbursement</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to disburse this loan? This action will activate the loan and begin the EMI schedule.</p>
                
                <form id="disburse-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Add any notes about this disbursement..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeDisburseModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            Confirm Disbursement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="collect-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm EMI Collection</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to mark this EMI payment as collected? This will update the loan balance.</p>
                
                <form id="collect-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Add any notes about this collection..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeCollectModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                            Confirm Collection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDisburseModal(loanId) {
            const modal = document.getElementById('disburse-modal');
            const form = document.getElementById('disburse-form');
            form.action = `/payments/disburse/${loanId}`;
            modal.classList.remove('hidden');
        }

        function closeDisburseModal() {
            document.getElementById('disburse-modal').classList.add('hidden');
        }

        function openCollectModal(paymentId) {
            const modal = document.getElementById('collect-modal');
            const form = document.getElementById('collect-form');
            form.action = `/payments/collect/${paymentId}`;
            modal.classList.remove('hidden');
        }

        function closeCollectModal() {
            document.getElementById('collect-modal').classList.add('hidden');
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
    </script>
</body>
</html>
