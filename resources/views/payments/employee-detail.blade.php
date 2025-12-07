@extends('layouts.app')

@section('title', $employee->user->name . ' - Employee Details')

@section('content')
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="text-sm text-gray-600 dark:text-gray-400">
                        <a href="{{ route('payments.employees') }}" class="hover:text-gray-900 dark:hover:text-white">Payments</a>
                        <span class="mx-2">→</span>
                        <a href="{{ route('payments.employees') }}" class="hover:text-gray-900 dark:hover:text-white">Employees</a>
                        <span class="mx-2">→</span>
                        <span class="text-gray-900 dark:text-white">{{ $employee->user->name }}</span>
                    </nav>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mt-2">Employee Financial Details</h2>
                </div>

                <div class="max-w-7xl mx-auto space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Employee Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full text-white font-semibold text-lg md:text-xl">
                                    {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Team:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $employee->team ? $employee->team->name : 'No Team' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                    @if($employee->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                        Active
                                    </span>
                                    @elseif($employee->status === 'inactive')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">
                                        Inactive
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                        Resigned
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Salary Summary - {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Gross Salary</p>
                                <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                                    ₹{{ number_format($employee->salary ?? 0, 2) }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Working Days</p>
                                <p class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $employee->getWorkingDays($currentMonth, $currentYear) }}/30
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Pay</p>
                                <p class="text-lg md:text-xl font-bold text-green-600 dark:text-green-400">
                                    ₹{{ number_format($employee->getNetPay($currentMonth, $currentYear), 2) }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">EMI Deduction</p>
                                <p class="text-lg md:text-xl font-bold text-orange-600 dark:text-orange-400">
                                    ₹{{ number_format($employee->getTotalMonthlyEmi(), 2) }}
                                </p>
                            </div>
                            
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Final Pay</p>
                                <p class="text-lg md:text-xl font-bold text-blue-600 dark:text-blue-400">
                                    ₹{{ number_format($employee->getFinalPay($currentMonth, $currentYear), 2) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Active Loans</h3>
                        @if($employee->activeLoans->count() > 0)
                        <div class="space-y-4">
                            @foreach($employee->activeLoans as $loan)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Loan ID</p>
                                        <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">#{{ $loan->id }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Loan Amount</p>
                                        <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">
                                            ₹{{ number_format($loan->loan_amount, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Remaining Balance</p>
                                        <p class="text-base md:text-lg font-semibold text-orange-600 dark:text-orange-400">
                                            ₹{{ number_format($loan->remaining_balance, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Monthly EMI</p>
                                        <p class="text-base md:text-lg font-semibold text-blue-600 dark:text-blue-400">
                                            ₹{{ number_format($loan->monthly_emi, 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-center text-gray-600 dark:text-gray-400 py-8">No active loans</p>
                        @endif
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                        </div>
                        @if($recentTransactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Notes</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y') : $transaction->created_at->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaction->payment_type === 'loan_disbursement')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-800 dark:text-purple-300">
                                                Loan Disbursement
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300">
                                                EMI Collection
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            ₹{{ number_format($transaction->amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($transaction->status === 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                                Completed
                                            </span>
                                            @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                                Pending
                                            </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white max-w-xs truncate">
                                            {{ $transaction->notes ?? 'N/A' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-center text-gray-600 dark:text-gray-400 py-8">No recent transactions</p>
                        @endif
                    </div>
                </div>
@endsection
