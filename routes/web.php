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
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware('role:admin,team-leader,manager,hr')->group(function () {
        Route::get('/employees', [App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    });
    Route::post('/employees', [App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store')->middleware('role:admin,team-leader');
    Route::post('/employees/{id}/status/{status}', [App\Http\Controllers\EmployeeController::class, 'updateStatus'])->name('employees.updateStatus')->middleware('role:admin,team-leader');
    
    Route::middleware('role:admin,manager,team-leader')->group(function () {
        Route::get('/teams', [App\Http\Controllers\TeamController::class, 'index'])->name('teams.index');
        Route::get('/teams/{id}', [App\Http\Controllers\TeamController::class, 'show'])->name('teams.show');
    });
    Route::post('/teams', [App\Http\Controllers\TeamController::class, 'store'])->name('teams.store')->middleware('role:admin');
    Route::put('/teams/{id}', [App\Http\Controllers\TeamController::class, 'update'])->name('teams.update')->middleware('role:admin');
    Route::delete('/teams/{id}', [App\Http\Controllers\TeamController::class, 'destroy'])->name('teams.destroy')->middleware('role:admin');
    Route::post('/teams/{id}/assign', [App\Http\Controllers\TeamController::class, 'assignEmployee'])->name('teams.assign')->middleware('role:admin,team-leader');
    Route::delete('/teams/{teamId}/remove/{employeeId}', [App\Http\Controllers\TeamController::class, 'removeEmployee'])->name('teams.remove')->middleware('role:admin,team-leader');
    Route::post('/teams/{teamId}/working-days', [App\Http\Controllers\TeamController::class, 'updateWorkingDays'])->name('teams.updateWorkingDays')->middleware('role:admin,manager,team-leader');
    Route::post('/teams/{teamId}/salary-settings', [App\Http\Controllers\TeamController::class, 'updateBulkSalarySettings'])->name('teams.updateBulkSalarySettings')->middleware('role:admin,manager');
    
    Route::middleware('role:admin,manager,team-leader,accountant')->group(function () {
        Route::get('/loans', [App\Http\Controllers\LoanController::class, 'index'])->name('loans.index');
        Route::get('/loans/team/{teamId}', [App\Http\Controllers\LoanController::class, 'showTeam'])->name('loans.team');
        Route::get('/loans/employee/{employeeId}', [App\Http\Controllers\LoanController::class, 'showEmployee'])->name('loans.employee');
    });
    Route::post('/loans/employee/{employeeId}', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store')->middleware('role:admin,team-leader');
    Route::post('/loans/{loanId}/activate', [App\Http\Controllers\LoanController::class, 'activateLoan'])->name('loans.activate')->middleware('role:admin');
    Route::post('/loans/payment/{paymentId}/mark-paid', [App\Http\Controllers\LoanController::class, 'markPaymentPaid'])->name('loans.markPaymentPaid')->middleware('role:admin,accountant');
    
    Route::middleware('role:admin,accountant,manager')->group(function () {
        Route::get('/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/salaries', [App\Http\Controllers\PaymentController::class, 'salaries'])->name('payments.salaries');
        Route::get('/payments/salary/teams', [App\Http\Controllers\PaymentController::class, 'salaryTeams'])->name('payments.salary.teams');
        Route::get('/payments/salary/team/{teamId}', [App\Http\Controllers\PaymentController::class, 'salaryTeamEmployees'])->name('payments.salary.team');
        Route::get('/payments/salary/history', [App\Http\Controllers\PaymentController::class, 'salaryHistory'])->name('payments.salary.history');
        Route::get('/payments/transactions', [App\Http\Controllers\PaymentController::class, 'transactionHistory'])->name('payments.transactions');
        Route::get('/payments/employees', [App\Http\Controllers\PaymentController::class, 'employees'])->name('payments.employees');
        Route::get('/payments/employee/{employeeId}', [App\Http\Controllers\PaymentController::class, 'employeeDetail'])->name('payments.employee.detail');
    });
    Route::post('/payments/disburse/{loanId}', [App\Http\Controllers\PaymentController::class, 'disburseLoan'])->name('payments.disburse')->middleware('role:admin,accountant');
    Route::post('/payments/collect/{loanPaymentId}', [App\Http\Controllers\PaymentController::class, 'collectEmi'])->name('payments.collect')->middleware('role:admin,accountant');
    Route::post('/payments/approve-loan/{loanId}', [App\Http\Controllers\PaymentController::class, 'approveLoan'])->name('payments.loan.approve')->middleware('role:admin,manager');
    Route::post('/payments/reject-loan/{loanId}', [App\Http\Controllers\PaymentController::class, 'rejectLoan'])->name('payments.loan.reject')->middleware('role:admin,manager');
    Route::post('/payments/salary/disburse/{employeeId}', [App\Http\Controllers\PaymentController::class, 'disburseSalary'])->name('payments.salary.disburse')->middleware('role:admin,accountant');
    Route::post('/payments/approve-salary/{employeeId}', [App\Http\Controllers\PaymentController::class, 'approveSalary'])->name('payments.salary.approve')->middleware('role:admin,manager');
    Route::post('/payments/reject-salary/{employeeId}', [App\Http\Controllers\PaymentController::class, 'rejectSalary'])->name('payments.salary.reject')->middleware('role:admin,manager');
    
    Route::get('/requests', [App\Http\Controllers\RequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [App\Http\Controllers\RequestController::class, 'store'])->name('requests.store')->middleware('role:admin,team-leader');
    Route::post('/requests/{id}/approve', [App\Http\Controllers\RequestController::class, 'approve'])->name('requests.approve')->middleware('role:admin,manager');
    Route::post('/requests/{id}/reject', [App\Http\Controllers\RequestController::class, 'reject'])->name('requests.reject')->middleware('role:admin,manager');
    
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    
    Route::get('/profile', [App\Http\Controllers\EmployeeProfileController::class, 'show'])->name('profile.show');
    
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar');
    
    Route::get('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.update');
});
