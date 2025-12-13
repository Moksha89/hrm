@extends('layouts.app')

@section('title', 'Payments - Loans')

@section('content')
                <div class="max-w-7xl mx-auto">
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div class="flex-1">
                            <nav class="flex space-x-8 border-b border-gray-200 dark:border-gray-700">
                                <a href="{{ route('payments.salaries') }}" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                    Salaries
                                </a>
                                <a href="{{ route('payments.index') }}" class="px-3 py-2 border-b-2 border-amber-500 text-amber-600 dark:text-amber-400 font-medium">
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
                        <div class="relative">
                            <button onclick="togglePaymentsExportDropdown()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span>Export</span>
                            </button>
                            <div id="payments-export-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20">
                                <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Loans</span>
                                </div>
                                <button onclick="exportData('loans', 'xlsx')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export to Excel (.xlsx)
                                </button>
                                <button onclick="exportData('loans', 'pdf')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export to PDF
                                </button>
                                <div class="px-3 py-2 border-b border-t border-gray-200 dark:border-gray-700">
                                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Transactions</span>
                                </div>
                                <button onclick="exportData('transactions', 'xlsx')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export to Excel (.xlsx)
                                </button>
                                <button onclick="exportData('transactions', 'pdf')" class="block w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export to PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="payments-search" placeholder="Search by employee name or team..." class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
                    </div>

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
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending Loan Disbursements ({{ $pendingLoans->count() }})</h3>
                        
                        @if($pendingLoans->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Employee</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Team</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Loan Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Monthly EMI</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($pendingLoans as $loan)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center justify-center w-8 h-8 bg-blue-600 rounded-full text-white font-semibold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($loan->employee->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $loan->employee->user->name }}</div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $loan->employee->user->mobile }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $loan->employee->team ? $loan->employee->team->name : 'No Team' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-white">
                                                ₹{{ number_format($loan->total_amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-orange-600 dark:text-orange-400">
                                                ₹{{ number_format($loan->monthly_emi, 2) }}
                                            </td>
                                            <td class="px-4 py-3">
                                                @if($loan->approval_status === 'pending')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                                    Pending Approval
                                                </span>
                                                @elseif($loan->approval_status === 'approved')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                                    Approved
                                                </span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="flex items-center space-x-2">
                                                    @if($loan->approval_status === 'pending')
                                                    <form action="{{ route('payments.loan.approve', $loan->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Accept
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('payments.loan.reject', $loan->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Reject
                                                        </button>
                                                    </form>
                                                    @elseif($loan->approval_status === 'approved')
                                                    @php
                                                        $loanBankAccount = $loan->bankAccount ?? $loan->employee->bankAccounts->where('is_default', true)->first() ?? $loan->employee->bankAccounts->first();
                                                    @endphp
                                                    <button onclick="openDisburseModal({{ $loan->id }}, '{{ $loan->employee->user->name }}', '{{ $loan->employee->team?->name ?? 'No Team' }}', {{ $loan->total_amount }}, {{ $loan->total_months }}, '{{ $loanBankAccount?->account_holder_name ?? '' }}', '{{ $loanBankAccount?->account_number ?? '' }}', '{{ $loanBankAccount?->ifsc_code ?? '' }}', '{{ $loanBankAccount?->bank_name ?? '' }}')" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Disburse
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-sm md:text-base font-medium text-gray-900 dark:text-white mb-2">No pending disbursements</h3>
                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">All loan applications have been processed.</p>
                        </div>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Pending EMI Collections ({{ $pendingEmis->count() }})</h3>
                        
                        @if($pendingEmis->count() > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Employee</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Team</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Installment</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">EMI Amount</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Due Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($pendingEmis as $payment)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="px-4 py-3">
                                                <div class="flex items-center space-x-3">
                                                    <div class="flex items-center justify-center w-8 h-8 bg-orange-600 rounded-full text-white font-semibold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($payment->loan->employee->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $payment->loan->employee->user->name }}</div>
                                                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $payment->loan->employee->user->mobile }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                                {{ $payment->loan->employee->team ? $payment->loan->employee->team->name : 'No Team' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                #{{ $payment->installment_number }} of {{ $payment->loan->total_months }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-semibold text-orange-600 dark:text-orange-400">
                                                ₹{{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-sm {{ $payment->due_date->isPast() ? 'text-red-600 dark:text-red-400 font-semibold' : 'text-gray-900 dark:text-white' }}">
                                                {{ $payment->due_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <button onclick="openCollectModal({{ $payment->id }})" class="inline-flex items-center px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs rounded transition-colors">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Collect
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @else
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-sm md:text-base font-medium text-gray-900 dark:text-white mb-2">No pending EMI collections</h3>
                            <p class="text-xs md:text-sm text-gray-600 dark:text-gray-400">All EMI payments for this month have been collected.</p>
                        </div>
                        @endif
                    </div>
                </div>

    <div id="disburse-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full">
            <div class="p-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-2">Confirm Loan Disbursement</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Disburse loan for <span id="loan-employee-name" class="font-semibold text-gray-900 dark:text-white"></span></p>
                
                <!-- Loan Details -->
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Team:</span>
                            <span id="loan-team" class="ml-1 font-medium text-gray-900 dark:text-white"></span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400">Tenure:</span>
                            <span id="loan-tenure" class="ml-1 font-medium text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-500 dark:text-gray-400">Loan Amount:</span>
                            <span id="loan-amount" class="ml-1 font-bold text-blue-600 dark:text-blue-400"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Bank Account Details -->
                <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Bank Account Details
                    </h4>
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Account Holder:</span>
                            <span id="loan-bank-holder" class="font-medium text-gray-900 dark:text-white"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Account Number:</span>
                            <div class="flex items-center space-x-2">
                                <span id="loan-bank-account" class="font-medium text-gray-900 dark:text-white font-mono"></span>
                                <button type="button" onclick="copyToClipboard('loan-bank-account')" class="text-amber-600 hover:text-amber-700 dark:text-amber-400" title="Copy">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">IFSC Code:</span>
                            <div class="flex items-center space-x-2">
                                <span id="loan-bank-ifsc" class="font-medium text-gray-900 dark:text-white font-mono"></span>
                                <button type="button" onclick="copyToClipboard('loan-bank-ifsc')" class="text-amber-600 hover:text-amber-700 dark:text-amber-400" title="Copy">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Bank Name:</span>
                            <span id="loan-bank-name" class="font-medium text-gray-900 dark:text-white"></span>
                        </div>
                    </div>
                </div>
                
                <form id="disburse-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">UTR Number <span class="text-red-500">*</span></label>
                        <input type="text" name="utr_number" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Enter UTR/transaction number...">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Add any remarks about this disbursement..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Screenshot (Optional)</label>
                        <div class="relative">
                            <input type="file" name="screenshot" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/30 dark:file:text-amber-400">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload payment confirmation screenshot (JPG, PNG, GIF - Max 5MB)</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeDisburseModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                            Confirm Disbursement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="collect-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm EMI Collection</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Mark this EMI payment as collected. This will update the loan balance.</p>
                
                <form id="collect-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">UTR Number (Optional)</label>
                        <input type="text" name="utr_number" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Enter UTR/transaction number...">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Add any notes about this collection..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Screenshot (Optional)</label>
                        <div class="relative">
                            <input type="file" name="screenshot" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/30 dark:file:text-amber-400">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload payment confirmation screenshot (JPG, PNG, GIF - Max 5MB)</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeCollectModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                            Confirm Collection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openDisburseModal(loanId, employeeName, teamName, loanAmount, tenure, bankHolder, bankAccount, bankIfsc, bankName) {
            const modal = document.getElementById('disburse-modal');
            const form = document.getElementById('disburse-form');
            form.action = `/payments/disburse/${loanId}`;
            
            // Populate loan details
            document.getElementById('loan-employee-name').textContent = employeeName || '';
            document.getElementById('loan-team').textContent = teamName || 'No Team';
            document.getElementById('loan-amount').textContent = '₹' + new Intl.NumberFormat('en-IN').format(loanAmount || 0);
            document.getElementById('loan-tenure').textContent = (tenure || 0) + ' months';
            
            // Populate bank account details
            document.getElementById('loan-bank-holder').textContent = bankHolder || 'N/A';
            document.getElementById('loan-bank-account').textContent = bankAccount || 'N/A';
            document.getElementById('loan-bank-ifsc').textContent = bankIfsc || 'N/A';
            document.getElementById('loan-bank-name').textContent = bankName || 'N/A';
            
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
        
        function copyToClipboard(elementId) {
            const text = document.getElementById(elementId).textContent;
            navigator.clipboard.writeText(text).then(() => {
                // Show brief feedback
                const btn = event.currentTarget;
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
                setTimeout(() => { btn.innerHTML = originalHTML; }, 1000);
            });
        }
        
        function exportData(type, format) {
            const url = `/export/${type}?format=${format}`;
            
            // Use fetch with credentials to ensure session cookie is sent
            fetch(url, {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'Accept': format === 'pdf' ? 'application/pdf' : (format === 'xlsx' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Export failed');
                }
                // Get filename from Content-Disposition header or generate one
                const contentDisposition = response.headers.get('Content-Disposition');
                let filename = `${type}_export.${format}`;
                if (contentDisposition) {
                    const match = contentDisposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (match && match[1]) {
                        filename = match[1].replace(/['"]/g, '');
                    }
                }
                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                // Create download link and trigger download
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(downloadUrl);
                document.body.removeChild(a);
            })
            .catch(error => {
                console.error('Export error:', error);
                alert('Export failed. Please try again.');
            });
            
            // Close dropdown
            document.getElementById('payments-export-dropdown').classList.add('hidden');
        }
        
        function togglePaymentsExportDropdown() {
            const dropdown = document.getElementById('payments-export-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#payments-export-dropdown') && !e.target.closest('button[onclick="togglePaymentsExportDropdown()"]')) {
                document.getElementById('payments-export-dropdown')?.classList.add('hidden');
            }
        });
        
        const paymentsSearch = document.getElementById('payments-search');
        if (paymentsSearch) {
            paymentsSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('tbody tr').forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

    </script>
@endsection
