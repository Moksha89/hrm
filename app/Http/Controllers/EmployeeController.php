<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\BankAccount;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['user', 'team'])->get();
        $teams = Team::all();
        
        return view('employees.index', compact('employees', 'teams'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => bcrypt('Password@00'),
                'password_changed_at' => null,
            ]);

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

            if ($request->loan > 0 && $request->emi > 0) {
                $months = ceil($request->loan / $request->emi);
                
                $bankAccountId = $employee->bankAccounts()->first()?->id;
                
                $loan = Loan::create([
                    'employee_id' => $employee->id,
                    'bank_account_id' => $bankAccountId,
                    'total_amount' => $request->loan,
                    'monthly_emi' => $request->emi,
                    'total_months' => $months,
                    'remaining_months' => $months,
                    'remaining_balance' => $request->loan,
                    'start_date' => now(),
                    'status' => 'pending',
                ]);

                for ($i = 1; $i <= $months; $i++) {
                    $amount = ($i === $months) ? ($request->loan - ($request->emi * ($months - 1))) : $request->emi;
                    
                    LoanPayment::create([
                        'loan_id' => $loan->id,
                        'installment_number' => $i,
                        'amount' => $amount,
                        'due_date' => now()->addMonths($i - 1),
                        'status' => 'pending',
                    ]);
                }
            }

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
        
        if (in_array($status, ['resigned', 'inactive'])) {
            $employee->team_id = null;
        }
        
        $employee->status = $status;
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Employee status updated successfully!');
    }
}
