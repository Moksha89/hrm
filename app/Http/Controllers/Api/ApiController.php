<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\Request as RequestModel;
use App\Models\SalaryPayment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{
    /**
     * Login and get API token
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'mobile' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke existing tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Logout and revoke token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'email' => $user->email,
                    'roles' => $user->roles->pluck('name'),
                ],
                'employee' => $employee ? [
                    'id' => $employee->id,
                    'team' => $employee->team?->name,
                    'salary' => $employee->salary,
                    'status' => $employee->status,
                    'joining_date' => $employee->joining_date,
                ] : null,
            ],
        ]);
    }

    /**
     * List employees (Admin, Manager, Team Leader, HR only)
     */
    public function employees(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasRole(['admin', 'manager', 'team-leader', 'hr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = Employee::with(['user', 'team']);

        // Team leaders can only see their team
        if ($user->hasRole('team-leader') && !$user->hasRole(['admin', 'manager', 'hr'])) {
            $query->where('team_id', $user->employee?->team_id);
        }

        $employees = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $employees->map(function ($emp) {
                return [
                    'id' => $emp->id,
                    'name' => $emp->user->name,
                    'mobile' => $emp->user->mobile,
                    'email' => $emp->user->email,
                    'team' => $emp->team?->name,
                    'salary' => $emp->salary,
                    'status' => $emp->status,
                    'joining_date' => $emp->joining_date,
                ];
            }),
            'pagination' => [
                'current_page' => $employees->currentPage(),
                'last_page' => $employees->lastPage(),
                'per_page' => $employees->perPage(),
                'total' => $employees->total(),
            ],
        ]);
    }

    /**
     * List teams
     */
    public function teams(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasRole(['admin', 'manager', 'team-leader', 'hr'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $teams = Team::withCount('employees')->get();

        return response()->json([
            'success' => true,
            'data' => $teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'employees_count' => $team->employees_count,
                ];
            }),
        ]);
    }

    /**
     * List loans
     */
    public function loans(Request $request)
    {
        $user = $request->user();
        
        $query = Loan::with(['employee.user', 'employee.team']);

        // Filter based on role
        if ($user->hasRole(['admin', 'manager', 'hr', 'accountant'])) {
            // Can see all loans
        } elseif ($user->hasRole('team-leader')) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('team_id', $user->employee?->team_id);
            });
        } else {
            // Regular employee - only their loans
            $query->where('employee_id', $user->employee?->id);
        }

        $loans = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $loans->map(function ($loan) {
                return [
                    'id' => $loan->id,
                    'employee' => $loan->employee->user->name,
                    'team' => $loan->employee->team?->name,
                    'total_amount' => $loan->total_amount,
                    'monthly_emi' => $loan->monthly_emi,
                    'tenure_months' => $loan->tenure_months,
                    'remaining_amount' => $loan->remaining_amount,
                    'status' => $loan->status,
                    'created_at' => $loan->created_at->toDateString(),
                ];
            }),
            'pagination' => [
                'current_page' => $loans->currentPage(),
                'last_page' => $loans->lastPage(),
                'per_page' => $loans->perPage(),
                'total' => $loans->total(),
            ],
        ]);
    }

    /**
     * List salary payments
     */
    public function salaryPayments(Request $request)
    {
        $user = $request->user();
        
        $query = SalaryPayment::with(['employee.user', 'employee.team']);

        // Filter based on role
        if ($user->hasRole(['admin', 'manager', 'hr', 'accountant'])) {
            // Can see all
        } elseif ($user->hasRole('team-leader')) {
            $query->whereHas('employee', function ($q) use ($user) {
                $q->where('team_id', $user->employee?->team_id);
            });
        } else {
            // Regular employee - only their salary
            $query->where('employee_id', $user->employee?->id);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'employee' => $payment->employee->user->name,
                    'team' => $payment->employee->team?->name,
                    'month' => $payment->month,
                    'year' => $payment->year,
                    'base_salary' => $payment->base_salary,
                    'deductions' => $payment->deductions,
                    'net_salary' => $payment->net_salary,
                    'status' => $payment->status,
                    'paid_at' => $payment->paid_at,
                ];
            }),
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ],
        ]);
    }

    /**
     * List requests
     */
    public function requests(Request $request)
    {
        $user = $request->user();
        
        $query = RequestModel::with(['user', 'user.employee.team']);

        // Filter based on role
        if ($user->hasRole(['admin', 'manager', 'hr'])) {
            // Can see all
        } elseif ($user->hasRole('team-leader')) {
            $query->whereHas('user.employee', function ($q) use ($user) {
                $q->where('team_id', $user->employee?->team_id);
            });
        } else {
            // Regular employee - only their requests
            $query->where('user_id', $user->id);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $requests->map(function ($req) {
                return [
                    'id' => $req->id,
                    'user' => $req->user->name,
                    'type' => $req->type,
                    'status' => $req->status,
                    'description' => $req->description,
                    'created_at' => $req->created_at->toDateTimeString(),
                ];
            }),
            'pagination' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }
}
