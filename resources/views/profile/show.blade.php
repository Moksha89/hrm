@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">My Profile</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">View your personal information and financial summary</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
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
</div>
@endsection
