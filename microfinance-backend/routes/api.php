<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BorrowerController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Dashboard - accessible by all staff
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Management & GM Routes
        Route::middleware('role:admin,managing_director,general_manager')->group(function () {
            // High-level reports, deletions, audit logs (not implemented yet)
            Route::delete('loans/{loan}', [LoanController::class, 'destroy']);
            Route::delete('borrowers/{borrower}', [BorrowerController::class, 'destroy']);
        });

        // Loan Management
        Route::middleware('role:admin,managing_director,general_manager,loan_manager')->group(function () {
            // Approval logics, updating terms
            Route::patch('loans/{loan}/approve', [LoanController::class, 'approve']);
            Route::patch('borrowers/{borrower}/approve', [BorrowerController::class, 'approve']);
            Route::patch('borrowers/{borrower}/reject', [BorrowerController::class, 'reject']);
        });

        // Loan Officer & Secretary
        Route::middleware('role:admin,managing_director,general_manager,loan_manager,loan_officer,secretary')->group(function () {
            Route::apiResource('borrowers', BorrowerController::class)->except(['destroy']);
            Route::apiResource('loans', LoanController::class)->except(['destroy']);
            Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'store']);
        });

        // Client specific routes
        Route::middleware('role:client')->group(function () {
            // Clients can only view their own summary or specific details
            // These would point to specific methods in controllers that filter by auth user
            Route::get('client/summary', [DashboardController::class, 'clientSummary']);
            Route::get('client/loans', [LoanController::class, 'clientLoans']);
            Route::get('client/payments', [PaymentController::class, 'clientPayments']);
        });
        // User info
        Route::get('user', function (Request $request) {
            return response()->json(['data' => $request->user()]);
        });
    });
});
