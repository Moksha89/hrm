<?php

namespace App\Http\Controllers;

use App\Events\DashboardUpdated;
use App\Events\NotificationCreated;
use App\Events\RequestStatusChanged;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\EmployeeActivity;
use App\Models\Notification;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin() || $user->isManager()) {
            $requests = Request::with(['employee.user', 'requestedBy', 'approvedBy'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            $employees = Employee::with('user')->where('status', 'active')->get();
        } elseif ($user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $requests = Request::with(['employee.user', 'requestedBy', 'approvedBy'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            $employees = Employee::with('user')->where('status', 'active')
                ->whereIn('team_id', $assignedTeamIds)
                ->get();
        } else {
            $employeeId = $user->employee?->id;
            $requests = Request::with(['employee.user', 'requestedBy', 'approvedBy'])
                ->where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            $employees = collect();
        }
        
        return view('requests.index', compact('requests', 'employees'));
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:promotion,salary_hike,loan,resign,idle,remove',
            'employee_id' => 'required|exists:employees,id',
            'details' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $newRequest = Request::create([
                'type' => $validated['type'],
                'employee_id' => $validated['employee_id'],
                'requested_by' => auth()->id(),
                'details' => $validated['details'] ?? [],
                'status' => 'pending',
            ]);
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $validated['employee_id'],
                'activity_type' => 'request_created',
                'description' => ucfirst(str_replace('_', ' ', $validated['type'])) . ' request created',
                'data' => [
                    'request_id' => $newRequest->id,
                    'type' => $validated['type'],
                    'details' => $validated['details'] ?? [],
                ],
                'performed_by' => auth()->id(),
            ]);

            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['admin', 'manager']);
            })->get();

            $notifications = [];
            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'request_created',
                    'message' => 'New ' . str_replace('_', ' ', $validated['type']) . ' request created',
                    'data' => ['request_id' => $newRequest->id],
                ]);
                $notifications[] = $notification;
            }

            // Log to audit
            AuditLog::logCreated(
                'requests',
                $newRequest,
                ucfirst(str_replace('_', ' ', $validated['type'])) . ' request created for ' . $newRequest->employee->user->name
            );

            DB::commit();
            
            // Broadcast real-time events
            foreach ($notifications as $notification) {
                event(new NotificationCreated($notification));
            }
            event(new DashboardUpdated('request_created', ['request_id' => $newRequest->id]));
            
            return redirect()->route('requests.index')->with('success', 'Request submitted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create request: ' . $e->getMessage()]);
        }
    }

    public function approve(HttpRequest $request, $id)
    {
        $req = Request::with('employee')->findOrFail($id);
        
        if ($req->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::beginTransaction();
        try {
            $req->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            switch ($req->type) {
                case 'salary_hike':
                    $newSalary = $req->details['new_salary'] ?? null;
                    if ($newSalary) {
                        $req->employee->update(['salary' => $newSalary]);
                        
                        EmployeeActivity::create([
                            'employee_id' => $req->employee_id,
                            'activity_type' => 'salary_hike',
                            'description' => 'Salary increased from ₹' . number_format($req->details['current_salary'], 2) . ' to ₹' . number_format($newSalary, 2),
                            'data' => $req->details,
                            'performed_by' => auth()->id(),
                        ]);
                    }
                    break;
                
                case 'promotion':
                    $newPosition = $req->details['new_position'] ?? null;
                    if ($newPosition) {
                        EmployeeActivity::create([
                            'employee_id' => $req->employee_id,
                            'activity_type' => 'promotion',
                            'description' => 'Promoted from ' . $req->details['current_position'] . ' to ' . $newPosition,
                            'data' => $req->details,
                            'performed_by' => auth()->id(),
                        ]);
                    }
                    break;
                
                case 'loan':
                    $loanAmount = $req->details['loan_amount'] ?? null;
                    if ($loanAmount) {
                        EmployeeActivity::create([
                            'employee_id' => $req->employee_id,
                            'activity_type' => 'loan_request_approved',
                            'description' => 'Loan request of ₹' . number_format($loanAmount, 2) . ' approved',
                            'data' => $req->details,
                            'performed_by' => auth()->id(),
                        ]);
                    }
                    break;
                    
                case 'resign':
                    $req->employee->update(['status' => 'resigned', 'team_id' => null]);
                    
                    EmployeeActivity::create([
                        'employee_id' => $req->employee_id,
                        'activity_type' => 'resigned',
                        'description' => 'Employee resigned',
                        'performed_by' => auth()->id(),
                    ]);
                    break;
                    
                case 'idle':
                    $req->employee->update(['status' => 'inactive', 'team_id' => null]);
                    
                    EmployeeActivity::create([
                        'employee_id' => $req->employee_id,
                        'activity_type' => 'status_changed',
                        'description' => 'Employee marked as inactive',
                        'performed_by' => auth()->id(),
                    ]);
                    break;
                    
                case 'remove':
                    $req->employee->update(['team_id' => null]);
                    
                    EmployeeActivity::create([
                        'employee_id' => $req->employee_id,
                        'activity_type' => 'team_removed',
                        'description' => 'Employee removed from team',
                        'performed_by' => auth()->id(),
                    ]);
                    break;
            }

            $notification = Notification::create([
                'user_id' => $req->requested_by,
                'type' => 'request_approved',
                'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been approved',
                'data' => ['request_id' => $req->id],
            ]);

            // Log to audit
            AuditLog::logApproved(
                'requests',
                $req,
                ucfirst(str_replace('_', ' ', $req->type)) . ' request approved for ' . $req->employee->user->name
            );

            DB::commit();
            
            // Broadcast real-time events
            event(new RequestStatusChanged($req));
            event(new NotificationCreated($notification));
            event(new DashboardUpdated('request_approved', ['request_id' => $req->id]));
            
            return redirect()->route('requests.index')->with('success', 'Request approved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve request: ' . $e->getMessage()]);
        }
    }

    public function reject(HttpRequest $request, $id)
    {
        $req = Request::findOrFail($id);
        
        if ($req->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $req->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'rejection_reason' => $validated['rejection_reason'],
            ]);
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $req->employee_id,
                'activity_type' => 'request_rejected',
                'description' => ucfirst(str_replace('_', ' ', $req->type)) . ' request rejected',
                'data' => [
                    'request_id' => $req->id,
                    'type' => $req->type,
                    'rejection_reason' => $validated['rejection_reason'],
                ],
                'performed_by' => auth()->id(),
            ]);

            $notification = Notification::create([
                'user_id' => $req->requested_by,
                'type' => 'request_rejected',
                'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been rejected',
                'data' => ['request_id' => $req->id, 'reason' => $validated['rejection_reason']],
            ]);

            // Log to audit
            AuditLog::logRejected(
                'requests',
                $req,
                ucfirst(str_replace('_', ' ', $req->type)) . ' request rejected for ' . $req->employee->user->name,
                $validated['rejection_reason']
            );

            DB::commit();
            
            // Broadcast real-time events
            event(new RequestStatusChanged($req));
            event(new NotificationCreated($notification));
            event(new DashboardUpdated('request_rejected', ['request_id' => $req->id]));
            
            return redirect()->route('requests.index')->with('success', 'Request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject request: ' . $e->getMessage()]);
        }
    }

    public function bulkUpdate(HttpRequest $request)
    {
        $validated = $request->validate([
            'request_ids' => 'required|array',
            'request_ids.*' => 'integer|exists:requests,id',
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string',
        ]);

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            $requests = Request::with('employee.user')->whereIn('id', $validated['request_ids'])->get();

            foreach ($requests as $req) {
                if ($req->status !== 'pending') {
                    $errors[] = "Request #{$req->id}: Already processed.";
                    continue;
                }

                if ($validated['action'] === 'approve') {
                    $req->update([
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    // Handle type-specific actions
                    switch ($req->type) {
                        case 'salary_hike':
                            $newSalary = $req->details['new_salary'] ?? null;
                            if ($newSalary) {
                                $req->employee->update(['salary' => $newSalary]);
                            }
                            break;
                        case 'resign':
                            $req->employee->update(['status' => 'resigned', 'team_id' => null]);
                            break;
                        case 'idle':
                            $req->employee->update(['status' => 'inactive', 'team_id' => null]);
                            break;
                        case 'remove':
                            $req->employee->update(['team_id' => null]);
                            break;
                    }

                    Notification::create([
                        'user_id' => $req->requested_by,
                        'type' => 'request_approved',
                        'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been approved',
                        'data' => ['request_id' => $req->id],
                    ]);

                    AuditLog::log(
                        'bulk_approved',
                        'requests',
                        ucfirst(str_replace('_', ' ', $req->type)) . ' request bulk approved for ' . $req->employee->user->name,
                        $req,
                        ['status' => 'pending'],
                        ['status' => 'approved']
                    );
                } else {
                    $req->update([
                        'status' => 'rejected',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                        'rejection_reason' => $validated['rejection_reason'],
                    ]);

                    Notification::create([
                        'user_id' => $req->requested_by,
                        'type' => 'request_rejected',
                        'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been rejected',
                        'data' => ['request_id' => $req->id, 'reason' => $validated['rejection_reason']],
                    ]);

                    AuditLog::log(
                        'bulk_rejected',
                        'requests',
                        ucfirst(str_replace('_', ' ', $req->type)) . ' request bulk rejected for ' . $req->employee->user->name,
                        $req,
                        ['status' => 'pending'],
                        ['status' => 'rejected', 'reason' => $validated['rejection_reason']]
                    );
                }

                $successCount++;
            }

            DB::commit();

            $actionText = $validated['action'] === 'approve' ? 'approved' : 'rejected';
            $message = "Successfully {$actionText} {$successCount} request(s).";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->back()->with($successCount > 0 ? 'success' : 'error', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to process bulk action: ' . $e->getMessage()]);
        }
    }
}
