<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrower;
use App\Models\Loan;
use App\Models\Payment;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_borrowers' => Borrower::count(),
                'total_loans' => Loan::count(),
                'active_loans' => Loan::where('status', 'active')->count(),
                'total_payments' => Payment::sum('amount'),
                'recent_loans' => Loan::with('borrower')->latest()->take(5)->get(),
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
}
