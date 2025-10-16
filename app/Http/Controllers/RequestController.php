<?php

namespace App\Http\Controllers;

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
        } elseif ($user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $requests = Request::with(['employee.user', 'requestedBy', 'approvedBy'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            $employeeId = $user->employee?->id;
            $requests = Request::with(['employee.user', 'requestedBy', 'approvedBy'])
                ->where('employee_id', $employeeId)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        }
        
        return view('requests.index', compact('requests'));
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

            $admins = \App\Models\User::whereHas('roles', function ($q) {
                $q->whereIn('slug', ['admin', 'manager']);
            })->get();

            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'request_created',
                    'message' => 'New ' . str_replace('_', ' ', $validated['type']) . ' request created',
                    'data' => ['request_id' => $newRequest->id],
                ]);
            }

            DB::commit();
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

            Notification::create([
                'user_id' => $req->requested_by,
                'type' => 'request_approved',
                'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been approved',
                'data' => ['request_id' => $req->id],
            ]);

            DB::commit();
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

            Notification::create([
                'user_id' => $req->requested_by,
                'type' => 'request_rejected',
                'message' => 'Your ' . str_replace('_', ' ', $req->type) . ' request has been rejected',
                'data' => ['request_id' => $req->id, 'reason' => $validated['rejection_reason']],
            ]);

            DB::commit();
            return redirect()->route('requests.index')->with('success', 'Request rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject request: ' . $e->getMessage()]);
        }
    }
}
