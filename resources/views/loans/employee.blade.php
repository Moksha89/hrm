@extends('layouts.app')

@section('title', $employee->user->name . ' Loans')

@section('content')
                <!-- Breadcrumb and Header -->
                <div class="mb-6">
                    <nav class="text-sm text-gray-600 dark:text-gray-400">
                        <a href="{{ route('loans.index') }}" class="hover:text-gray-900 dark:hover:text-white">Loans</a>
                        @if($employee->team)
                        <span class="mx-2">→</span>
                        <a href="{{ route('loans.team', $employee->team->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400">{{ $employee->team->name }}</a>
                        @endif
                        <span class="mx-2">/</span>
                        <span class="text-gray-900 dark:text-white">{{ $employee->user->name }}</span>
                    </nav>
                    <div class="flex items-center justify-between mt-2">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">{{ $employee->user->name }} - Loans</h2>
                        <button id="add-loan-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Apply New Loan
                        </button>
                    </div>
                </div>

                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @forelse($employee->loans as $loan)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Loan #{{ $loan->id }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Applied on {{ $loan->start_date->format('M d, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($loan->status === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                        Pending Disbursement
                                    </span>
                                    @elseif($loan->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                        Active
                                    </span>
                                    @elseif($loan->status === 'closed' || $loan->status === 'waived')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                        {{ ucfirst($loan->status) }}
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                        Paid
                                    </span>
                                    @endif
                                    
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager() || auth()->user()->isTeamLeader())
                                    <a href="{{ route('loans.manage', $loan->id) }}" class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-amber-500 hover:bg-amber-600 text-white transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Manage
                                    </a>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Amount</p>
                                    <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->total_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Monthly EMI</p>
                                    <p class="text-base md:text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($loan->monthly_emi, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Remaining Balance</p>
                                    <p class="text-base md:text-lg font-semibold text-red-600 dark:text-red-400">₹{{ number_format($loan->remaining_balance, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Months Left</p>
                                    <p class="text-base md:text-lg font-semibold text-blue-600 dark:text-blue-400">{{ $loan->remaining_months }} / {{ $loan->total_months }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <h4 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Payment Schedule</h4>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Installment</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Due Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($loan->payments as $payment)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->installment_number }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">₹{{ number_format($payment->amount, 2) }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->due_date->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($payment->status === 'paid')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                                    Paid on {{ $payment->paid_date->format('M d, Y') }}
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                                    Pending
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No loans yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Apply for a new loan to get started.</p>
                        <button onclick="document.getElementById('add-loan-btn').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Apply New Loan
                        </button>
                    </div>
                    @endforelse
                </div>

    <div id="add-loan-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between z-10">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Apply New Loan</h2>
                <button id="close-modal-btn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('loans.store', $employee->id) }}" method="POST" class="p-6">
                @csrf

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loan Amount *</label>
                    <input type="number" name="loan_amount" step="0.01" min="1" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Calculation Type *</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="calculation_type" value="months" checked class="mr-2" onchange="toggleCalculationType()">
                            <span class="text-gray-900 dark:text-white">By number of months</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="calculation_type" value="emi" class="mr-2" onchange="toggleCalculationType()">
                            <span class="text-gray-900 dark:text-white">By monthly EMI amount</span>
                        </label>
                    </div>
                </div>

                <div id="months-input" class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Number of Months *</label>
                    <input type="number" name="months" min="1" max="360" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div id="emi-input" class="mb-6 hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Monthly EMI Amount *</label>
                    <input type="number" name="monthly_emi" step="0.01" min="1" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Account for Disbursement *</label>
                    <select name="bank_account_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="">Select Bank Account</option>
                        @foreach($employee->bankAccounts as $account)
                        <option value="{{ $account->id }}">{{ $account->account_holder_name }} - {{ $account->bank_name }} ({{ substr($account->account_number, -4) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date *</label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" id="cancel-btn" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Apply Loan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const addLoanBtn = document.getElementById('add-loan-btn');
        const addLoanModal = document.getElementById('add-loan-modal');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        addLoanBtn.addEventListener('click', () => {
            addLoanModal.classList.remove('hidden');
        });

        closeModalBtn.addEventListener('click', () => {
            addLoanModal.classList.add('hidden');
        });

        cancelBtn.addEventListener('click', () => {
            addLoanModal.classList.add('hidden');
        });

        function toggleCalculationType() {
            const monthsInput = document.getElementById('months-input');
            const emiInput = document.getElementById('emi-input');
            const calculationType = document.querySelector('input[name="calculation_type"]:checked').value;

            if (calculationType === 'months') {
                monthsInput.classList.remove('hidden');
                emiInput.classList.add('hidden');
                document.querySelector('input[name="months"]').required = true;
                document.querySelector('input[name="monthly_emi"]').required = false;
            } else {
                monthsInput.classList.add('hidden');
                emiInput.classList.remove('hidden');
                document.querySelector('input[name="months"]').required = false;
                document.querySelector('input[name="monthly_emi"]').required = true;
            }
        }

    </script>
@endsection
