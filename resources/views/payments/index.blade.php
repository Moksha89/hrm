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
                            <div id="payments-export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20">
                                <a href="{{ route('export.loans', ['format' => 'xlsx']) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export Loans (.xlsx)
                                </a>
                                <a href="{{ route('export.transactions', ['format' => 'xlsx']) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                    Export Transactions (.xlsx)
                                </a>
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
                                                    <button onclick="openDisburseModal({{ $loan->id }})" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-colors">
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

    <div id="disburse-modal"class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm Loan Disbursement</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to disburse this loan? This action will activate the loan and begin the EMI schedule.</p>
                
                <form id="disburse-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">UTR Number <span class="text-red-500">*</span></label>
                        <input type="text" name="utr_number" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Enter UTR/transaction number...">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks (Optional)</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Add any remarks about this disbursement..."></textarea>
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

    <div id="collect-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white mb-4">Confirm EMI Collection</h3>
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
