<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Payment;
use App\Models\LoanSchedule;
use App\Enums\BorrowerStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $loanProduct = $request->get('product');

        $loanQuery = Loan::query();
        if ($loanProduct) {
            $loanQuery->where('loan_product', $loanProduct);
        }

        $totalDisbursed = (clone $loanQuery)->where('status', 'active')
            ->sum('amount');

        $totalInterest = (clone $loanQuery)->where('status', 'active')
            ->sum('total_interest');

        $paymentsQuery = Payment::whereBetween('payment_date', [$startDate, $endDate]);
        $totalCollected = (clone $paymentsQuery)->sum('amount');

        $principalCollected = (clone $paymentsQuery)
            ->select(DB::raw('COALESCE(SUM(amount), 0) - COALESCE(SUM(CASE WHEN amount > 0 THEN amount * 0.3 END), 0)'))
            ->value(DB::raw('COALESCE(SUM(amount), 0)'));

        $interestCollected = (clone $paymentsQuery)
            ->select(DB::raw('COALESCE(SUM(amount) * 0.3, 0)'))
            ->value(DB::raw('COALESCE(SUM(amount) * 0.3, 0)'));

        $outstandingLoans = (clone $loanQuery)->where('status', 'active')
            ->withCount(['payments' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('payment_date', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($loan) {
                $paid = $loan->payments()->sum('amount');
                return $loan->total_payment - $paid;
            })
            ->sum();

        $loanCount = (clone $loanQuery)->where('status', 'active')->count();

        $overdueSchedules = LoanSchedule::where('status', 'unpaid')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $collectionRate = 0;
        if ($totalDisbursed > 0) {
            $expected = $totalDisbursed * 0.1;
            $collectionRate = ($totalCollected / $expected) * 100;
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'summary' => [
                    'total_disbursed' => $totalDisbursed,
                    'total_interest_expected' => $totalInterest,
                    'total_collected' => $totalCollected,
                    'principal_collected' => $totalCollected * 0.7,
                    'interest_collected' => $totalCollected * 0.3,
                    'outstanding_balance' => $outstandingLoans,
                    'active_loans_count' => $loanCount,
                    'overdue_schedules_count' => $overdueSchedules,
                    'collection_rate_percent' => round($collectionRate, 2),
                ],
                'by_product' => $this->getReportByProduct($startDate, $endDate),
                'monthly_trend' => $this->getMonthlyTrend($startDate, $endDate),
            ]
        ]);
    }

    public function performance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $borrowerStats = [
            'total' => Borrower::count(),
            'pending' => Borrower::where('status', BorrowerStatus::PENDING_LOAN_MANAGER)->count(),
            'pending_gm' => Borrower::where('status', BorrowerStatus::PENDING_GENERAL_MANAGER)->count(),
            'pending_md' => Borrower::where('status', BorrowerStatus::PENDING_MANAGING_DIRECTOR)->count(),
            'approved' => Borrower::where('status', BorrowerStatus::APPROVED)->count(),
            'rejected' => Borrower::where('status', BorrowerStatus::REJECTED)->count(),
        ];

        $loanStats = [
            'total' => Loan::count(),
            'pending' => Loan::where('status', 'pending')->count(),
            'active' => Loan::where('status', 'active')->count(),
            'completed' => Loan::where('status', 'completed')->count(),
            'defaulted' => Loan::where('status', 'defaulted')->count(),
        ];

        $approvalTime = Borrower::whereNotNull('md_reviewed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, md_reviewed_at)) as avg_hours'))
            ->value('avg_hours');

        return response()->json([
            'status' => 'success',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'borrower_pipeline' => $borrowerStats,
                'loan_portfolio' => $loanStats,
                'performance_metrics' => [
                    'avg_approval_time_hours' => round($approvalTime ?? 0, 1),
                    'approval_rate_percent' => $borrowerStats['total'] > 0 
                        ? round(($borrowerStats['approved'] / $borrowerStats['total']) * 100, 2) 
                        : 0,
                    'rejection_rate_percent' => $borrowerStats['total'] > 0 
                        ? round(($borrowerStats['rejected'] / $borrowerStats['total']) * 100, 2) 
                        : 0,
                ],
            ]
        ]);
    }

    private function getReportByProduct($startDate, $endDate): array
    {
        $products = ['Employment Loan', 'Jikwamue Loan', 'Group Loan'];
        $result = [];

        foreach ($products as $product) {
            $loans = Loan::where('loan_product', $product)->where('status', 'active');
            
            $result[] = [
                'product' => $product,
                'count' => $loans->count(),
                'total_disbursed' => $loans->sum('amount'),
                'total_outstanding' => $loans->sum('total_payment') - Loan::where('loan_product', $product)
                    ->where('status', 'active')
                    ->withCount('payments')
                    ->get()
                    ->sum(fn($l) => $l->payments->sum('amount')),
            ];
        }

        return $result;
    }

    private function getMonthlyTrend($startDate, $endDate): array
    {
        $payments = Payment::select(
            DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->whereBetween('payment_date', [$startDate, $endDate])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $disbursements = Loan::select(
            DB::raw('DATE_FORMAT(disbursed_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
        ->whereBetween('disbursed_at', [$startDate, $endDate])
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'collections' => $payments,
            'disbursements' => $disbursements,
        ];
    }
}
