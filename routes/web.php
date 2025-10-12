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
    
    Route::get('/teams', [App\Http\Controllers\TeamController::class, 'index'])->name('teams.index');
    Route::post('/teams', [App\Http\Controllers\TeamController::class, 'store'])->name('teams.store');
    Route::get('/teams/{id}', [App\Http\Controllers\TeamController::class, 'show'])->name('teams.show');
    Route::post('/teams/{id}/assign', [App\Http\Controllers\TeamController::class, 'assignEmployee'])->name('teams.assign');
    Route::delete('/teams/{teamId}/remove/{employeeId}', [App\Http\Controllers\TeamController::class, 'removeEmployee'])->name('teams.remove');
    
    Route::get('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'show'])->name('password.change');
    Route::post('/password/change', [App\Http\Controllers\PasswordChangeController::class, 'update'])->name('password.update');
});
