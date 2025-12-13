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
use Barryvdh\DomPDF\Facade\Pdf;

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
        $title = 'Employees Report';
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($headers, $rows, $filename, $title);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function loans(Request $request)
    {
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $loans = Loan::with(['employee.user', 'employee.team', 'employee.bankAccounts'])->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $loans = Loan::with(['employee.user', 'employee.team', 'employee.bankAccounts'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $loans = collect();
        }
        
        $headers = ['Employee Name', 'Team', 'Account Holder', 'Account Number', 'IFSC Code', 'Bank Name', 'Loan Amount', 'Tenure (Months)', 'Monthly EMI', 'Net Payable Amount', 'Status'];
        
        $rows = $loans->map(function ($loan) {
            $defaultBank = $loan->employee->bankAccounts->where('is_default', true)->first() 
                ?? $loan->employee->bankAccounts->first();
            return [
                $loan->employee->user->name,
                $loan->employee->team?->name ?? 'No Team',
                $defaultBank?->account_holder_name ?? 'N/A',
                $defaultBank?->account_number ?? 'N/A',
                $defaultBank?->ifsc_code ?? 'N/A',
                $defaultBank?->bank_name ?? 'N/A',
                $loan->total_amount,
                $loan->total_months,
                $loan->monthly_emi,
                $loan->remaining_balance,
                $loan->status,
            ];
        })->toArray();
        
        $filename = 'loan_disbursements_' . date('Y-m-d_His');
        $title = 'Loan Disbursements Report';
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($headers, $rows, $filename, $title);
        }
        
        return $this->exportToCsv($headers, $rows, $filename);
    }
    
    public function salaryHistory(Request $request)
    {
        \Log::info('ExportController::salaryHistory hit', [
            'user_id' => auth()->id(),
            'user_roles' => auth()->user()?->roles->pluck('name')->toArray(),
            'format' => $request->get('format'),
            'url' => $request->url(),
        ]);
        
        $user = auth()->user();
        $format = $request->get('format', 'xlsx');
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $payments = SalaryPayment::with(['employee.user', 'employee.team', 'employee.bankAccounts'])
                ->where('month', $month)
                ->where('year', $year)
                ->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $payments = SalaryPayment::with(['employee.user', 'employee.team', 'employee.bankAccounts'])
                ->where('month', $month)
                ->where('year', $year)
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $payments = collect();
        }
        
        $headers = ['Employee Name', 'Team', 'Account Holder', 'Account Number', 'IFSC Code', 'Bank Name', 'Gross Salary', 'EMI Deduction', 'Net Payable Amount', 'Status', 'UTR'];
        
        $rows = $payments->map(function ($payment) {
            $defaultBank = $payment->employee->bankAccounts->where('is_default', true)->first() 
                ?? $payment->employee->bankAccounts->first();
            return [
                $payment->employee->user->name,
                $payment->employee->team?->name ?? 'No Team',
                $defaultBank?->account_holder_name ?? 'N/A',
                $defaultBank?->account_number ?? 'N/A',
                $defaultBank?->ifsc_code ?? 'N/A',
                $defaultBank?->bank_name ?? 'N/A',
                $payment->gross_salary ?? 0,
                $payment->total_emi ?? 0,
                $payment->final_pay,
                $payment->status,
                $payment->utr ?? '',
            ];
        })->toArray();
        
        $filename = 'salary_payments_' . $month . '_' . $year . '_' . date('Y-m-d_His');
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $title = 'Salary Payments - ' . $monthNames[$month - 1] . ' ' . $year;
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($headers, $rows, $filename, $title);
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
        $title = 'Transactions Report';
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($headers, $rows, $filename, $title);
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
        $title = 'Requests Report';
        
        if ($format === 'xlsx') {
            return $this->exportToExcel($headers, $rows, $filename);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($headers, $rows, $filename, $title);
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
    
    private function exportToPdf(array $headers, array $rows, string $filename, string $title = 'Export')
    {
        $html = '<html><head><style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            h1 { font-size: 16px; margin-bottom: 10px; color: #333; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #f59e0b; color: white; padding: 8px; text-align: left; font-size: 9px; }
            td { border: 1px solid #ddd; padding: 6px; font-size: 9px; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .footer { margin-top: 20px; font-size: 8px; color: #666; }
        </style></head><body>';
        $html .= '<h1>' . $title . '</h1>';
        $html .= '<p style="font-size: 9px; color: #666;">Generated on: ' . date('Y-m-d H:i:s') . '</p>';
        $html .= '<table><thead><tr>';
        
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody></table>';
        $html .= '<div class="footer">Total Records: ' . count($rows) . '</div>';
        $html .= '</body></html>';
        
        $pdf = Pdf::loadHTML($html)->setPaper('a4', 'landscape');
        
        return $pdf->download($filename . '.pdf');
    }
}
