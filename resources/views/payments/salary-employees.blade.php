@extends('layouts.app')

@section('title', $team->name . ' - Salary Payments')

@section('content')
                <!-- Breadcrumb -->
                <div class="mb-4">
                    <nav class="text-xs text-gray-600 dark:text-gray-400">
                        <a href="{{ route('payments.salaries') }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Payments</a>
                        <span class="mx-1.5">→</span>
                        <a href="{{ route('payments.salaries', ['month' => $currentMonth, 'year' => $currentYear]) }}" class="hover:text-amber-600 dark:hover:text-amber-400 transition-colors">Salaries</a>
                        <span class="mx-1.5">→</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $team->name }}</span>
                    </nav>
                    <div class="flex flex-wrap items-center justify-between gap-3 mt-2">
                        <div>
                            <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">{{ $team->name }} - Salary Payments</h2>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }}</p>
                        </div>
                        <form method="GET" action="{{ route('payments.salary.team', $team->id) }}" class="flex items-center gap-2">
                            <select name="month" onchange="this.form.submit()" class="px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endfor
                            </select>
                            <select name="year" onchange="this.form.submit()" class="px-2 py-1.5 text-xs border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </form>
                    </div>
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center">
                        <svg class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No employees in this team</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Assign employees to this team to process salary payments.</p>
                    </div>
                    @else
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($employees as $employee)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 font-semibold text-sm">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm md:text-base font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    </div>
                                </div>
                                @if($employee->has_salary_payment)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Paid
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/20 text-yellow-800 dark:text-yellow-300">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending
                                </span>
                                @endif
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-3">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Gross Salary</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">₹{{ number_format($employee->salary, 0) }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Working Days</p>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $employee->working_days_count }}/30</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Net Pay</p>
                                    <p class="text-sm font-semibold text-green-600 dark:text-green-400">₹{{ number_format($employee->net_pay, 0) }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">EMI Deduction</p>
                                    <p class="text-sm font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($employee->total_emi, 0) }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Final Pay</p>
                                    <p class="text-sm font-semibold text-amber-600 dark:text-amber-400">₹{{ number_format($employee->final_pay, 0) }}</p>
                                </div>
                            </div>

                            @if(!$employee->has_salary_payment)
                            <div class="flex justify-end">
                                <button onclick="disburseSalary({{ $employee->id }}, '{{ $employee->user->name }}', {{ $currentMonth }}, {{ $currentYear }})" class="inline-flex items-center px-3 py-1.5 bg-amber-500 text-white text-sm rounded-lg hover:bg-amber-600 transition-all duration-200 hover:shadow-md">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Disburse
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

    <div id="disburse-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full mx-4 animate-modal-in">
            <div class="p-5">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm md:text-base font-semibold text-gray-900 dark:text-white">Confirm Salary Disbursement</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" id="disburse-month-year"></p>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Disburse salary for <span id="employee-name" class="font-semibold text-gray-900 dark:text-white"></span>?</p>
                
                <form id="disburse-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="month" id="disburse-month">
                    <input type="hidden" name="year" id="disburse-year">
                    <div class="mb-4">
                        <label for="utr_number" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">UTR Number (Optional)</label>
                        <input type="text" id="utr_number" name="utr_number" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Enter UTR/transaction number...">
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Remarks (Optional)</label>
                        <textarea id="notes" name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white" placeholder="Add any notes about this payment..."></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="screenshot" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1.5">Payment Screenshot (Optional)</label>
                        <div class="relative">
                            <input type="file" id="screenshot" name="screenshot" accept="image/*" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-900/30 dark:file:text-amber-400">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload payment confirmation screenshot (JPG, PNG, GIF - Max 5MB)</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeDisburseModal()" class="px-3 py-1.5 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-3 py-1.5 text-sm bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all duration-200 hover:shadow-md">
                            Confirm Disbursement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes modal-in {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-modal-in {
            animation: modal-in 0.2s ease-out;
        }
    </style>

    <script>
        function disburseSalary(employeeId, employeeName, month, year) {
            document.getElementById('employee-name').textContent = employeeName;
            document.getElementById('disburse-form').action = `/payments/salary/disburse/${employeeId}`;
            document.getElementById('disburse-month').value = month;
            document.getElementById('disburse-year').value = year;
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            document.getElementById('disburse-month-year').textContent = monthNames[month - 1] + ' ' + year;
            
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
