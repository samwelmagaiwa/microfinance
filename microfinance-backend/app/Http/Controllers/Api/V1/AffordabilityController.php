<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AffordabilityAssessment;
use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AffordabilityController extends Controller
{
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'salary' => 'nullable|numeric|min:0',
            'business_income' => 'nullable|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'rent' => 'nullable|numeric|min:0',
            'food' => 'nullable|numeric|min:0',
            'transport' => 'nullable|numeric|min:0',
            'utilities' => 'nullable|numeric|min:0',
            'school_fees' => 'nullable|numeric|min:0',
            'existing_loan_repayments' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
            'affordability_threshold' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $salary = $data['salary'] ?? 0;
        $businessIncome = $data['business_income'] ?? 0;
        $otherIncome = $data['other_income'] ?? 0;
        $totalIncome = $salary + $businessIncome + $otherIncome;

        $rent = $data['rent'] ?? 0;
        $food = $data['food'] ?? 0;
        $transport = $data['transport'] ?? 0;
        $utilities = $data['utilities'] ?? 0;
        $schoolFees = $data['school_fees'] ?? 0;
        $existingLoanRepayments = $data['existing_loan_repayments'] ?? 0;
        $otherExpenses = $data['other_expenses'] ?? 0;
        $totalExpenses = $rent + $food + $transport + $utilities + $schoolFees + $existingLoanRepayments + $otherExpenses;

        $netDisposableIncome = $totalIncome - $totalExpenses;

        $threshold = ($data['affordability_threshold'] ?? 40) / 100;
        $maxAffordableInstallment = max(0, $netDisposableIncome * $threshold);

        $riskLevel = 'low';
        $riskMessage = '';

        if ($netDisposableIncome <= 0) {
            $riskLevel = 'high';
            $riskMessage = 'Negative disposable income - cannot afford additional loan';
        } elseif ($maxAffordableInstallment < 50000) {
            $riskLevel = 'high';
            $riskMessage = 'Very low affordable installment - high risk';
        } elseif ($maxAffordableInstallment < 150000) {
            $riskLevel = 'medium';
            $riskMessage = 'Moderate risk - careful assessment needed';
        } else {
            $riskLevel = 'low';
            $riskMessage = 'Low risk - client can afford loan';
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'income' => [
                    'salary' => $salary,
                    'business_income' => $businessIncome,
                    'other_income' => $otherIncome,
                    'total_income' => $totalIncome,
                ],
                'expenses' => [
                    'rent' => $rent,
                    'food' => $food,
                    'transport' => $transport,
                    'utilities' => $utilities,
                    'school_fees' => $schoolFees,
                    'existing_loan_repayments' => $existingLoanRepayments,
                    'other_expenses' => $otherExpenses,
                    'total_expenses' => $totalExpenses,
                ],
                'assessment' => [
                    'net_disposable_income' => $netDisposableIncome,
                    'max_affordable_installment' => round($maxAffordableInstallment, 2),
                    'affordability_threshold_percent' => $threshold * 100,
                    'risk_level' => $riskLevel,
                    'risk_message' => $riskMessage,
                ],
                'assessed_at' => now()->toIso8601String(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'borrower_id' => 'required|integer|exists:borrowers,id',
            'salary' => 'nullable|numeric|min:0',
            'business_income' => 'nullable|numeric|min:0',
            'other_income' => 'nullable|numeric|min:0',
            'rent' => 'nullable|numeric|min:0',
            'food' => 'nullable|numeric|min:0',
            'transport' => 'nullable|numeric|min:0',
            'utilities' => 'nullable|numeric|min:0',
            'school_fees' => 'nullable|numeric|min:0',
            'existing_loan_repayments' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
            'affordability_threshold' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $salary = $data['salary'] ?? 0;
        $businessIncome = $data['business_income'] ?? 0;
        $otherIncome = $data['other_income'] ?? 0;
        $totalIncome = $salary + $businessIncome + $otherIncome;

        $rent = $data['rent'] ?? 0;
        $food = $data['food'] ?? 0;
        $transport = $data['transport'] ?? 0;
        $utilities = $data['utilities'] ?? 0;
        $schoolFees = $data['school_fees'] ?? 0;
        $existingLoanRepayments = $data['existing_loan_repayments'] ?? 0;
        $otherExpenses = $data['other_expenses'] ?? 0;
        $totalExpenses = $rent + $food + $transport + $utilities + $schoolFees + $existingLoanRepayments + $otherExpenses;

        $netDisposableIncome = $totalIncome - $totalExpenses;

        $threshold = ($data['affordability_threshold'] ?? 40) / 100;
        $maxAffordableInstallment = max(0, $netDisposableIncome * $threshold);

        $riskLevel = 'low';
        $riskMessage = '';

        if ($netDisposableIncome <= 0) {
            $riskLevel = 'high';
            $riskMessage = 'Negative disposable income - cannot afford additional loan';
        } elseif ($maxAffordableInstallment < 50000) {
            $riskLevel = 'high';
            $riskMessage = 'Very low affordable installment - high risk';
        } elseif ($maxAffordableInstallment < 150000) {
            $riskLevel = 'medium';
            $riskMessage = 'Moderate risk - careful assessment needed';
        } else {
            $riskLevel = 'low';
            $riskMessage = 'Low risk - client can afford loan';
        }

        $assessment = AffordabilityAssessment::create([
            'borrower_id' => $data['borrower_id'],
            'user_id' => Auth::id(),
            'salary' => $salary,
            'business_income' => $businessIncome,
            'other_income' => $otherIncome,
            'total_income' => $totalIncome,
            'rent' => $rent,
            'food' => $food,
            'transport' => $transport,
            'utilities' => $utilities,
            'school_fees' => $schoolFees,
            'existing_loan_repayments' => $existingLoanRepayments,
            'other_expenses' => $otherExpenses,
            'total_expenses' => $totalExpenses,
            'net_disposable_income' => $netDisposableIncome,
            'max_affordable_installment' => $maxAffordableInstallment,
            'affordability_threshold_percent' => $threshold * 100,
            'risk_level' => $riskLevel,
            'risk_message' => $riskMessage,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Affordability assessment saved successfully',
            'data' => $assessment
        ], 201);
    }

    public function show(Request $request, int $borrowerId)
    {
        $borrower = Borrower::findOrFail($borrowerId);
        
        $assessment = AffordabilityAssessment::where('borrower_id', $borrowerId)
            ->latest()
            ->first();

        if (!$assessment) {
            return response()->json([
                'status' => 'error',
                'message' => 'No affordability assessment found for this borrower'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $assessment
        ]);
    }

    public function history(int $borrowerId)
    {
        $borrower = Borrower::findOrFail($borrowerId);
        
        $assessments = AffordabilityAssessment::where('borrower_id', $borrowerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $assessments
        ]);
    }
}
