<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    protected $service;

    public function __construct(LoanService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getAllLoans()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getLoan($id)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'borrower_id' => 'required|exists:borrowers,id',
            'amount' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'duration_months' => 'required|integer',
        ]);

        $data['status'] = 'pending';

        return response()->json([
            'status' => 'success',
            'message' => 'Loan application submitted successfully.',
            'data' => $this->service->createLoan($data)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->updateLoan($id, $request->all())
        ]);
    }

    public function destroy($id)
    {
        $this->service->deleteLoan($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Loan record removed successfully.'
        ]);
    }

    public function approve($id)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Loan has been approved and activated.',
            'data' => $this->service->approveLoan($id)
        ]);
    }

    public function clientLoans(Request $request)
    {
        $borrower = \App\Models\Borrower::where('user_id', $request->user()->id)->first();
        if (!$borrower) {
            return response()->json(['status' => 'error', 'message' => 'Profile not found.'], 404);
        }

        $loans = \App\Models\Loan::where('borrower_id', $borrower->id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $loans
        ]);
    }
}
