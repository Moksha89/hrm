<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::post('/employees/{id}/status/{status}', [App\Http\Controllers\EmployeeController::class, 'updateStatus'])->name('employees.updateStatus');
    
    Route::get('/teams', [App\Http\Controllers\TeamController::class, 'index'])->name('teams.index');
    Route::post('/teams', [App\Http\Controllers\TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{id}', [App\Http\Controllers\TeamController::class, 'show'])->name('teams.show');
    Route::post('/teams/{id}/assign', [App\Http\Controllers\TeamController::class, 'assignEmployee'])->name('teams.assign');
    Route::delete('/teams/{teamId}/remove/{employeeId}', [App\Http\Controllers\TeamController::class, 'removeEmployee'])->name('teams.remove');
    Route::post('/teams/{teamId}/working-days', [App\Http\Controllers\TeamController::class, 'updateWorkingDays'])->name('teams.updateWorkingDays');
    Route::post('/teams/{teamId}/salary-settings', [App\Http\Controllers\TeamController::class, 'updateBulkSalarySettings'])->name('teams.updateBulkSalarySettings');
    
    Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/team/{teamId}', [App\Http\Controllers\LoanController::class, 'showTeam'])->name('loans.team');
    Route::get('/loans/employee/{employeeId}', [App\Http\Controllers\LoanController::class, 'showEmployee'])->name('loans.employee');
    Route::post('/loans/employee/{employeeId}', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{loanId}/activate', [App\Http\Controllers\LoanController::class, 'activateLoan'])->name('loans.activate');
    Route::post('/loans/payment/{paymentId}/mark-paid', [App\Http\Controllers\LoanController::class, 'markPaymentPaid'])->name('loans.markPaymentPaid');
    
    Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments/disburse/{loanId}', [App\Http\Controllers\PaymentController::class, 'disburseLoan'])->name('payments.disburse');
    Route::post('/payments/collect/{loanPaymentId}', [App\Http\Controllers\PaymentController::class, 'collectEmi'])->name('payments.collect');
    Route::post('/payments/approve-loan/{loanId}', [App\Http\Controllers\PaymentController::class, 'approveLoan'])->name('payments.loan.approve');
    Route::post('/payments/reject-loan/{loanId}', [App\Http\Controllers\PaymentController::class, 'rejectLoan'])->name('payments.loan.reject');
    
    Route::get('/payments/salaries', [App\Http\Controllers\PaymentController::class, 'salaries'])->name('payments.salaries');
    Route::get('/payments/salary/teams', [App\Http\Controllers\PaymentController::class, 'salaryTeams'])->name('payments.salary.teams');
    Route::get('/payments/salary/team/{teamId}', [App\Http\Controllers\PaymentController::class, 'salaryTeamEmployees'])->name('payments.salary.team');
    Route::post('/payments/salary/disburse/{employeeId}', [App\Http\Controllers\PaymentController::class, 'disburseSalary'])->name('payments.salary.disburse');
    Route::get('/payments/salary/history', [App\Http\Controllers\PaymentController::class, 'salaryHistory'])->name('payments.salary.history');
    Route::post('/payments/approve-salary/{employeeId}', [App\Http\Controllers\PaymentController::class, 'approveSalary'])->name('payments.salary.approve');
    Route::post('/payments/reject-salary/{employeeId}', [App\Http\Controllers\PaymentController::class, 'rejectSalary'])->name('payments.salary.reject');
    
    Route::get('/payments/transactions', [App\Http\Controllers\PaymentController::class, 'transactionHistory'])->name('payments.transactions');
    
    Route::get('/payments/employees', [App\Http\Controllers\PaymentController::class, 'employees'])->name('payments.employees');
    Route::get('/payments/employee/{employeeId}', [App\Http\Controllers\PaymentController::class, 'employeeDetail'])->name('payments.employee.detail');
    
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar');
    
    Route::get('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.update');
});
