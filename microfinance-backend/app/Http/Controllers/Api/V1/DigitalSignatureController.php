<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use App\Models\DigitalSignature;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DigitalSignatureController extends Controller
{
    /**
     * Store a new digital signature
     */
    public function store(Request $request)
    {
        $request->validate([
            'signable_type' => 'required|string|in:borrower,loan',
            'signable_id' => 'required|integer',
            'signature_data' => 'required|string',
            'status' => 'nullable|string|in:approved,rejected,pending',
            'rejection_reason' => 'nullable|string',
        ]);

        $signableType = $request->input('signable_type') === 'borrower' 
            ? Borrower::class 
            : Loan::class;

        $signable = $signableType::findOrFail($request->input('signable_id'));

        $user = Auth::user();
        
        // Generate unique signature ID
        $signatureId = DigitalSignature::generateSignatureId();
        
        // Generate document hash
        $documentHash = DigitalSignature::generateDocumentHash($request->input('signature_data'));
        
        // Generate encryption key (in production, store securely)
        $encryptionKey = bin2hex(random_bytes(32));
        
        // Encrypt the signature data
        $encryptedData = DigitalSignature::encryptSignature(
            $request->input('signature_data'),
            $encryptionKey
        );

        $signature = DigitalSignature::create([
            'signable_type' => $signableType,
            'signable_id' => $signable->id,
            'user_id' => $user->id,
            'signature_id' => $signatureId,
            'signature_data' => $encryptedData,
            'document_hash' => $documentHash,
            'encryption_key' => $encryptionKey,
            'signed_by_name' => $user->name,
            'signed_by_role' => $user->role,
            'signed_at' => now(),
            'status' => $request->input('status', 'approved'),
            'rejection_reason' => $request->input('rejection_reason'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Log the action
        Log::info("Digital signature created", [
            'signature_id' => $signatureId,
            'user_id' => $user->id,
            'signable_type' => $signableType,
            'signable_id' => $signable->id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Digital signature created successfully',
            'data' => [
                'signature_id' => $signatureId,
                'document_hash' => $documentHash,
                'signed_by' => $user->name,
                'signed_by_role' => $user->role,
                'signed_at' => $signature->signed_at->format('Y-m-d H:i:s'),
                'status' => $signature->status,
            ]
        ]);
    }

    /**
     * Get signatures for a borrower or loan
     */
    public function index(Request $request)
    {
        $request->validate([
            'signable_type' => 'required|string|in:borrower,loan',
            'signable_id' => 'required|integer',
        ]);

        $signableType = $request->input('signable_type') === 'borrower' 
            ? Borrower::class 
            : Loan::class;

        $signatures = DigitalSignature::where('signable_type', $signableType)
            ->where('signable_id', $request->input('signable_id'))
            ->orderBy('signed_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $signatures->map(function ($sig) {
                return [
                    'id' => $sig->id,
                    'signature_id' => $sig->signature_id,
                    'signed_by' => $sig->signed_by_name,
                    'signed_by_role' => $sig->signed_by_role,
                    'signed_at' => $sig->signed_at->format('Y-m-d H:i:s'),
                    'status' => $sig->status,
                    'document_hash' => $sig->document_hash,
                ];
            })
        ]);
    }

    /**
     * Verify a signature
     */
    public function verify(Request $request)
    {
        $request->validate([
            'signature_id' => 'required|string',
            'document_data' => 'required|string',
        ]);

        $signature = DigitalSignature::where('signature_id', $request->input('signature_id'))->first();

        if (!$signature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Signature not found',
            ], 404);
        }

        // Verify document hash
        $currentHash = DigitalSignature::generateDocumentHash($request->input('document_data'));
        $isValid = $signature->document_hash === $currentHash;

        return response()->json([
            'status' => 'success',
            'data' => [
                'is_valid' => $isValid,
                'signature_id' => $signature->signature_id,
                'signed_by' => $signature->signed_by_name,
                'signed_by_role' => $signature->signed_by_role,
                'signed_at' => $signature->signed_at->format('Y-m-d H:i:s'),
                'status' => $signature->status,
                'document_hash_match' => $isValid,
            ]
        ]);
    }

    /**
     * Get approval workflow status
     */
    public function approvalStatus(Request $request)
    {
        $request->validate([
            'signable_type' => 'required|string|in:borrower,loan',
            'signable_id' => 'required|integer',
        ]);

        $signableType = $request->input('signable_type') === 'borrower' 
            ? Borrower::class 
            : Loan::class;

        $signatures = DigitalSignature::where('signable_type', $signableType)
            ->where('signable_id', $request->input('signable_id'))
            ->orderBy('signed_at', 'asc')
            ->get();

        // Build approval workflow
        $workflow = [
            'loan_officer' => null,
            'loan_manager' => null,
            'general_manager' => null,
            'managing_director' => null,
        ];

        $roleMap = [
            'loan_officer' => 'loan_officer',
            'loan_manager' => 'loan_manager', 
            'general_manager' => 'general_manager',
            'managing_director' => 'managing_director',
        ];

        foreach ($signatures as $sig) {
            $role = $sig->user->role ?? 'unknown';
            if (isset($roleMap[$role])) {
                $workflow[$roleMap[$role]] = [
                    'signed' => true,
                    'signature_id' => $sig->signature_id,
                    'signed_by' => $sig->signed_by_name,
                    'signed_at' => $sig->signed_at->format('Y-m-d H:i:s'),
                    'document_hash' => $sig->document_hash,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $workflow
        ]);
    }

    /**
     * Reject a signature
     */
    public function reject(Request $request)
    {
        $request->validate([
            'signature_id' => 'required|string',
            'rejection_reason' => 'required|string',
        ]);

        $signature = DigitalSignature::where('signature_id', $request->input('signature_id'))->first();

        if (!$signature) {
            return response()->json([
                'status' => 'error',
                'message' => 'Signature not found',
            ], 404);
        }

        $signature->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Signature rejected successfully'
        ]);
    }
}
