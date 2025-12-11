<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use OpenSpout\Reader\XLSX\Reader as XlsxReader;
use OpenSpout\Reader\CSV\Reader as CsvReader;

class ImportController extends Controller
{
    public function showEmployeeImport()
    {
        $teams = Team::all();
        return view('employees.import', compact('teams'));
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv|max:10240',
            'default_team_id' => 'nullable|exists:teams,id',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        $reader = $extension === 'csv' ? new CsvReader() : new XlsxReader();
        $reader->open($file->getPathname());

        $successCount = 0;
        $errors = [];
        $rowNumber = 0;

        DB::beginTransaction();
        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $row) {
                    $rowNumber++;
                    
                    // Skip header row
                    if ($rowNumber === 1) {
                        continue;
                    }

                    $cells = $row->getCells();
                    
                    // Expected columns: Name, Mobile, Email, Salary, Team, Bank Name, Account Number, IFSC
                    $name = trim($cells[0]->getValue() ?? '');
                    $mobile = trim($cells[1]->getValue() ?? '');
                    $email = trim($cells[2]->getValue() ?? '');
                    $salary = floatval($cells[3]->getValue() ?? 0);
                    $teamName = trim($cells[4]->getValue() ?? '');
                    $bankName = trim($cells[5]->getValue() ?? '');
                    $accountNumber = trim($cells[6]->getValue() ?? '');
                    $ifsc = trim($cells[7]->getValue() ?? '');

                    // Validate required fields
                    if (empty($name) || empty($mobile)) {
                        $errors[] = "Row {$rowNumber}: Name and Mobile are required.";
                        continue;
                    }

                    // Check for duplicate mobile
                    if (User::where('mobile', $mobile)->exists()) {
                        $errors[] = "Row {$rowNumber}: Mobile {$mobile} already exists.";
                        continue;
                    }

                    // Find or use default team
                    $teamId = $request->default_team_id;
                    if (!empty($teamName)) {
                        $team = Team::where('name', 'like', "%{$teamName}%")->first();
                        if ($team) {
                            $teamId = $team->id;
                        }
                    }

                    // Create user
                    $user = User::create([
                        'name' => $name,
                        'mobile' => $mobile,
                        'email' => $email ?: null,
                        'password' => Hash::make('Password@123'), // Default password
                    ]);

                    // Assign employee role
                    $employeeRole = \App\Models\Role::where('slug', 'employee')->first();
                    if ($employeeRole) {
                        $user->roles()->attach($employeeRole->id);
                    }

                    // Create employee
                    $employee = Employee::create([
                        'user_id' => $user->id,
                        'team_id' => $teamId,
                        'salary' => $salary,
                        'status' => 'active',
                        'joining_date' => now(),
                    ]);

                    // Create bank account if provided
                    if (!empty($bankName) && !empty($accountNumber)) {
                        BankAccount::create([
                            'employee_id' => $employee->id,
                            'bank_name' => $bankName,
                            'account_number' => $accountNumber,
                            'ifsc_code' => $ifsc,
                            'is_default' => true,
                        ]);
                    }

                    $successCount++;
                }
                break; // Only process first sheet
            }

            $reader->close();

            if ($successCount > 0) {
                DB::commit();
                
                AuditLog::log(
                    'bulk_import',
                    'employees',
                    "Bulk imported {$successCount} employees from file",
                    null,
                    [],
                    ['count' => $successCount, 'errors' => count($errors)]
                );
            } else {
                DB::rollBack();
            }

            $message = "Successfully imported {$successCount} employees.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " ... and " . (count($errors) - 5) . " more errors.";
                }
            }

            return redirect()->route('employees.index')->with($successCount > 0 ? 'success' : 'error', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            $reader->close();
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employee_import_template.csv"',
        ];

        $columns = ['Name', 'Mobile', 'Email', 'Salary', 'Team', 'Bank Name', 'Account Number', 'IFSC Code'];
        $example = ['John Doe', '9876543210', 'john@example.com', '50000', 'Marketing Team', 'HDFC Bank', '1234567890', 'HDFC0001234'];

        $callback = function() use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
