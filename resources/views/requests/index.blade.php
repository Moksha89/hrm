@extends('layouts.app')

@section('title', 'Requests')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white">Requests</h1>
            <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Manage employee requests and approvals</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative">
                <button onclick="toggleRequestsExportDropdown()" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Export</span>
                </button>
                <div id="requests-export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-20">
                    <a href="{{ route('export.requests', ['format' => 'xlsx']) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                        Export to Excel (.xlsx)
                    </a>
                    <a href="{{ route('export.requests', ['format' => 'csv']) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm">
                        Export to CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4 flex flex-wrap gap-3 items-center">
        <div class="flex-1 min-w-[200px] max-w-md">
            <input type="text" id="requests-search" placeholder="Search by employee, type, or status..." class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
        </div>
        <select id="requests-status-filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <select id="requests-type-filter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 dark:bg-gray-700 dark:text-white text-sm">
            <option value="">All Types</option>
            <option value="salary_hike">Salary Hike</option>
            <option value="promotion">Promotion</option>
            <option value="loan">Loan</option>
            <option value="resign">Resignation</option>
        </select>
    </div>
                @if(session('success'))
                <div class="mb-4 md:mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                    {{ session('success') }}
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 md:mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(auth()->user()->isAdmin() || auth()->user()->isTeamLeader())
                <div class="mb-4 flex justify-end">
                    <button onclick="showCreateRequestModal()" class="inline-flex items-center px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-sm rounded-lg transition-all duration-200 hover:shadow-md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create Request
                    </button>
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <th class="px-3 md:px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($requests as $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            @if($request->type === 'promotion') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300
                                            @elseif($request->type === 'salary_hike') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                            @elseif($request->type === 'loan') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                            @elseif($request->type === 'resign') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap text-xs md:text-sm text-gray-900 dark:text-white">{{ $request->employee->user->name }}</td>
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap text-xs md:text-sm text-gray-600 dark:text-gray-400">{{ $request->requestedBy->name }}</td>
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                            @elseif($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                            @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap text-xs text-gray-600 dark:text-gray-400">{{ $request->created_at->format('M d, Y') }}</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <td class="px-3 md:px-4 py-3 whitespace-nowrap">
                                        @if($request->status === 'pending')
                                        <div class="flex items-center space-x-2">
                                            <form method="POST" action="{{ route('requests.approve', $request->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded text-xs transition-all duration-200 hover:shadow-md">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                            <button onclick="showRejectModal({{ $request->id }})" class="inline-flex items-center px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-xs transition-all duration-200 hover:shadow-md">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Reject
                                            </button>
                                        </div>
                                        @else
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $request->status === 'approved' ? 'Approved' : 'Rejected' }}</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-3 md:px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-xs">
                                        No requests found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($requests->hasPages())
                    <div class="px-4 md:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $requests->links() }}
                    </div>
                    @endif
                </div>

    <div id="createRequestModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto animate-modal-in">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-900 dark:text-white">Create Request</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Submit a new employee request</p>
                </div>
            </div>
            <form method="POST" action="{{ route('requests.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="block text-xs font-medium mb-1.5 text-gray-700 dark:text-gray-300">Request Type</label>
                    <select name="type" id="request_type" required onchange="toggleRequestFields()" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                        <option value="">Select Type</option>
                        <option value="salary_hike">Salary Hike</option>
                        <option value="promotion">Promotion</option>
                        <option value="loan">Loan Request</option>
                        <option value="resign">Resignation</option>
                        <option value="idle">Mark as Idle</option>
                        <option value="remove">Remove from Team</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium mb-1.5 text-gray-700 dark:text-gray-300">Employee</label>
                    <select name="employee_id" id="request_employee_id" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                        <option value="">Select Employee</option>
                        @foreach($employees ?? [] as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->user->name }} ({{ $emp->user->mobile }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="salary_hike_fields" style="display: none;">
                    <div class="mb-2">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Current Salary</label>
                        <input type="number" name="details[current_salary]" id="current_salary" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="mb-2">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">New Salary</label>
                        <input type="number" name="details[new_salary]" id="new_salary" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Reason</label>
                        <textarea name="details[reason]" id="salary_reason" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                </div>

                <div id="promotion_fields" style="display: none;">
                    <div class="mb-2">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Current Position</label>
                        <input type="text" name="details[current_position]" id="current_position" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">New Position</label>
                        <input type="text" name="details[new_position]" id="new_position" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div id="loan_fields" style="display: none;">
                    <div class="mb-2">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Loan Amount</label>
                        <input type="number" name="details[loan_amount]" id="loan_amount" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="mb-3">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Monthly EMI</label>
                        <input type="number" name="details[emi]" id="loan_emi" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div id="reason_field" style="display: none;">
                    <div class="mb-3">
                        <label class="block text-xs font-medium mb-1 text-gray-700 dark:text-gray-300">Reason</label>
                        <textarea name="details[reason]" id="general_reason" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-amber-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideCreateRequestModal()" class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-3 py-1.5 text-sm bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-all duration-200 hover:shadow-md">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-5 max-w-md w-full mx-4 animate-modal-in">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm md:text-base font-semibold text-gray-900 dark:text-white">Reject Request</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Provide a reason for rejection</p>
                </div>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1.5 text-gray-700 dark:text-gray-300">Rejection Reason</label>
                    <textarea name="rejection_reason" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 focus:ring-2 focus:ring-red-500" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideRejectModal()" class="px-3 py-1.5 text-sm bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-3 py-1.5 text-sm bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-200 hover:shadow-md">Reject</button>
                </div>
            </form>
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
        function toggleRequestFields() {
            const requestType = document.getElementById('request_type').value;
            
            document.getElementById('salary_hike_fields').style.display = 'none';
            document.getElementById('promotion_fields').style.display = 'none';
            document.getElementById('loan_fields').style.display = 'none';
            document.getElementById('reason_field').style.display = 'none';
            
            if (requestType === 'salary_hike') {
                document.getElementById('salary_hike_fields').style.display = 'block';
            } else if (requestType === 'promotion') {
                document.getElementById('promotion_fields').style.display = 'block';
            } else if (requestType === 'loan') {
                document.getElementById('loan_fields').style.display = 'block';
            } else if (requestType === 'resign' || requestType === 'idle' || requestType === 'remove') {
                document.getElementById('reason_field').style.display = 'block';
            }
        }

        function showCreateRequestModal() {
            const modal = document.getElementById('createRequestModal');
            modal.style.display = 'flex';
        }

        function hideCreateRequestModal() {
            const modal = document.getElementById('createRequestModal');
            modal.style.display = 'none';
            document.getElementById('request_type').value = '';
            toggleRequestFields();
        }

        function showRejectModal(requestId) {
            const modal = document.getElementById('rejectModal');
            const form = document.getElementById('rejectForm');
            form.action = `/requests/${requestId}/reject`;
            modal.style.display = 'flex';
        }

        function hideRejectModal() {
            const modal = document.getElementById('rejectModal');
            modal.style.display = 'none';
        }
        
        function toggleRequestsExportDropdown() {
            const dropdown = document.getElementById('requests-export-dropdown');
            dropdown.classList.toggle('hidden');
        }
        
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#requests-export-dropdown') && !e.target.closest('button[onclick="toggleRequestsExportDropdown()"]')) {
                document.getElementById('requests-export-dropdown')?.classList.add('hidden');
            }
        });
        
        function filterRequests() {
            const searchTerm = document.getElementById('requests-search')?.value.toLowerCase() || '';
            const statusFilter = document.getElementById('requests-status-filter')?.value.toLowerCase() || '';
            const typeFilter = document.getElementById('requests-type-filter')?.value.toLowerCase() || '';
            
            document.querySelectorAll('tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                const matchesSearch = text.includes(searchTerm);
                const matchesStatus = !statusFilter || text.includes(statusFilter);
                const matchesType = !typeFilter || text.includes(typeFilter.replace('_', ' '));
                row.style.display = (matchesSearch && matchesStatus && matchesType) ? '' : 'none';
            });
        }
        
        document.getElementById('requests-search')?.addEventListener('input', filterRequests);
        document.getElementById('requests-status-filter')?.addEventListener('change', filterRequests);
        document.getElementById('requests-type-filter')?.addEventListener('change', filterRequests);
    </script>
</div>
@endsection
