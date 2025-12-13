@extends('layouts.app')

@section('title', $team->name)

@section('content')
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('teams.index') }}" class="text-amber-600 hover:text-amber-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ $team->name }}</h1>
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $team->employees->count() }} employees</p>
                        </div>
                    </div>

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex space-x-8">
                            <button onclick="showTab('employees')" id="employees-tab" class="px-3 py-2 border-b-2 border-amber-500 text-amber-600 dark:text-amber-400 font-medium">
                                Employees
                            </button>
                            <button onclick="showTab('salary')" id="salary-tab" class="px-3 py-2 border-b-2 border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:border-gray-300">
                                Salary Management
                            </button>
                        </nav>
                    </div>

                    <div id="employees-content" class="tab-content">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-4 mb-6">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 md:p-4">
                            <div class="flex flex-col min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">Total Employees</p>
                                <p class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $team->employees->count() }}</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 md:p-4">
                            <div class="flex flex-col min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">Gross Salary</p>
                                <p class="text-lg md:text-xl lg:text-2xl font-bold text-gray-900 dark:text-white mt-1 break-all">₹{{ number_format($totalSalary, 0) }}</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 md:p-4">
                            <div class="flex flex-col min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">Net Pay</p>
                                <p class="text-lg md:text-xl lg:text-2xl font-bold text-green-600 dark:text-green-400 mt-1 break-all">₹{{ number_format($totalNetPay, 0) }}</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 md:p-4">
                            <div class="flex flex-col min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">Total EMI</p>
                                <p class="text-lg md:text-xl lg:text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1 break-all">₹{{ number_format($totalEmi, 0) }}</p>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-3 md:p-4 col-span-2 md:col-span-1">
                            <div class="flex flex-col min-w-0">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">Final Pay</p>
                                <p class="text-lg md:text-xl lg:text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1 break-all">₹{{ number_format($totalFinalPay, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-3 items-center justify-between">
                        <div class="flex-1 min-w-[200px] max-w-md">
                            <input type="text" id="team-member-search" placeholder="Search by name or mobile..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
                        </div>
                        <div class="flex items-center space-x-3">
                            <select id="team-member-sort" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">Sort by</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                <option value="salary_high">Salary (High-Low)</option>
                                <option value="salary_low">Salary (Low-High)</option>
                            </select>
                            <div class="relative">
                                <button onclick="toggleTeamExportDropdown()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    <span>Export</span>
                                </button>
                                <div id="team-export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20">
                                    <a href="{{ route('export.employees', ['format' => 'xlsx', 'team_id' => $team->id]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                        Export to Excel (.xlsx)
                                    </a>
                                    <a href="{{ route('export.employees', ['format' => 'csv', 'team_id' => $team->id]) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                                        Export to CSV
                                    </a>
                                </div>
                            </div>
                            @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader() || auth()->user()->isHR())
                            <button id="create-employee-btn" class="bg-amber-500 hover:bg-amber-400 text-black px-3 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>Create Employee</span>
                            </button>
                            @endif
                        </div>
                    </div>

                    @if($unassignedEmployees->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white mb-4">Assign Employee</h2>
                        <form action="{{ route('teams.assign', $team->id) }}" method="POST" class="flex items-end space-x-4">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Employee</label>
                                <select name="employee_id" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Choose an employee...</option>
                                    @foreach($unassignedEmployees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->user->name }} ({{ $employee->user->mobile }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors">
                                Assign
                            </button>
                        </form>
                    </div>
                    @endif

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Team Members</h2>
                        </div>
                        
                        @if($team->employees->count() > 0)
                        <div id="team-members-container" class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($team->employees as $employee)
                            @php
                                $workingDay = $employee->workingDays->first();
                                $currentWorkingDays = $workingDay ? $workingDay->working_days : 30;
                                $netPay = $employee->getNetPay($currentMonth, $currentYear);
                                $monthlyEmi = $employee->getTotalMonthlyEmi();
                                $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
                            @endphp
                            <div class="team-member-card p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                 data-name="{{ strtolower($employee->user->name) }}"
                                 data-mobile="{{ $employee->user->mobile }}"
                                 data-salary="{{ $employee->salary ?? 0 }}">
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center justify-center w-12 h-12 bg-amber-500 rounded-full text-white font-semibold text-base md:text-lg">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Gross Salary</p>
                                        <p class="font-semibold text-gray-900 dark:text-white">₹{{ number_format($employee->salary ?? 0, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <form action="{{ route('teams.updateWorkingDays', $team->id) }}" method="POST" class="inline-flex items-center space-x-2">
                                            @csrf
                                            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
                                            <input type="hidden" name="month" value="{{ $currentMonth }}">
                                            <input type="hidden" name="year" value="{{ $currentYear }}">
                                            <div>
                                                <label class="text-xs text-gray-600 dark:text-gray-400 block mb-1">Working Days</label>
                                                <input 
                                                    type="number" 
                                                    name="working_days" 
                                                    value="{{ $currentWorkingDays }}" 
                                                    min="0" 
                                                    max="31" 
                                                    class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white"
                                                    onchange="this.form.submit()"
                                                >
                                            </div>
                                        </form>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Net Pay</p>
                                        <p class="font-semibold text-green-600 dark:text-green-400">₹{{ number_format($netPay, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">EMI Deduction</p>
                                        <p class="font-semibold text-orange-600 dark:text-orange-400">₹{{ number_format($monthlyEmi, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Final Pay</p>
                                        <p class="font-semibold text-amber-600 dark:text-amber-400">₹{{ number_format($finalPay, 2) }}</p>
                                    </div>
                                    <form action="{{ route('teams.remove', [$team->id, $employee->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No team members yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Assign employees to this team to get started.</p>
                        </div>
                        @endif
                    </div>
                </div>
                    </div>

                    <div id="salary-content" class="tab-content hidden">
                        @php
                            $currentMonth = now()->month;
                            $currentYear = now()->year;
                        @endphp
                        
                        <form action="{{ route('teams.updateBulkSalarySettings', $team->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $currentMonth }}">
                            <input type="hidden" name="year" value="{{ $currentYear }}">
                            
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                    <div>
                                        <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">Salary Settings - {{ date('F Y', mktime(0, 0, 0, $currentMonth, 1, $currentYear)) }}</h2>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Update working days and EMI deduction settings for all team members</p>
                                    </div>
                                    <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white px-6 py-2 rounded-lg transition-colors">
                                        Save All Changes
                                    </button>
                                </div>
                                
                                @if($team->employees->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Employee</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gross Salary</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Working Days</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Net Pay</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Deduct EMI</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">EMI Amount</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Final Pay</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($team->employees as $employee)
                                            @php
                                                $workingDayRecord = $employee->workingDays()
                                                    ->where('month', $currentMonth)
                                                    ->where('year', $currentYear)
                                                    ->first();
                                                $currentWorkingDays = $workingDayRecord ? $workingDayRecord->working_days : 30;
                                                $shouldDeductEmi = $workingDayRecord ? ($workingDayRecord->deduct_emi ?? true) : true;
                                                $netPay = $employee->getNetPay($currentMonth, $currentYear);
                                                $monthlyEmi = $employee->getTotalMonthlyEmi();
                                                $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
                                            @endphp
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex items-center justify-center w-10 h-10 bg-amber-500 rounded-full text-white font-semibold text-sm mr-3">
                                                            {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $employee->user->name }}</div>
                                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                    ₹{{ number_format($employee->salary ?? 0, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <input type="number" 
                                                           name="employees[{{ $employee->id }}][working_days]" 
                                                           value="{{ $currentWorkingDays }}" 
                                                           min="0" 
                                                           max="31" 
                                                           class="w-20 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                                        ₹{{ number_format($netPay, 2) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="checkbox" 
                                                               name="employees[{{ $employee->id }}][deduct_emi]" 
                                                               value="1"
                                                               {{ $shouldDeductEmi ? 'checked' : '' }}
                                                               class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500"></div>
                                                    </label>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($monthlyEmi > 0)
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                                            ₹{{ number_format($monthlyEmi, 2) }}
                                                        </span>
                                                        <input type="number" 
                                                               name="employees[{{ $employee->id }}][emi_override_amount]" 
                                                               value="{{ $workingDayRecord?->emi_override_amount ?? '' }}" 
                                                               placeholder="Override"
                                                               step="0.01"
                                                               min="0"
                                                               class="w-24 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                                                    </div>
                                                    @else
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">₹0.00</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-semibold text-amber-600 dark:text-amber-400">
                                                        ₹{{ number_format($finalPay, 2) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="p-12 text-center text-gray-600 dark:text-gray-400">
                                    No employees in this team
                                </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            document.getElementById(tabName + '-content').classList.remove('hidden');
            
            document.querySelectorAll('[id$="-tab"]').forEach(btn => {
                btn.classList.remove('border-amber-500', 'text-amber-600', 'dark:text-amber-400', 'font-medium');
                btn.classList.add('border-transparent', 'text-gray-600', 'dark:text-gray-400');
            });
            
            document.getElementById(tabName + '-tab').classList.add('border-amber-500', 'text-amber-600', 'dark:text-amber-400', 'font-medium');
            document.getElementById(tabName + '-tab').classList.remove('border-transparent', 'text-gray-600', 'dark:text-gray-400');
        }
        
        function toggleTeamExportDropdown() {
            const dropdown = document.getElementById('team-export-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#team-export-dropdown') && !e.target.closest('button[onclick="toggleTeamExportDropdown()"]')) {
                document.getElementById('team-export-dropdown')?.classList.add('hidden');
            }
        });
        
        const searchInput = document.getElementById('team-member-search');
        const sortSelect = document.getElementById('team-member-sort');
        const container = document.getElementById('team-members-container');
        
        if (searchInput && container) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const cards = container.querySelectorAll('.team-member-card');
                
                cards.forEach(card => {
                    const name = card.dataset.name || '';
                    const mobile = card.dataset.mobile || '';
                    const matches = name.includes(searchTerm) || mobile.includes(searchTerm);
                    card.style.display = matches ? '' : 'none';
                });
            });
        }
        
        if (sortSelect && container) {
            sortSelect.addEventListener('change', function() {
                const sortValue = this.value;
                if (!sortValue) return;
                
                const cards = Array.from(container.querySelectorAll('.team-member-card'));
                
                cards.sort((a, b) => {
                    if (sortValue === 'name_asc') {
                        return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                    } else if (sortValue === 'name_desc') {
                        return (b.dataset.name || '').localeCompare(a.dataset.name || '');
                    } else if (sortValue === 'salary_high') {
                        return parseFloat(b.dataset.salary || 0) - parseFloat(a.dataset.salary || 0);
                    } else if (sortValue === 'salary_low') {
                        return parseFloat(a.dataset.salary || 0) - parseFloat(b.dataset.salary || 0);
                    }
                    return 0;
                });
                
                cards.forEach(card => container.appendChild(card));
            });
        }
        
        // Create Employee Modal
        const createEmployeeModal = document.getElementById('create-employee-modal');
        const createEmployeeBtn = document.getElementById('create-employee-btn');
        const closeCreateEmployeeModalBtn = document.getElementById('close-create-employee-modal-btn');
        const cancelCreateEmployeeBtn = document.getElementById('cancel-create-employee-btn');
        
        createEmployeeBtn?.addEventListener('click', () => {
            createEmployeeModal?.classList.remove('hidden');
            if (document.querySelectorAll('.team-bank-account-item').length === 0) {
                addTeamBankAccount();
            }
        });
        
        closeCreateEmployeeModalBtn?.addEventListener('click', () => {
            createEmployeeModal?.classList.add('hidden');
        });
        
        cancelCreateEmployeeBtn?.addEventListener('click', () => {
            createEmployeeModal?.classList.add('hidden');
        });
        
        let teamBankAccountIndex = 0;
        
        function addTeamBankAccount() {
            const container = document.getElementById('team-bank-accounts-container');
            const isFirst = container.children.length === 0;
            
            const bankAccountHtml = `
                <div class="team-bank-account-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">Bank Account ${teamBankAccountIndex + 1}</h4>
                        ${!isFirst ? '<button type="button" class="remove-team-bank-account text-red-600 hover:text-red-700 text-sm">Remove</button>' : ''}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Holder Name *</label>
                            <input type="text" name="bank_accounts[${teamBankAccountIndex}][account_holder_name]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number *</label>
                            <input type="text" name="bank_accounts[${teamBankAccountIndex}][account_number]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">IFSC Code *</label>
                            <input type="text" name="bank_accounts[${teamBankAccountIndex}][ifsc_code]" required pattern="[A-Z]{4}0[A-Z0-9]{6}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Name *</label>
                            <input type="text" name="bank_accounts[${teamBankAccountIndex}][bank_name]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center space-x-2">
                                <input type="checkbox" name="bank_accounts[${teamBankAccountIndex}][is_default]" value="1" ${isFirst ? 'checked' : ''} class="rounded border-gray-300 dark:border-gray-600 text-amber-500 focus:ring-amber-500">
                                <span class="text-sm text-gray-700 dark:text-gray-300">Set as default account</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', bankAccountHtml);
            teamBankAccountIndex++;
            
            container.querySelectorAll('.remove-team-bank-account').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.closest('.team-bank-account-item').remove();
                });
            });
        }
        
        document.getElementById('add-team-bank-account-btn')?.addEventListener('click', addTeamBankAccount);
    </script>
    
    <!-- Create Employee Modal -->
    @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader() || auth()->user()->isHR())
    <div id="create-employee-modal" class="hidden fixed inset-0 bg-black/30 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between z-10">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Create New Employee</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">This employee will be assigned to <span class="font-medium text-amber-600">{{ $team->name }}</span></p>
                </div>
                <button id="close-create-employee-modal-btn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <input type="hidden" name="team_id" value="{{ $team->id }}">

                <div class="mb-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number *</label>
                            <input type="text" name="mobile" required pattern="[0-9]{10}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team</label>
                            <input type="text" value="{{ $team->name }}" disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Financial Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Salary</label>
                            <input type="number" name="salary" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loan</label>
                            <input type="number" name="loan" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">EMI</label>
                            <input type="number" name="emi" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Aadhar Number</label>
                            <input type="text" name="aadhar" pattern="[0-9]{12}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Number</label>
                            <input type="text" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                            <input type="date" name="dob" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bank Accounts *</h3>
                        <button type="button" id="add-team-bank-account-btn" class="text-amber-600 hover:text-amber-500 dark:text-amber-400 text-sm font-medium">
                            + Add Bank Account
                        </button>
                    </div>
                    <div id="team-bank-accounts-container" class="space-y-4">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancel-create-employee-btn" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-400 text-black rounded-lg transition-colors">
                        Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endsection
