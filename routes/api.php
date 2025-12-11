<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes
Route::post('/login', [ApiController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [ApiController::class, 'logout']);
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/employees', [ApiController::class, 'employees']);
    Route::get('/teams', [ApiController::class, 'teams']);
    Route::get('/loans', [ApiController::class, 'loans']);
    Route::get('/salary-payments', [ApiController::class, 'salaryPayments']);
    Route::get('/requests', [ApiController::class, 'requests']);
});
