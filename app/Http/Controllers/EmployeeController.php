<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\BankAccount;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Employee::class);
        
        $user = auth()->user();
        
        if ($user->isAdmin() || $user->isHR() || $user->isAccountant()) {
            $employees = Employee::with(['user', 'team'])->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $employees = Employee::with(['user', 'team'])
                ->whereIn('team_id', $assignedTeamIds)
                ->get();
        } else {
            $employees = collect();
        }
        
        $teams = Team::all();
        
        return view('employees.index', compact('employees', 'teams'));
    }

    public function show($id)
    {
        $employee = Employee::with(['user', 'team', 'bankAccounts', 'documents', 'loans.payments', 'salaryPayments', 'activities.performedBy'])->findOrFail($id);
        
        $this->authorize('view', $employee);
        
        return view('employees.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::with(['user', 'bankAccounts'])->findOrFail($id);
        
        $this->authorize('update', $employee);
        
        $teams = Team::all();
        
        return view('employees.edit', compact('employee', 'teams'));
    }

    public function update(StoreEmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $this->authorize('update', $employee);
        
        try {
            DB::beginTransaction();
            
            $teamId = $request->team_id;
            if ($teamId && auth()->user()->isTeamLeader() && !auth()->user()->isAdmin()) {
                $assignedTeamIds = auth()->user()->assignedTeams()->pluck('teams.id');
                if (!$assignedTeamIds->contains($teamId)) {
                    return back()->withErrors(['error' => 'You can only assign employees to your own teams.'])->withInput();
                }
            }

            $employee->user->update([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
            ]);

            $oldTeamId = $employee->team_id;
            $employee->update([
                'team_id' => $request->team_id,
                'salary' => $request->salary,
                'loan' => $request->loan,
                'emi' => $request->emi,
                'aadhar' => $request->aadhar,
                'pan' => $request->pan,
                'dob' => $request->dob,
                'date_of_joining' => $request->date_of_joining,
            ]);

            if ($oldTeamId != $request->team_id) {
                \App\Models\EmployeeActivity::create([
                    'employee_id' => $employee->id,
                    'activity_type' => 'team_changed',
                    'description' => 'Employee team changed',
                    'data' => [
                        'old_team_id' => $oldTeamId,
                        'new_team_id' => $request->team_id,
                    ],
                    'performed_by' => auth()->id(),
                ]);
            }

            if ($request->has('bank_accounts')) {
                $employee->bankAccounts()->delete();
                foreach ($request->bank_accounts as $bankAccount) {
                    BankAccount::create([
                        'employee_id' => $employee->id,
                        'account_holder_name' => $bankAccount['account_holder_name'],
                        'account_number' => $bankAccount['account_number'],
                        'ifsc_code' => $bankAccount['ifsc_code'],
                        'bank_name' => $bankAccount['bank_name'],
                        'is_default' => isset($bankAccount['is_default']) ? (bool)$bankAccount['is_default'] : false,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('employees.show', $employee->id)->with('success', 'Employee updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update employee: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        
        $this->authorize('delete', $employee);
        
        try {
            DB::beginTransaction();
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $employee->id,
                'activity_type' => 'deleted',
                'description' => 'Employee record deleted',
                'data' => [
                    'name' => $employee->user->name,
                    'mobile' => $employee->user->mobile,
                ],
                'performed_by' => auth()->id(),
            ]);
            
            $employee->bankAccounts()->delete();
            $employee->documents()->delete();
            $employee->user()->delete();
            $employee->delete();
            
            DB::commit();
            
            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete employee: ' . $e->getMessage()]);
        }
    }

    public function store(StoreEmployeeRequest $request)
    {
        $this->authorize('create', Employee::class);
        
        try {
            DB::beginTransaction();
            
            $teamId = $request->team_id;
            if ($teamId && auth()->user()->isTeamLeader() && !auth()->user()->isAdmin()) {
                $assignedTeamIds = auth()->user()->assignedTeams()->pluck('teams.id');
                if (!$assignedTeamIds->contains($teamId)) {
                    return back()->withErrors(['error' => 'You can only assign employees to your own teams.'])->withInput();
                }
            }

            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => bcrypt('Password@00'),
                'password_changed_at' => null,
            ]);
            
            $employeeRole = \App\Models\Role::where('slug', 'employee')->first();
            if ($employeeRole) {
                $user->roles()->attach($employeeRole->id);
            }

            $employee = Employee::create([
                'user_id' => $user->id,
                'team_id' => $request->team_id,
                'salary' => $request->salary,
                'loan' => $request->loan,
                'emi' => $request->emi,
                'aadhar' => $request->aadhar,
                'pan' => $request->pan,
                'dob' => $request->dob,
                'date_of_joining' => $request->date_of_joining,
            ]);

            foreach ($request->bank_accounts as $bankAccount) {
                BankAccount::create([
                    'employee_id' => $employee->id,
                    'account_holder_name' => $bankAccount['account_holder_name'],
                    'account_number' => $bankAccount['account_number'],
                    'ifsc_code' => $bankAccount['ifsc_code'],
                    'bank_name' => $bankAccount['bank_name'],
                    'is_default' => isset($bankAccount['is_default']) ? (bool)$bankAccount['is_default'] : false,
                ]);
            }

            if ($request->has('documents')) {
                foreach ($request->documents as $doc) {
                    if (isset($doc['file'])) {
                        $file = $doc['file'];
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('documents', $filename, 'local');

                        Document::create([
                            'employee_id' => $employee->id,
                            'file_path' => $filePath,
                            'document_name' => $doc['document_name'],
                            'expiry_date' => $doc['expiry_date'] ?? null,
                        ]);
                    }
                }
            }
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $employee->id,
                'activity_type' => 'joined',
                'description' => 'Employee joined the organization',
                'data' => [
                    'team_id' => $teamId,
                    'salary' => $request->salary,
                    'date_of_joining' => $request->date_of_joining,
                ],
                'performed_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create employee: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateStatus($id, $status)
    {
        $employee = Employee::findOrFail($id);
        
        $oldStatus = $employee->status;
        
        if (in_array($status, ['resigned', 'inactive'])) {
            $employee->team_id = null;
        }
        
        $employee->status = $status;
        $employee->save();
        
        \App\Models\EmployeeActivity::create([
            'employee_id' => $employee->id,
            'activity_type' => 'status_changed',
            'description' => 'Employee status changed from ' . $oldStatus . ' to ' . $status,
            'data' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee status updated successfully!');
    }
}
