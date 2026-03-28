<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Enums\BorrowerStatus;
use Illuminate\Http\Request;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        [$actionRequired, $recentActivity, $targetStatus] = $this->buildApprovalQueues($user);

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_borrowers' => Borrower::count(),
                'total_loans' => Loan::count(),
                'active_loans' => Loan::where('status', 'active')->count(),
                'total_payments' => Payment::sum('amount'),
                'recent_loans' => Loan::with('borrower')->latest()->take(5)->get(),
                'workflow' => [
                    'current_role' => $user?->role?->value,
                    'target_status' => $targetStatus?->value,
                    'action_required_count' => $actionRequired->count(),
                    'action_required' => $actionRequired,
                    'recent_activity' => $recentActivity,
                    'status_breakdown' => [
                        BorrowerStatus::PENDING_LOAN_MANAGER->value => Borrower::where('status', BorrowerStatus::PENDING_LOAN_MANAGER)->count(),
                        BorrowerStatus::PENDING_GENERAL_MANAGER->value => Borrower::where('status', BorrowerStatus::PENDING_GENERAL_MANAGER)->count(),
                        BorrowerStatus::PENDING_MANAGING_DIRECTOR->value => Borrower::where('status', BorrowerStatus::PENDING_MANAGING_DIRECTOR)->count(),
                        BorrowerStatus::APPROVED->value => Borrower::where('status', BorrowerStatus::APPROVED)->count(),
                        BorrowerStatus::CONDITIONAL->value => Borrower::where('status', BorrowerStatus::CONDITIONAL)->count(),
                        BorrowerStatus::REJECTED->value => Borrower::where('status', BorrowerStatus::REJECTED)->count(),
                    ],
                ],
            ]
        ]);
    }

    public function clientSummary(Request $request)
    {
        $borrower = Borrower::where('user_id', $request->user()->id)->first();
        if (!$borrower) {
            return response()->json([
                'status' => 'error',
                'message' => 'Client profile not found. Please contact the administrator.'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'borrower' => $borrower,
                'summary' => [
                    'total_loanable' => Loan::where('borrower_id', $borrower->id)->sum('amount'),
                    'active_loans' => Loan::where('borrower_id', $borrower->id)->where('status', 'active')->count(),
                    'total_paid' => Payment::whereHas('loan', function($q) use($borrower) {
                        $q->where('borrower_id', $borrower->id);
                    })->sum('amount')
                ]
            ]
        ]);
    }

    private function buildApprovalQueues($user): array
    {
        if (!$user || $user->isClient()) {
            return [collect(), collect(), null];
        }

        $targetStatus = match (true) {
            $user->isLoanManager() => BorrowerStatus::PENDING_LOAN_MANAGER,
            $user->isGeneralManager() => BorrowerStatus::PENDING_GENERAL_MANAGER,
            $user->isManagingDirector() => BorrowerStatus::PENDING_MANAGING_DIRECTOR,
            default => null,
        };

        $baseQuery = Borrower::query()
            ->with(['loanManagerReviewer', 'gmReviewer', 'mdReviewer'])
            ->orderByDesc('registration_date')
            ->orderByDesc('created_at');

        $actionRequired = $targetStatus
            ? (clone $baseQuery)->where('status', $targetStatus)->take(10)->get()
            : collect();

        $recentActivity = match (true) {
            $user->isLoanManager() => (clone $baseQuery)
                ->where(function ($query) use ($user) {
                    $query->where('reviewed_by_loan_manager_id', $user->id)
                        ->orWhere(function ($nested) use ($user) {
                            $nested->where('rejected_by_id', $user->id)
                                ->whereNotNull('loan_manager_reviewed_at');
                        });
                })
                ->take(10)
                ->get(),
            $user->isGeneralManager() => (clone $baseQuery)
                ->where(function ($query) use ($user) {
                    $query->where('reviewed_by_gm_id', $user->id)
                        ->orWhere(function ($nested) use ($user) {
                            $nested->where('rejected_by_id', $user->id)
                                ->whereNotNull('gm_reviewed_at');
                        });
                })
                ->take(10)
                ->get(),
            $user->isManagingDirector() => (clone $baseQuery)
                ->where(function ($query) use ($user) {
                    $query->where('reviewed_by_md_id', $user->id)
                        ->orWhere('rejected_by_id', $user->id);
                })
                ->take(10)
                ->get(),
            default => (clone $baseQuery)->take(10)->get(),
        };

        return [$actionRequired, $recentActivity, $targetStatus];
    }
}
