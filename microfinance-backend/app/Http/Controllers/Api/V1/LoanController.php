<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

class LoanController extends Controller
{
    protected $service;

    public function __construct(LoanService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $user = request()->user();
        $all = request()->query('all');
        
        $query = \App\Models\Loan::with('borrower');
        
        // If 'all' parameter is provided, skip role filtering (for loan history)
        if ($all) {
            return response()->json([
                'status' => 'success',
                'data' => $query->orderBy('id', 'desc')->get()
            ]);
        }
        
        // Role-based filtering for approvals
        if ($user->isLoanManager()) {
            // LM sees: pending_loan_manager OR already approved by LM OR approved OR at loan_manager step
            $query->where(function($q) {
                $q->where('approval_status', 'pending_loan_manager')
                  ->orWhereNotNull('loan_manager_hash')
                  ->orWhere('approval_status', 'approved')
                  ->orWhere('current_approval_step', 'loan_manager');
            });
        } elseif ($user->isGeneralManager()) {
            // GM sees: pending_general_manager OR already approved by GM OR approved (LM approved) OR at general_manager step
            $query->where(function($q) {
                $q->where('approval_status', 'pending_general_manager')
                  ->orWhereNotNull('general_manager_hash')
                  ->orWhere('approval_status', 'approved')
                  ->orWhere('current_approval_step', 'general_manager');
            });
        } elseif ($user->isManagingDirector()) {
            // MD sees: pending_managing_director OR already approved by MD OR approved (GM approved) OR at managing_director step
            $query->where(function($q) {
                $q->where('approval_status', 'pending_managing_director')
                  ->orWhereNotNull('managing_director_hash')
                  ->orWhere('approval_status', 'approved')
                  ->orWhere('current_approval_step', 'managing_director');
            });
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $query->orderBy('id', 'desc')->get()
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
        $data['approval_status'] = 'pending_loan_officer';
        $data['current_approval_step'] = 'loan_officer';
        $loan = $this->service->createLoan($data);

        \App\Models\AuditLog::log(
            'loan_created',
            'Loan',
            $loan->id,
            null,
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Loan application submitted successfully.',
            'data' => $loan
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
        $loan = $this->service->getLoan($id);
        $oldStatus = $loan->status;
        
        $loan = $this->service->approveLoan($id);

        \App\Models\AuditLog::log(
            'loan_approved',
            'Loan',
            $id,
            ['status' => $oldStatus],
            ['status' => 'active']
        );

            return response()->json([
                'status' => 'success',
                'message' => 'Loan has been rejected.',
                'data' => $loan
            ]);
        }

        public function disburse(Request $request, $id)
        {
            $request->validate([
                'disbursementAmount' => 'required|numeric|min:0',
                'paymentMethod' => 'required|in:bank,mobile,cash',
                'transactionReference' => 'required|string',
                'disbursementDate' => 'required|date',
            ]);

            $user = request()->user();
            
            // Only loan_manager can disburse
            if (!$user->isLoanManager()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only Loan Manager can process disbursement.'
                ], 403);
            }

            $loan = \App\Models\Loan::findOrFail($id);
            
            // Check if already disbursed
            if ($loan->disbursed_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Loan has already been disbursed.'
                ], 422);
            }

            // Check if loan is approved
            if (!in_array($loan->approval_status, ['approved', 'pending_disbursement'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Loan must be approved before disbursement.'
                ], 422);
            }

            $loan->update([
                'status' => 'disbursed',
                'disbursed_at' => $request->disbursementDate,
                'disbursed_by' => $user->id,
                'disbursement_method' => $request->paymentMethod,
                'disbursement_ref' => $request->transactionReference,
                'disbursed_amount' => $request->disbursementAmount,
            ]);

            \App\Models\AuditLog::log(
                'loan_disbursed',
                'Loan',
                $id,
                null,
                [
                    'disbursed_by' => $user->id,
                    'amount' => $request->disbursementAmount,
                    'method' => $request->paymentMethod,
                    'reference' => $request->transactionReference,
                    'date' => $request->disbursementDate,
                ]
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Disbursement completed successfully.',
                'data' => $loan->fresh()
            ]);
        }

    public function approveStep(Request $request, $id)
    {
        $request->validate([
            'signature_data' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = $request->user();
        $loan = \App\Models\Loan::findOrFail($id);
        
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid password.'
            ], 422);
        }

        $currentStep = $loan->current_approval_step;
        $userRole = $user->role->value;

        $stepRoles = [
            'loan_officer' => 'loan_officer',
            'loan_manager' => 'loan_manager',
            'general_manager' => 'general_manager',
            'managing_director' => 'managing_director',
        ];

        $allowedSteps = [
            'loan_officer' => ['loan_officer'],
            'loan_manager' => ['loan_manager'],
            'general_manager' => ['general_manager'],
            'managing_director' => ['managing_director'],
        ];

        if (!isset($allowedSteps[$currentStep]) || !in_array($userRole, $allowedSteps[$currentStep])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to approve this step.'
            ], 403);
        }

        $approvedLoan = DB::transaction(function () use ($request, $loan, $user, $currentStep, $id) {
            $signatureData = $request->input('signature_data');
            $signedAt = now();
            $documentData = json_encode([
                'loan_id' => $loan->id,
                'borrower_id' => $loan->borrower_id,
                'amount' => $loan->amount,
                'step' => $currentStep,
                'user_id' => $user->id,
                'timestamp' => $signedAt->toIso8601String(),
            ]);

            $hash = hash('sha256', $documentData . $signatureData);
            $encryptedSignature = Crypt::encryptString($signatureData);

            $signature = \App\Models\DigitalSignature::create([
                'user_id' => $user->id,
                'signable_type' => 'App\Models\Loan',
                'signable_id' => $loan->id,
                'signature_id' => \App\Models\DigitalSignature::generateSignatureId(),
                'signature_data' => $encryptedSignature,
                'hash' => $hash,
                'document_hash' => $hash,
                'signed_by_name' => $user->name,
                'signed_by_role' => $user->role->value,
                'signed_at' => $signedAt,
                'status' => 'approved',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $updateData = [
                $currentStep . '_id' => $user->id,
                $currentStep . '_signature_id' => $signature->id,
                $currentStep . '_approved_at' => $signedAt,
                $currentStep . '_hash' => $hash,
            ];

            $nextSteps = [
                'loan_officer' => 'loan_manager',
                'loan_manager' => 'general_manager',
                'general_manager' => 'managing_director',
                'managing_director' => 'completed',
            ];

            if ($nextSteps[$currentStep] === 'completed') {
                $updateData['approval_status'] = 'approved';
                $updateData['status'] = 'active';
                $updateData['current_approval_step'] = 'completed';
            } else {
                $updateData['current_approval_step'] = $nextSteps[$currentStep];
                $updateData['approval_status'] = 'pending_' . $nextSteps[$currentStep];
            }

            $loan->update($updateData);

            \App\Models\AuditLog::log(
                'loan_' . $currentStep . '_approved',
                'Loan',
                $id,
                ['step' => $currentStep],
                ['step' => $currentStep, 'signature_id' => $signature->id, 'hash' => $hash]
            );

            return [$loan->fresh(), $signature];
        });

        [$loan, $signature] = $approvedLoan;

        return response()->json([
            'status' => 'success',
            'message' => 'Approval step completed successfully.',
            'data' => [
                'loan' => $loan->fresh(),
                'signature' => [
                    'id' => $signature->id,
                    'hash' => $signature->hash,
                    'approved_at' => $signature->created_at,
                ]
            ]
        ]);
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        $user = request()->user();
        $loan = \App\Models\Loan::findOrFail($id);

        $loan->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_by' => $user->id,
        ]);

        \App\Models\AuditLog::log(
            'loan_rejected',
            'Loan',
            $id,
            null,
            ['reason' => $request->reason, 'rejected_by' => $user->id]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Loan has been rejected.',
            'data' => $loan->fresh()
        ]);
    }

    public function getApprovalStatus($id)
    {
        $loan = \App\Models\Loan::with([
            'loanOfficer',
            'loanManager',
            'generalManager',
            'managingDirector',
        ])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'approval_status' => $loan->approval_status,
                'current_step' => $loan->current_approval_step,
                'loan_officer' => $loan->loan_officer_id ? [
                    'name' => $loan->loanOfficer?->name,
                    'approved_at' => $loan->loan_officer_approved_at,
                    'hash' => $loan->loan_officer_hash,
                ] : null,
                'loan_manager' => $loan->loan_manager_id ? [
                    'name' => $loan->loanManager?->name,
                    'approved_at' => $loan->loan_manager_approved_at,
                    'hash' => $loan->loan_manager_hash,
                ] : null,
                'general_manager' => $loan->general_manager_id ? [
                    'name' => $loan->generalManager?->name,
                    'approved_at' => $loan->general_manager_approved_at,
                    'hash' => $loan->general_manager_hash,
                ] : null,
                'managing_director' => $loan->managing_director_id ? [
                    'name' => $loan->managingDirector?->name,
                    'approved_at' => $loan->managing_director_approved_at,
                    'hash' => $loan->managing_director_hash,
                ] : null,
                'rejection' => $loan->rejection_reason ? [
                    'reason' => $loan->rejection_reason,
                    'rejected_by' => $loan->rejectedByUser?->name,
                ] : null,
            ]
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
