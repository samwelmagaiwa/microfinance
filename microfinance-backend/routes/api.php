<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BorrowerController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\AuditLogController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\LoanCalculatorController;
use App\Http\Controllers\Api\V1\AffordabilityController;
use App\Http\Controllers\Api\V1\DigitalSignatureController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('login', [AuthController::class, 'login']);
    
    // Loan Calculator (public - no auth required)
    Route::post('loan/calculate', [LoanCalculatorController::class, 'calculate']);
    
    // Affordability Calculator
    Route::post('affordability/calculate', [AffordabilityController::class, 'calculate']);
    
    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);

        // Staff dashboard - versioned and role gated
        Route::middleware('role:admin,managing_director,general_manager,loan_manager,loan_officer,secretary')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'index']);
        });

        // Management & GM Routes
        Route::middleware('role:admin,managing_director,general_manager,loan_officer')->group(function () {
            // High-level reports, deletions, audit logs
            Route::delete('loans/{loan}', [LoanController::class, 'destroy']);
            Route::delete('borrowers/{borrower}', [BorrowerController::class, 'destroy']);
            Route::get('audit-logs', [AuditLogController::class, 'index']);
            Route::get('audit-logs/{id}', [AuditLogController::class, 'show']);
            Route::get('reports/financial', [ReportController::class, 'financial']);
            Route::get('reports/performance', [ReportController::class, 'performance']);
        });

        // Loan Management
        Route::middleware('role:admin,managing_director,general_manager,loan_manager,loan_officer')->group(function () {
            // Approval logics, updating terms
            Route::patch('loans/{loan}/approve', [LoanController::class, 'approve']);
            Route::patch('loans/{loan}/approve-step', [LoanController::class, 'approveStep']);
            Route::patch('loans/{loan}/reject', [LoanController::class, 'reject']);
            Route::get('loans/{loan}/approval-status', [LoanController::class, 'getApprovalStatus']);
            Route::patch('borrowers/{borrower}/approve', [BorrowerController::class, 'approve']);
            Route::patch('borrowers/{borrower}/reject', [BorrowerController::class, 'reject']);
            Route::get('borrowers/{borrower}/review-history', [BorrowerController::class, 'getReviewHistory']);
        });

        // Loan Officer & Secretary
        Route::middleware('role:admin,managing_director,general_manager,loan_manager,loan_officer,secretary')->group(function () {
            Route::apiResource('borrowers', BorrowerController::class)->except(['destroy']);
            Route::apiResource('loans', LoanController::class)->except(['destroy']);
            Route::apiResource('payments', PaymentController::class)->only(['index', 'show', 'store']);
            
            // Affordability assessments
            Route::post('affordability/store', [AffordabilityController::class, 'store']);
            Route::get('affordability/{borrower}', [AffordabilityController::class, 'show']);
            Route::get('affordability/{borrower}/history', [AffordabilityController::class, 'history']);
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

        // Digital Signatures
        Route::middleware('role:admin,managing_director,general_manager,loan_manager,loan_officer')->group(function () {
            Route::post('signatures', [DigitalSignatureController::class, 'store']);
            Route::get('signatures', [DigitalSignatureController::class, 'index']);
            Route::get('signatures/approval-status', [DigitalSignatureController::class, 'approvalStatus']);
            Route::post('signatures/verify', [DigitalSignatureController::class, 'verify']);
            Route::patch('signatures/reject', [DigitalSignatureController::class, 'reject']);
        });
    });
});
