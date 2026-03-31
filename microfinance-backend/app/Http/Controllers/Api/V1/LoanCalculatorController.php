<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class LoanCalculatorController extends Controller
{
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'principal' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'period_months' => 'required|integer|min:1|max:360',
            'processing_fee_rate' => 'nullable|numeric|min:0|max:100',
            'bima_rate' => 'nullable|numeric|min:0|max:100',
            'bima_type' => 'nullable|in:deducted,financed',
            'start_date' => 'nullable|date',
            // Affordability fields
            'income' => 'nullable|array',
            'income.salary' => 'nullable|numeric|min:0',
            'income.other' => 'nullable|numeric|min:0',
            'expenses' => 'nullable|array',
            'expenses.rent' => 'nullable|numeric|min:0',
            'expenses.food' => 'nullable|numeric|min:0',
            'expenses.transport' => 'nullable|numeric|min:0',
            'expenses.utilities' => 'nullable|numeric|min:0',
            'expenses.school_fees' => 'nullable|numeric|min:0',
            'expenses.loan_repayments' => 'nullable|numeric|min:0',
            'expenses.other' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        $principal = (float) $data['principal'];
        $interestRate = (float) $data['interest_rate'];
        $periodMonths = (int) $data['period_months'];
        $processingFeeRate = (float) ($data['processing_fee_rate'] ?? 0);
        $bimaRate = (float) ($data['bima_rate'] ?? 0);
        $bimaType = $data['bima_type'] ?? 'deducted';
        $startDate = !empty($data['start_date']) 
            ? Carbon::parse($data['start_date']) 
            : Carbon::now();

        $monthlyInterest = $principal * ($interestRate / 100);
        $totalInterest = $monthlyInterest * $periodMonths;
        $totalRepayment = $principal + $totalInterest;
        $monthlyPayment = $totalRepayment / $periodMonths;

        $bimaAmount = $principal * ($bimaRate / 100);
        $processingFee = $principal * ($processingFeeRate / 100);

        if ($bimaType === 'deducted') {
            $netDisbursed = $principal - $bimaAmount - $processingFee;
            $effectivePrincipal = $principal;
        } else {
            $effectivePrincipal = $principal + $bimaAmount;
            $netDisbursed = $effectivePrincipal - $processingFee;
            $bimaAmount = $effectivePrincipal * ($bimaRate / 100);
            $totalInterest = $effectivePrincipal * ($interestRate / 100) * $periodMonths;
            $totalRepayment = $effectivePrincipal + $totalInterest;
            $monthlyPayment = $totalRepayment / $periodMonths;
        }

        $schedule = $this->generateSchedule(
            $effectivePrincipal,
            $monthlyInterest,
            $monthlyPayment,
            $periodMonths,
            $startDate
        );

        $affordability = null;
        if (isset($data['income']) || isset($data['expenses'])) {
            $affordability = $this->assessAffordability($data['income'] ?? [], $data['expenses'] ?? [], $monthlyPayment);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'principal' => $principal,
                'interest_rate' => $interestRate,
                'period_months' => $periodMonths,
                'monthly_payment' => round($monthlyPayment, 2),
                'total_interest' => round($totalInterest, 2),
                'total_repayment' => round($totalRepayment, 2),
                'processing_fee' => round($processingFee, 2),
                'bima_amount' => round($bimaAmount, 2),
                'bima_type' => $bimaType,
                'net_disbursed' => round($netDisbursed, 2),
                'effective_principal' => round($effectivePrincipal, 2),
                'affordability' => $affordability,
                'schedule' => $schedule,
            ]
        ]);
    }

    private function generateSchedule(
        float $principal,
        float $monthlyInterest,
        float $monthlyPayment,
        int $periodMonths,
        Carbon $startDate
    ): array {
        $schedule = [];
        $remainingBalance = $principal;
        $monthlyPrincipal = $principal / $periodMonths;

        for ($i = 1; $i <= $periodMonths; $i++) {
            $paymentDate = $startDate->copy()->addMonths($i);
            
            $schedule[] = [
                'installment_number' => $i,
                'payment_date' => $paymentDate->toDateString(),
                'principal' => round($monthlyPrincipal, 2),
                'interest' => round($monthlyInterest, 2),
                'total_payment' => round($monthlyPayment, 2),
                'remaining_balance' => round($remainingBalance - $monthlyPrincipal, 2),
            ];

            $remainingBalance -= $monthlyPrincipal;
        }

        return $schedule;
    }

    private function assessAffordability(array $income, array $expenses, float $monthlyInstallment): array
    {
        $salary = (float) ($income['salary'] ?? 0);
        $otherIncome = (float) ($income['other'] ?? 0);
        $totalIncome = $salary + $otherIncome;

        $totalExpenses = array_sum([
            (float) ($expenses['rent'] ?? 0),
            (float) ($expenses['food'] ?? 0),
            (float) ($expenses['transport'] ?? 0),
            (float) ($expenses['utilities'] ?? 0),
            (float) ($expenses['school_fees'] ?? 0),
            (float) ($expenses['loan_repayments'] ?? 0),
            (float) ($expenses['other'] ?? 0),
        ]);

        $netIncome = $totalIncome - $totalExpenses;
        $affordableLimit = $netIncome * 0.4; // 40% of net income
        
        $isAffordable = $monthlyInstallment <= $affordableLimit;
        
        // Risk Calculation
        $riskLevel = 'Low';
        if (!$isAffordable) {
            $riskLevel = 'High';
        } elseif ($monthlyInstallment > ($affordableLimit * 0.75)) {
            $riskLevel = 'Medium';
        }

        return [
            'total_income' => round($totalIncome, 2),
            'total_expenses' => round($totalExpenses, 2),
            'net_disposable_income' => round($netIncome, 2),
            'max_affordable_installment' => round($affordableLimit, 2),
            'is_affordable' => $isAffordable,
            'risk_level' => $riskLevel,
            'message' => $isAffordable 
                ? 'Client can afford this loan based on net income.' 
                : 'Client may struggle to afford this loan. Consider reducing amount or increasing period.'
        ];
    }

    private function generateInsights(
        float $principal,
        float $interestRate,
        int $periodMonths,
        float $totalInterest,
        float $totalRepayment,
        float $bimaAmount,
        float $processingFee,
        float $netDisbursed,
        string $bimaType
    ): array {
        $insights = [];
        $interestAsPercent = ($totalInterest / $principal) * 100;

        if ($interestAsPercent > 50) {
            $insights[] = [
                'type' => 'warning',
                'message' => "High loan cost: Total interest is {$interestAsPercent}% of principal. Consider negotiating a lower rate."
            ];
        } elseif ($interestAsPercent > 30) {
            $insights[] = [
                'type' => 'info',
                'message' => "Moderate interest cost ({$interestAsPercent}%). Compare with other lenders for best rates."
            ];
        } else {
            $insights[] = [
                'type' => 'success',
                'message' => "Competitive interest rate. Total interest is only {$interestAsPercent}% of principal."
            ];
        }

        if ($periodMonths > 24) {
            $insights[] = [
                'type' => 'info',
                'message' => "Long loan period ({$periodMonths} months). Consider reducing to lower total interest paid."
            ];
        }

        if ($bimaAmount > 0 || $processingFee > 0) {
            $totalDeductions = $bimaAmount + $processingFee;
            $deductionPercent = ($totalDeductions / $principal) * 100;
            
            if ($deductionPercent > 10) {
                $insights[] = [
                    'type' => 'warning',
                    'message' => "High deductions: Only " . number_format($netDisbursed) . " TZS (net) from " . number_format($principal) . " TZS loan. Total deductions: " . number_format($totalDeductions) . " TZS"
                ];
            }

            if ($bimaType === 'deducted') {
                $insights[] = [
                    'type' => 'info',
                    'message' => "Bima (insurance) is deducted upfront - you receive less now but repay the full amount."
                ];
            } else {
                $insights[] = [
                    'type' => 'info',
                    'message' => "Bima (insurance) is financed - added to your loan. You repay it with interest."
                ];
            }
        }

        if ($processingFee > 0) {
            $insights[] = [
                'type' => 'info',
                'message' => "Processing fee of " . number_format($processingFee) . " TZS is applied."
            ];
        }

        return $insights;
    }
}
