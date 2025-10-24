<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Employees</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] dark:bg-gray-900 overflow-hidden">
    <div class="flex h-screen">
        <x-sidebar />

        <div class="flex-1 flex flex-col ml-20">
            <x-header breadcrumb="Employees" />

            <main class="flex-1 overflow-y-auto mt-16 p-6">
                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Employees</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your organization's employees</p>
                        </div>
                        <button id="add-employee-btn" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span>Add Employee</span>
                        </button>
                    </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($employees as $employee)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-4 flex-1">
                                    <div class="flex items-center justify-center w-12 h-12 bg-teal-600 rounded-full text-white font-semibold text-base md:text-lg flex-shrink-0">
                                        {{ strtoupper(substr($employee->user->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">{{ $employee->user->name }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->mobile }}</p>
                                        <p class="text-sm text-teal-600 dark:text-teal-400 mt-1">
                                            {{ $employee->team ? $employee->team->name : 'No Team' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="relative">
                                    <button onclick="toggleStatusDropdown({{ $employee->id }})" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                    <div id="status-dropdown-{{ $employee->id }}" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                        <div class="py-1">
                                            <div class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                                Status
                                            </div>
                                            <form action="{{ route('employees.updateStatus', [$employee->id, 'active']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-green-600 dark:text-green-400 flex items-center space-x-2">
                                                    <span class="w-2 h-2 bg-green-600 rounded-full"></span>
                                                    <span>Active</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('employees.updateStatus', [$employee->id, 'inactive']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 flex items-center space-x-2">
                                                    <span class="w-2 h-2 bg-gray-600 rounded-full"></span>
                                                    <span>Inactive</span>
                                                </button>
                                            </form>
                                            <form action="{{ route('employees.updateStatus', [$employee->id, 'resigned']) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-red-600 dark:text-red-400 flex items-center space-x-2">
                                                    <span class="w-2 h-2 bg-red-600 rounded-full"></span>
                                                    <span>Resigned</span>
                                                </button>
                                            </form>
                                        </div>
                                        <div class="border-t border-gray-200 dark:border-gray-700 py-1">
                                            <div class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase">
                                                Actions
                                            </div>
                                            <a href="{{ route('payments.employee.detail', $employee->id) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                View Details
                                            </a>
                                            <a href="{{ route('loans.employee', $employee->id) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                Manage Loans
                                            </a>
                                            @if($employee->team)
                                            <a href="{{ route('teams.show', $employee->team->id) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                View Team
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-start">
                                @if($employee->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-800 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                    Active
                                </span>
                                @elseif($employee->status === 'inactive')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900/20 text-gray-800 dark:text-gray-300">
                                    <span class="w-1.5 h-1.5 bg-gray-600 rounded-full mr-1.5"></span>
                                    Inactive
                                </span>
                                @elseif($employee->status === 'resigned')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-800 dark:text-red-300">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                    Resigned
                                </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full bg-white dark:bg-gray-800 rounded-lg shadow p-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <h3 class="text-base md:text-lg font-medium text-gray-900 dark:text-white mb-2">No employees yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Get started by adding your first employee.</p>
                            <button onclick="document.getElementById('add-employee-btn').click()" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors">
                                Add Employee
                            </button>
                        </div>
                        @endforelse
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div id="add-employee-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between z-10">
                <h2 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Add New Employee</h2>
                <button id="close-modal-btn" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form id="employee-form" action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf

                <div class="mb-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mobile Number *</label>
                            <input type="text" name="mobile" required pattern="[0-9]{10}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Team</label>
                            <select name="team_id" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Team (Optional)</option>
                                @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Financial Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Salary</label>
                            <input type="number" name="salary" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Loan</label>
                            <input type="number" name="loan" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">EMI</label>
                            <input type="number" name="emi" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Aadhar Number</label>
                            <input type="text" name="aadhar" pattern="[0-9]{12}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">PAN Number</label>
                            <input type="text" name="pan" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                            <input type="date" name="dob" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Joining</label>
                            <input type="date" name="date_of_joining" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Bank Accounts *</h3>
                        <button type="button" id="add-bank-account-btn" class="text-teal-600 hover:text-teal-700 dark:text-teal-400 text-sm font-medium">
                            + Add Bank Account
                        </button>
                    </div>
                    <div id="bank-accounts-container" class="space-y-4">
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-white">Documents</h3>
                        <button type="button" id="add-document-btn" class="text-teal-600 hover:text-teal-700 dark:text-teal-400 text-sm font-medium">
                            + Add Document
                        </button>
                    </div>
                    <div id="documents-container" class="space-y-4">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" id="cancel-btn" class="px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors">
                        Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const profileMenuBtn = document.getElementById('profile-menu-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        function toggleSidebar() {
            sidebar.classList.toggle('collapsed');
            const isCollapsed = sidebar.classList.contains('collapsed');
            
            if (isCollapsed) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.remove('hidden'));
            }
            
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }

        function initSidebar() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            const isMobile = window.innerWidth < 1024;
            
            if (isCollapsed || isMobile) {
                sidebar.classList.add('collapsed', 'w-20');
                sidebar.classList.remove('w-64');
                document.querySelectorAll('.sidebar-text').forEach(el => el.classList.add('hidden'));
            }
        }

        sidebarToggle?.addEventListener('click', toggleSidebar);
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleSidebar);
        }

        profileMenuBtn?.addEventListener('click', () => {
            profileDropdown?.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (profileMenuBtn && profileDropdown && !profileMenuBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
        });

        initSidebar();

        const modal = document.getElementById('add-employee-modal');
        const addEmployeeBtn = document.getElementById('add-employee-btn');
        const closeModalBtn = document.getElementById('close-modal-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        addEmployeeBtn?.addEventListener('click', () => {
            modal?.classList.remove('hidden');
            if (document.querySelectorAll('.bank-account-item').length === 0) {
                addBankAccount();
            }
        });

        closeModalBtn?.addEventListener('click', () => {
            modal?.classList.add('hidden');
        });

        cancelBtn?.addEventListener('click', () => {
            modal?.classList.add('hidden');
        });

        let bankAccountIndex = 0;

        function addBankAccount() {
            const container = document.getElementById('bank-accounts-container');
            const isFirst = container.children.length === 0;
            
            const bankAccountHtml = `
                <div class="bank-account-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">Bank Account ${bankAccountIndex + 1}</h4>
                        ${!isFirst ? '<button type="button" class="remove-bank-account text-red-600 hover:text-red-700 text-sm">Remove</button>' : ''}
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Holder Name *</label>
                            <input type="text" name="bank_accounts[${bankAccountIndex}][account_holder_name]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account Number *</label>
                            <input type="text" name="bank_accounts[${bankAccountIndex}][account_number]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">IFSC Code *</label>
                            <input type="text" name="bank_accounts[${bankAccountIndex}][ifsc_code]" required pattern="[A-Z]{4}0[A-Z0-9]{6}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bank Name *</label>
                            <input type="text" name="bank_accounts[${bankAccountIndex}][bank_name]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="radio" name="default_bank" value="${bankAccountIndex}" ${isFirst ? 'checked' : ''} class="mr-2">
                            <span class="text-sm text-gray-700 dark:text-gray-300">Set as default account</span>
                        </label>
                    </div>
                    <input type="hidden" name="bank_accounts[${bankAccountIndex}][is_default]" value="0" class="is-default-input">
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', bankAccountHtml);
            bankAccountIndex++;
        }

        document.getElementById('add-bank-account-btn')?.addEventListener('click', addBankAccount);

        document.getElementById('bank-accounts-container')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-bank-account')) {
                e.target.closest('.bank-account-item').remove();
            }
        });

        document.getElementById('bank-accounts-container')?.addEventListener('change', (e) => {
            if (e.target.name === 'default_bank') {
                document.querySelectorAll('.is-default-input').forEach(input => {
                    input.value = '0';
                });
                const selectedItem = e.target.closest('.bank-account-item');
                selectedItem.querySelector('.is-default-input').value = '1';
            }
        });

        let documentIndex = 0;

        function addDocument() {
            const container = document.getElementById('documents-container');
            
            const documentHtml = `
                <div class="document-item border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900 dark:text-white">Document ${documentIndex + 1}</h4>
                        <button type="button" class="remove-document text-red-600 hover:text-red-700 text-sm">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document File *</label>
                            <input type="file" name="documents[${documentIndex}][file]" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Document Name *</label>
                            <input type="text" name="documents[${documentIndex}][document_name]" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                            <input type="date" name="documents[${documentIndex}][expiry_date]" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', documentHtml);
            documentIndex++;
        }

        document.getElementById('add-document-btn')?.addEventListener('click', addDocument);

        document.getElementById('documents-container')?.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-document')) {
                e.target.closest('.document-item').remove();
            }
        });

        function toggleStatusDropdown(employeeId) {
            const dropdown = document.getElementById(`status-dropdown-${employeeId}`);
            document.querySelectorAll('[id^="status-dropdown-"]').forEach(d => {
                if (d !== dropdown) d.classList.add('hidden');
            });
            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleStatusDropdown"]') && !event.target.closest('[id^="status-dropdown-"]')) {
                document.querySelectorAll('[id^="status-dropdown-"]').forEach(d => d.classList.add('hidden'));
            }
        });

        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const html = document.documentElement;

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        darkModeToggle?.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });
    </script>
</body>
</html>
