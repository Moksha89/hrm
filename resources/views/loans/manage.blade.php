@extends('layouts.app')

@section('title', 'Manage Loan - ' . $loan->employee->user->name)

@section('content')
<div class="max-w-6xl mx-auto">
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
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Manage Loan</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $loan->employee->user->name }} - {{ $loan->employee->team?->name ?? 'No Team' }}</p>
        </div>
        <a href="{{ route('loans.employee', $loan->employee_id) }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition text-sm">
            Back to Loans
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Total Amount</h3>
            <p class="text-xl font-bold text-gray-900 dark:text-white">₹{{ number_format($loan->total_amount, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Remaining Balance</h3>
            <p class="text-xl font-bold text-amber-600 dark:text-amber-400">₹{{ number_format($loan->remaining_balance, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase mb-1">Status</h3>
            <span class="px-2 py-1 text-xs rounded-full {{ $loan->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : ($loan->status === 'paid' || $loan->status === 'closed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                {{ ucfirst($loan->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Current EMI:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($loan->monthly_emi, 2) }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Remaining Months:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->remaining_months }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Total Months:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->total_months }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Start Date:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->start_date->format('d M Y') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Paid EMIs:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->payments->where('status', 'paid')->count() }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Pending EMIs:</span>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $loan->payments->where('status', 'pending')->count() }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Total Paid:</span>
                    <p class="font-semibold text-green-600 dark:text-green-400">₹{{ number_format($loan->payments->where('status', 'paid')->sum('amount'), 2) }}</p>
                </div>
                <div>
                    <span class="text-gray-500 dark:text-gray-400">Bank Account:</span>
                    <p class="font-semibold text-gray-900 dark:text-white text-xs">{{ $loan->bankAccount?->bank_name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($loan->status === 'active' && $loan->remaining_balance > 0)
    <div x-data="{ activeTab: 'emi' }" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px">
                <button @click="activeTab = 'emi'" :class="activeTab === 'emi' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Change EMI
                </button>
                <button @click="activeTab = 'tenure'" :class="activeTab === 'tenure' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Change Tenure
                </button>
                <button @click="activeTab = 'preclose'" :class="activeTab === 'preclose' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Pre-Closure
                </button>
                @if(auth()->user()->isAdmin())
                <button @click="activeTab = 'waive'" :class="activeTab === 'waive' ? 'border-amber-500 text-amber-600 dark:text-amber-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'" class="px-6 py-4 border-b-2 font-medium text-sm transition">
                    Waive Off
                </button>
                @endif
            </nav>
        </div>

        <div class="p-6">
            <div x-show="activeTab === 'emi'">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Change EMI Amount</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Changing the EMI will automatically recalculate the remaining tenure based on the outstanding balance.</p>
                
                <form action="{{ route('loans.updateEmi', $loan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Current EMI</label>
                            <input type="text" value="₹{{ number_format($loan->monthly_emi, 2) }}" disabled class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">New EMI Amount *</label>
                            <input type="number" name="new_emi" step="0.01" min="1" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter new EMI amount">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Change *</label>
                        <textarea name="reason" rows="2" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter reason for EMI change"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition text-sm font-medium">
                        Update EMI
                    </button>
                </form>
            </div>

            <div x-show="activeTab === 'tenure'">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Change Tenure</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Changing the tenure will automatically recalculate the EMI amount based on the outstanding balance.</p>
                
                <form action="{{ route('loans.updateTenure', $loan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Current Remaining Tenure</label>
                            <input type="text" value="{{ $loan->remaining_months }} months" disabled class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">New Tenure (months) *</label>
                            <input type="number" name="new_tenure" min="1" max="360" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter new tenure in months">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Change *</label>
                        <textarea name="reason" rows="2" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter reason for tenure change"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition text-sm font-medium">
                        Update Tenure
                    </button>
                </form>
            </div>

            <div x-show="activeTab === 'preclose'">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Pre-Close Loan</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pre-closing the loan will mark all pending EMIs as cancelled and close the loan with the settlement amount.</p>
                
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 mb-4">
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        <strong>Outstanding Balance:</strong> ₹{{ number_format($loan->remaining_balance, 2) }}
                    </p>
                </div>
                
                <form action="{{ route('loans.preClose', $loan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Settlement Amount *</label>
                            <input type="number" name="settlement_amount" step="0.01" min="0" value="{{ $loan->remaining_balance }}" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Settlement Date *</label>
                            <input type="date" name="settlement_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Pre-Closure *</label>
                        <textarea name="reason" rows="2" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter reason for pre-closure"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-sm font-medium" onclick="return confirm('Are you sure you want to pre-close this loan? This action cannot be undone.')">
                        Pre-Close Loan
                    </button>
                </form>
            </div>

            @if(auth()->user()->isAdmin())
            <div x-show="activeTab === 'waive'">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Waive Off Loan</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Waiving off the loan will mark all pending EMIs as waived and close the loan without any payment.</p>
                
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4 mb-4">
                    <p class="text-sm text-red-800 dark:text-red-300">
                        <strong>Warning:</strong> This will waive off ₹{{ number_format($loan->remaining_balance, 2) }} from the loan. This action cannot be undone.
                    </p>
                </div>
                
                <form action="{{ route('loans.waiveOff', $loan->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Waive Off *</label>
                        <textarea name="reason" rows="3" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500" placeholder="Enter detailed reason for waiving off this loan"></textarea>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition text-sm font-medium" onclick="return confirm('Are you absolutely sure you want to waive off this loan? This will write off ₹{{ number_format($loan->remaining_balance, 2) }} and cannot be undone.')">
                        Waive Off Loan
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="mt-2 text-gray-600 dark:text-gray-400">This loan is {{ $loan->status }}. No modifications can be made.</p>
    </div>
    @endif

    <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Payment Schedule</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Paid Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($loan->payments->sortBy('installment_number') as $payment)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->installment_number }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->due_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : ($payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $payment->paid_date ? $payment->paid_date->format('d M Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
