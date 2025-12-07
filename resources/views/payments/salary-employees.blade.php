@extends('layouts.app')

@section('title', $team->name . ' - Salary Payments')

@section('content')
                <!-- Breadcrumb -->
                <div class="mb-6">
                    <nav class="text-sm text-gray-600 dark:text-gray-400">
                        <a href="{{ route('payments.salaries') }}" class="hover:text-gray-900 dark:hover:text-white">Payments</a>
                        <span class="mx-2">→</span>
                        <a href="{{ route('payments.salaries') }}" class="hover:text-gray-900 dark:hover:text-white">Salaries</a>
                        <span class="mx-2">→</span>
                        <span class="text-gray-900 dark:text-white">{{ $team->name }}</span>
                    </nav>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ $team->name }} - Salary Payments</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }}</p>
                </div>

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

                    @if($employees->isEmpty())
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees in this team</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign employees to this team to process salary payments.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($employees as $employee)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h3 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                </div>
                                @if($employee->has_salary_payment)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Paid
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                    Pending
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Gross Salary</p>
                                    <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">₹{{ number_format($employee->salary, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Working Days</p>
                                    <p class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->working_days_count }}/30</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                                    <p class="text-base md:text-lg font-semibold text-green-600 dark:text-green-400">₹{{ number_format($employee->net_pay, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">EMI Deduction</p>
                                    <p class="text-base md:text-lg font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($employee->total_emi, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay</p>
                                    <p class="text-base md:text-lg font-semibold text-blue-600 dark:text-blue-400">₹{{ number_format($employee->final_pay, 2) }}</p>
                                </div>
                            </div>

                            @if(!$employee->has_salary_payment)
                            <div class="flex justify-end">
                                <button onclick="disburseSalary({{ $employee->id }}, '{{ $employee->user->name }}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Disburse Salary
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

    <div id="disburse-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4">
            <div class="p-6">
                <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Confirm Salary Disbursement</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to disburse salary for <span id="employee-name" class="font-semibold"></span>?</p>
                
                <form id="disburse-form" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Add any notes about this payment..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDisburseModal()" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Confirm Disbursement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function disburseSalary(employeeId, employeeName) {
            document.getElementById('employee-name').textContent = employeeName;
            document.getElementById('disburse-form').action = `/payments/salary/disburse/${employeeId}`;
            document.getElementById('disburse-modal').classList.remove('hidden');
        }

        function closeDisburseModal() {
            document.getElementById('disburse-modal').classList.add('hidden');
            document.getElementById('notes').value = '';
        }

        document.getElementById('disburse-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDisburseModal();
            }
        });
    </script>
@endsection
