@extends('layouts.app')

@section('title', 'Requests')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Requests</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Manage employee requests and approvals</p>
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
                <div class="mb-4 md:mb-6 flex justify-end">
                    <button onclick="showCreateRequestModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm md:text-base">
                        Create Request
                    </button>
                </div>
                @endif

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Requested By</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <th class="px-4 md:px-6 py-3 text-left text-xs md:text-sm font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($requests as $request)
                                <tr>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        <span class="px-2 py-1 text-xs md:text-sm font-semibold rounded-full
                                            @if($request->type === 'promotion') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200
                                            @elseif($request->type === 'salary_hike') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @elseif($request->type === 'loan') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                            @elseif($request->type === 'resign') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $request->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->employee->user->name }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->requestedBy->name }}</td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        <span class="px-2 py-1 text-xs md:text-sm font-semibold rounded-full
                                            @if($request->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                            @elseif($request->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                            @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                            @endif">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">{{ $request->created_at->format('M d, Y') }}</td>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isManager())
                                    <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm md:text-base">
                                        @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('requests.approve', $request->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs md:text-sm">Approve</button>
                                        </form>
                                        <button onclick="showRejectModal({{ $request->id }})" class="ml-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs md:text-sm">Reject</button>
                                        @else
                                        <span class="text-gray-500 dark:text-gray-400 text-xs md:text-sm">{{ $request->status === 'approved' ? 'Approved' : 'Rejected' }}</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-4 md:px-6 py-8 text-center text-gray-500 dark:text-gray-400 text-sm md:text-base">
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

    <div id="createRequestModal"class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg md:text-xl font-semibold mb-4">Create Request</h3>
            <form method="POST" action="{{ route('requests.store') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm md:text-base font-medium mb-2">Request Type</label>
                    <select name="type" id="request_type" required onchange="toggleRequestFields()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm md:text-base">
                        <option value="">Select Type</option>
                        <option value="salary_hike">Salary Hike</option>
                        <option value="promotion">Promotion</option>
                        <option value="loan">Loan Request</option>
                        <option value="resign">Resignation</option>
                        <option value="idle">Mark as Idle</option>
                        <option value="remove">Remove from Team</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm md:text-base font-medium mb-2">Employee</label>
                    <select name="employee_id" id="request_employee_id" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm md:text-base">
                        <option value="">Select Employee</option>
                        @foreach($employees ?? [] as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->user->name }} ({{ $emp->user->mobile }})</option>
                        @endforeach
                    </select>
                </div>

                <div id="salary_hike_fields" style="display: none;">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Current Salary</label>
                        <input type="number" name="details[current_salary]" id="current_salary" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">New Salary</label>
                        <input type="number" name="details[new_salary]" id="new_salary" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Reason</label>
                        <textarea name="details[reason]" id="salary_reason" rows="2" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm"></textarea>
                    </div>
                </div>

                <div id="promotion_fields" style="display: none;">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Current Position</label>
                        <input type="text" name="details[current_position]" id="current_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">New Position</label>
                        <input type="text" name="details[new_position]" id="new_position" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                </div>

                <div id="loan_fields" style="display: none;">
                    <div class="mb-3">
                        <label class="block text-sm font-medium mb-1">Loan Amount</label>
                        <input type="number" name="details[loan_amount]" id="loan_amount" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Monthly EMI</label>
                        <input type="number" name="details[emi]" id="loan_emi" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm">
                    </div>
                </div>

                <div id="reason_field" style="display: none;">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Reason</label>
                        <textarea name="details[reason]" id="general_reason" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideCreateRequestModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 rounded-lg text-sm md:text-base">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm md:text-base">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg md:text-xl font-semibold mb-4">Reject Request</h3>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm md:text-base font-medium mb-2">Rejection Reason</label>
                    <textarea name="rejection_reason" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm md:text-base" rows="4"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 rounded-lg text-sm md:text-base">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm md:text-base">Reject</button>
                </div>
            </form>
        </div>
    </div>

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
    </script>
</div>
@endsection
