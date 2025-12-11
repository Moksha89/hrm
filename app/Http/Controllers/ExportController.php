<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\Request as RequestModel;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Row;

class ExportController extends Controller
{
    public function employees(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        $teamId = $request->get('team_id');
        
        if ($user->isAdmin() || $user->isHR() || $user->isAccountant()) {
            $query = Employee::with(['user', 'team']);
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $query = Employee::with(['user', 'team'])
                ->whereIn('team_id', $assignedTeamIds);
        } else {
            $employees = collect();
            $query = null;
        }
        
        if ($query) {
            if ($teamId) {
                $query->where('team_id', $teamId);
            }
            $employees = $query->get();
        }
        
        $headers = ['ID', 'Name', 'Mobile', 'Email', 'Team', 'Salary', 'Status', 'Date of Joining'];
        
        $rows = $employees->map(function ($employee) {
            return [
                $employee->id,
                $employee->user->name,
                $employee->user->mobile,
                $employee->user->email,
                $employee->team?->name ?? 'No Team',
                $employee->salary,
                $employee->status,
                $employee->date_of_joining?->format('Y-m-d'),
            ];
        })->toArray();
        
        $filename = 'employees_' . date('Y-m-d_His');
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function loans(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $loans = Loan::with(['employee.user', 'employee.team'])->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $loans = Loan::with(['employee.user', 'employee.team'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $loans = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Total Amount', 'Monthly EMI', 'Remaining Balance', 'Remaining Months', 'Status', 'Start Date'];
        
        $rows = $loans->map(function ($loan) {
            return [
                $loan->id,
                $loan->employee->user->name,
                $loan->employee->team?->name ?? 'No Team',
                $loan->total_amount,
                $loan->monthly_emi,
                $loan->remaining_balance,
                $loan->remaining_months,
                $loan->status,
                $loan->start_date?->format('Y-m-d'),
            ];
        })->toArray();
        
        $filename = 'loans_' . date('Y-m-d_His');
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function salaryHistory(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $payments = SalaryPayment::with(['employee.user', 'employee.team'])
                ->where('month', $month)
                ->where('year', $year)
                ->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $payments = SalaryPayment::with(['employee.user', 'employee.team'])
                ->where('month', $month)
                ->where('year', $year)
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $payments = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Month', 'Year', 'Base Salary', 'Working Days', 'Net Pay', 'EMI Deduction', 'Final Pay', 'Status', 'UTR'];
        
        $rows = $payments->map(function ($payment) {
            return [
                $payment->id,
                $payment->employee->user->name,
                $payment->employee->team?->name ?? 'No Team',
                $payment->month,
                $payment->year,
                $payment->gross_salary ?? 0,
                $payment->working_days,
                $payment->net_pay,
                $payment->total_emi ?? 0,
                $payment->final_pay,
                $payment->status,
                $payment->utr,
            ];
        })->toArray();
        
        $filename = 'salary_history_' . $month . '_' . $year . '_' . date('Y-m-d_His');
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function transactions(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $transactions = Payment::with(['employee.user', 'employee.team', 'loan'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $transactions = Payment::with(['employee.user', 'employee.team', 'loan'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $transactions = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Type', 'Amount', 'Status', 'UTR', 'Date'];
        
        $rows = $transactions->map(function ($transaction) {
            return [
                $transaction->id,
                $transaction->employee->user->name ?? 'N/A',
                $transaction->employee->team?->name ?? 'No Team',
                $transaction->payment_type,
                $transaction->amount,
                $transaction->status,
                $transaction->utr,
                $transaction->created_at?->format('Y-m-d H:i'),
            ];
        })->toArray();
        
        $filename = 'transactions_' . date('Y-m-d_His');
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function requests(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        
        if ($user->isAdmin() || $user->isManager()) {
            $requests = RequestModel::with(['employee.user', 'employee.team'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $requests = RequestModel::with(['employee.user', 'employee.team'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $requests = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Type', 'Status', 'Created At'];
        
        $rows = $requests->map(function ($req) {
            return [
                $req->id,
                $req->employee->user->name ?? 'N/A',
                $req->employee->team?->name ?? 'No Team',
                $req->type,
                $req->status,
                $req->created_at?->format('Y-m-d H:i'),
            ];
        })->toArray();
        
        $filename = 'requests_' . date('Y-m-d_His');
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    private function exportToExcel(array $headers, array $rows, string $filename)
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'export_') . '.xlsx';
        
        $writer = new Writer();
        $writer->openToFile($tempFile);
        
        $writer->addRow(Row::fromValues($headers));
        
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }
        
        $writer->close();
        
        return response()->download($tempFile, $filename . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
    
    private function exportToCsv(array $headers, array $rows, string $filename)
    {
        $callback = function() use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ]);
    }
}
