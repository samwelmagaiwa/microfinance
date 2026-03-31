<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $service;

    public function __construct(PaymentService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getAllPayments()
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getPayment($id)
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount' => 'required|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string',
        ]);

        $payment = $this->service->createPayment($data);

        \App\Models\AuditLog::log(
            'payment_created',
            'Payment',
            $payment->id,
            null,
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Payment recorded successfully.',
            'data' => $payment
        ], 201);
    }

    public function clientPayments(Request $request)
    {
        $borrower = \App\Models\Borrower::where('user_id', $request->user()->id)->first();
        if (!$borrower) {
            return response()->json(['status' => 'error', 'message' => 'Profile not found.'], 404);
        }

        $payments = \App\Models\Payment::whereHas('loan', function($q) use($borrower) {
            $q->where('borrower_id', $borrower->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => $payments
        ]);
    }
}
