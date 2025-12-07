@extends('layouts.app')

@section('title', $employee->user->name)

@section('content')
                <div class="max-w-7xl mx-auto">
                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded mb-4">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $employee->user->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $employee->user->mobile }}</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition">
                                Back to List
                            </a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader())
                                <a href="{{ route('employees.edit', $employee->id) }}" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition">
                                    Edit Employee
                                </a>
                            @endif
                        </div>
                    </div>

                    <div x-data="{ activeTab: 'personal' }" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="flex -mb-px">
                                <button @click="activeTab = 'personal'" :class="activeTab === 'personal' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Personal Information
                                </button>
                                <button @click="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Bank Accounts
                                </button>
                                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Documents
                                </button>
                                <button @click="activeTab = 'loans'" :class="activeTab === 'loans' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Loans
                                </button>
                                <button @click="activeTab = 'salary'" :class="activeTab === 'salary' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Salary History
                                </button>
                                <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                                    Activity Log
                                </button>
                            </nav>
                        </div>

                        <div class="p-6">
                            <div x-show="activeTab === 'personal'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->user->name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->user->mobile }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->user->email }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Team</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->team ? $employee->team->name : 'No Team' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                                        <p class="mt-1">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $employee->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : ($employee->status === 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Salary</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">₹{{ number_format($employee->salary, 2) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Aadhar Number</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->aadhar ?: 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">PAN Number</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->pan ?: 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d M Y') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date of Joining</label>
                                        <p class="mt-1 text-gray-900 dark:text-white">{{ $employee->date_of_joining ? \Carbon\Carbon::parse($employee->date_of_joining)->format('d M Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="activeTab === 'bank'">
                                @if($employee->bankAccounts->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($employee->bankAccounts as $account)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 {{ $account->is_default ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}">
                                                <div class="flex justify-between items-start mb-3">
                                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $account->bank_name }}</h3>
                                                    @if($account->is_default)
                                                        <span class="px-2 py-1 text-xs bg-amber-500 text-white rounded">Default</span>
                                                    @endif
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Account Holder:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">{{ $account->account_holder_name }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Account Number:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">{{ $account->account_number }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">IFSC Code:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">{{ $account->ifsc_code }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No bank accounts added</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'documents'">
                                @if($employee->documents->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($employee->documents as $document)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $document->document_name }}</h3>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            Uploaded: {{ $document->created_at->format('d M Y') }}
                                                        </p>
                                                        @if($document->expiry_date)
                                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                                Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('d M Y') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No documents uploaded</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'loans'">
                                @if($employee->loans->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($employee->loans as $loan)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div>
                                                        <h3 class="font-semibold text-gray-900 dark:text-white">Loan #{{ $loan->id }}</h3>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $loan->created_at->format('d M Y') }}</p>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $loan->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                        {{ ucfirst($loan->status) }}
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($loan->amount, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">EMI:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($loan->emi, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Paid:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($loan->loanPayments->where('status', 'paid')->sum('amount'), 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Balance:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($loan->amount - $loan->loanPayments->where('status', 'paid')->sum('amount'), 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No loans taken</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'salary'">
                                @if($employee->salaryPayments->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($employee->salaryPayments->sortByDesc('created_at') as $payment)
                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div>
                                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::create($payment->year, $payment->month)->format('F Y') }}</h3>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">Processed: {{ $payment->created_at->format('d M Y') }}</p>
                                                    </div>
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $payment->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Gross Salary:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($payment->gross_salary, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Working Days:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">{{ $payment->working_days }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Total Deductions:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2">₹{{ number_format($payment->total_deductions, 2) }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600 dark:text-gray-400">Final Pay:</span>
                                                        <span class="text-gray-900 dark:text-white ml-2 font-semibold">₹{{ number_format($payment->final_pay, 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No salary payments processed yet</p>
                                    </div>
                                @endif
                            </div>

                            <div x-show="activeTab === 'activity'">
                                @if($employee->activities->count() > 0)
                                    <div class="space-y-4">
                                        @foreach($employee->activities->sortByDesc('created_at') as $activity)
                                            <div class="border-l-4 {{ $activity->activity_type === 'joined' ? 'border-green-500' : ($activity->activity_type === 'deleted' ? 'border-red-500' : 'border-blue-500') }} pl-4 py-2">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                            By: {{ $activity->performedBy->name }} • {{ $activity->created_at->format('d M Y H:i') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-12">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 text-gray-600 dark:text-gray-400">No activity recorded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
@endsection
