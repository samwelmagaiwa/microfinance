<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BorrowerDocument;
use App\Models\GroupMemberSignatory;
use App\Services\BorrowerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BorrowerController extends Controller
{
    protected $service;

    public function __construct(BorrowerService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $borrowers = $this->service->getAllBorrowers($status);
        
        return response()->json([
            'status' => 'success',
            'data' => $borrowers
        ]);
    }

    public function show($id)
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->service->getBorrower($id)
        ]);
    }

    public function store(Request $request)
    {
        $request->merge([
            'borrower_account_number' => $request->input('borrower_account_number', $request->input('borrower_account_no')),
            'borrower_oath' => $request->boolean('borrower_oath', $request->boolean('oath_confirmed')),
            'spouse_work_place' => $request->input('spouse_work_place', $request->input('spouse_workplace')),
        ]);

        $data = $request->validate([
            // Step 1: Profile
            'full_name' => 'required|string|max:255',
            'gender' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'id_type' => 'nullable|string',
            'nida_number' => 'required|string|unique:borrowers,nida_number',
            'id_issued_at' => 'nullable|string',
            'id_expiry_date' => 'nullable|date',
            'tin_number' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'alt_phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'marital_status' => 'nullable|string',
            'children_count' => 'nullable|integer',
            'dependents' => 'nullable|integer',
            'spouse_name' => 'nullable|string',
            'spouse_phone' => 'nullable|string',
            'spouse_workplace' => 'nullable|string',
            
            // Section 1.1: Spouse Details (Detailed)
            'spouse_full_name' => 'nullable|string',
            'spouse_dob' => 'nullable|date',
            'spouse_id_number' => 'nullable|string',
            'spouse_occupation' => 'nullable|string',
            'spouse_region' => 'nullable|string',
            'spouse_district' => 'nullable|string',
            'spouse_village' => 'nullable|string',
            'spouse_work_place' => 'nullable|string',
            'spouse_employer' => 'nullable|string',
            'spouse_employer_phone' => 'nullable|string',
            'spouse_monthly_income' => 'nullable|numeric',
            'spouse_consent' => 'nullable|boolean',
            'spouse_consent_thumbprint' => 'nullable|boolean',
            'spouse_signature_name' => 'nullable|string',
            'spouse_signature_date' => 'nullable|date',
            
            // Step 2: Address
            'region' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
            'village' => 'nullable|string',
            'house_number' => 'nullable|string',
            'residence_description' => 'nullable|string',
            'residence_type' => 'nullable|string',
            'residence_type_other' => 'nullable|string',
            'years_at_address' => 'nullable|integer',
            'postal_address' => 'nullable|string',
            
            // Economic / Employment
            'employment_status' => 'nullable|string',
            'employer_name' => 'nullable|string',
            'employer_address' => 'nullable|string',
            'employer_phone' => 'nullable|string',
            'occupation' => 'nullable|string',
            'employee_title' => 'nullable|string',
            'tenure_years' => 'nullable|string',
            'contract_type' => 'nullable|string',
            'contract_duration' => 'nullable|string',
            'contract_start_date' => 'nullable|date',
            'salary_payment_method' => 'nullable|string',
            'monthly_salary' => 'nullable|numeric',
            'net_salary' => 'nullable|numeric',
            'monthly_repayment_capacity' => 'nullable|numeric',
            'other_income' => 'nullable|numeric',
            'other_income_source' => 'nullable|string',
            'office_location' => 'nullable|string',
            'business_name' => 'nullable|string',
            'business_type' => 'nullable|string',
            'business_location' => 'nullable|string',
            'years_in_business' => 'nullable|integer',
            'monthly_revenue' => 'nullable|numeric',
            'business_capital' => 'nullable|numeric',
            'business_has_license' => 'nullable|boolean',
            'business_license_number' => 'nullable|string',
            'average_monthly_profit' => 'nullable|numeric',
            'products_services' => 'nullable|string',
            'project_description' => 'nullable|string',
            'business_legal_status' => 'nullable|string',
            'business_occupancy' => 'nullable|string',
            'landlord_name' => 'nullable|string',
            'landlord_phone' => 'nullable|string',
            'landlord_address' => 'nullable|string',
            'rent_duration' => 'nullable|string',
            'previous_business_location' => 'nullable|string',
            'moving_reason' => 'nullable|string',
            
            // Financial
            'existing_loans' => 'nullable|boolean',
            'other_institutions' => 'nullable|string',
            'total_existing_amount' => 'nullable|numeric',
            'current_savings' => 'nullable|numeric',
            'monthly_expenses' => 'nullable|numeric',
            'asset_value' => 'nullable|numeric',
            'other_income_financial' => 'nullable|numeric',
            'collateral_total_value' => 'nullable|numeric',
            'other_collaterals' => 'nullable|array',
            'other_collaterals.*.name' => 'nullable|string',
            'other_collaterals.*.location' => 'nullable|string',
            'other_collaterals.*.value' => 'nullable|numeric',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'mobile_money_number' => 'nullable|string',

            // Collateral - Vehicle
            'collateral_vehicle_owner' => 'nullable|string',
            'collateral_vehicle_type' => 'nullable|string',
            'collateral_vehicle_reg_no' => 'nullable|string',
            'collateral_vehicle_engine_no' => 'nullable|string',
            'collateral_vehicle_chassis_no' => 'nullable|string',
            'collateral_vehicle_model' => 'nullable|string',
            'collateral_vehicle_color' => 'nullable|string',
            'collateral_vehicle_insurance_type' => 'nullable|string',
            'collateral_vehicle_insurance_provider' => 'nullable|string',
            'collateral_vehicle_value' => 'nullable|numeric',
            'collateral_vehicle_forced_sale_value' => 'nullable|numeric',

            // Collateral - Land
            'collateral_land_type' => 'nullable|string',
            'collateral_land_owner' => 'nullable|string',
            'collateral_land_kitalu' => 'nullable|string',
            'collateral_land_plot_no' => 'nullable|string',
            'collateral_land_description' => 'nullable|string',
            'collateral_land_value' => 'nullable|numeric',
            'collateral_land_forced_sale_value' => 'nullable|numeric',
            
            // Guarantors
            'guarantor1' => 'nullable|array',
            'guarantor2' => 'nullable|array',
            
            // Loan Request
            'loan_product' => 'nullable|string',
            'loan_amount' => 'nullable|numeric',
            'loan_purpose' => 'nullable|string',
            'loan_purpose_biashara' => 'nullable|boolean',
            'loan_purpose_kilimo' => 'nullable|boolean',
            'loan_purpose_ada' => 'nullable|boolean',
            'loan_purpose_ujenzi' => 'nullable|boolean',
            'loan_purpose_ukarabati' => 'nullable|boolean',
            'loan_purpose_hospitali' => 'nullable|boolean',
            'loan_purpose_nyingine' => 'nullable|boolean',
            'loan_purpose_other' => 'nullable|string',
            'loan_main_purpose' => 'nullable|string',
            'repayment_period' => 'nullable|integer',
            'repayment_method' => 'nullable|string',
            'repayment_frequency' => 'nullable|string',
            'repayment_start_date' => 'nullable|date',
            'repayment_capacity' => 'nullable|numeric',
            'interest_rate' => 'nullable|numeric',
            'mandatory_savings' => 'nullable|numeric',
            'repayment_means' => 'nullable|string',
            'net_asset_value' => 'nullable|numeric',

            // ORETHAN Specific
            'pf_number' => 'nullable|string',
            'retirement_date' => 'nullable|date',
            'work_station' => 'nullable|string',
            'group_name' => 'nullable|string',
            'group_id_number' => 'nullable|string',
            'group_position' => 'nullable|string',
            'group_members_count' => 'nullable|integer',
            'group_established_date' => 'nullable|date',
            'group_meeting_place' => 'nullable|string',
            'group_chairman_name' => 'nullable|string',
            'group_chairman_phone' => 'nullable|string',
            'group_secretary_name' => 'nullable|string',
            'group_secretary_phone' => 'nullable|string',
            'group_treasurer_name' => 'nullable|string',
            'group_treasurer_phone' => 'nullable|string',
            'group_bank_account' => 'nullable|string',
            'group_bank_name' => 'nullable|string',
            'group_region' => 'nullable|string',
            'group_district' => 'nullable|string',
            'group_ward' => 'nullable|string',
            'group_village' => 'nullable|string',
            'date_joined_group' => 'nullable|date',
            'local_govt_chairman_name' => 'nullable|string',
            'local_govt_chairman_phone' => 'nullable|string',
            'local_govt_chairman_title' => 'nullable|string',
            'group_members_list' => 'nullable|string',
            'group_liability_agreed' => 'nullable|boolean',
            'group_member_signatories' => 'nullable|array',
            'group_member_signatories.*.sequence' => 'nullable|integer',
            'group_member_signatories.*.name' => 'nullable|string',
            'group_member_signatories.*.phone' => 'nullable|string',
            'group_member_signatories.*.signatureName' => 'nullable|string',
            'group_member_signatories.*.signedAt' => 'nullable|date',
            'group_member_signatories.*.thumbprintConfirmed' => 'nullable|boolean',
            'group_leadership_acknowledgements' => 'nullable|array',
            'group_leadership_acknowledgements.*.role' => 'nullable|string',
            'group_leadership_acknowledgements.*.name' => 'nullable|string',
            'group_leadership_acknowledgements.*.signatureName' => 'nullable|string',
            'group_leadership_acknowledgements.*.signedAt' => 'nullable|date',
            'group_leadership_acknowledgements.*.thumbprintConfirmed' => 'nullable|boolean',

            
            // Internal / Oath
            'borrower_account_number' => 'nullable|string',
            'loan_number' => 'nullable|string',
            'loan_officer_name' => 'nullable|string',
            'registration_date' => 'nullable|date',
            'branch' => 'nullable|string',
            'risk_assessment' => 'nullable|string',
            'officer_remarks' => 'nullable|string',
            'loan_manager_remarks' => 'nullable|string',
            'gm_remarks' => 'nullable|string',
            'md_remarks' => 'nullable|string',
            'md_name' => 'nullable|string',
            'board_decision' => 'nullable|string',
            'board_decision_remarks' => 'nullable|string',
            'board_decision_date' => 'nullable|date',
            'board_member_name' => 'nullable|string',
            'officer_confirmed' => 'nullable|boolean',
            'borrower_oath' => 'nullable|boolean',
            'borrower_oath_date' => 'nullable|date',
            'borrower_oath_thumbprint' => 'nullable|boolean',
            'employment_guarantee_confirmed' => 'nullable|boolean',
            'risk_description' => 'nullable|string',
            'local_govt_verification_date' => 'nullable|date',
            'local_govt_stamp' => 'nullable|boolean',
            'proof_of_address_description' => 'nullable|string',
            'calculated_capacity' => 'nullable|numeric',
            'risk_high' => 'nullable|boolean',
            'risk_medium' => 'nullable|boolean',
            'risk_low' => 'nullable|boolean',

            // Attachment files
            'photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'id_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'spouse_id_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'work_id_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'employer_letter' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'employer_letter_secondary' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'guarantor_ids' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bank_statement' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'payslip' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'contract_copy' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'spouse_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'proof_of_address' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'collateral_attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'local_govt_documents' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'rent_agreement' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'guarantor_intro_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'guarantor_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'borrower_intro_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'group_intro_letter' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'group_members_signed_list' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'group_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'permanent_residence_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_license_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'business_photos' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'guarantor1_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'guarantor2_photo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $attachments = [];
        $attachmentFields = [
            'photo',
            'id_copy',
            'spouse_id_copy',
            'work_id_copy',
            'employer_letter',
            'employer_letter_secondary',
            'guarantor_ids',
            'bank_statement',
            'payslip',
            'contract_copy',
            'spouse_photo',
            'proof_of_address',
            'collateral_attachment',
            'local_govt_documents',
            'rent_agreement',
            'guarantor_intro_letter',
            'guarantor_photo',
            'borrower_intro_letter',
            'group_intro_letter',
            'group_members_signed_list',
            'group_photo',
            'permanent_residence_proof',
            'business_license_document',
            'business_photos',
            'guarantor1_photo',
            'guarantor2_photo',
        ];

        foreach ($attachmentFields as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('borrowers/attachments', 'public');
                $attachments[$field] = $path;
                if ($field === 'photo') {
                    $data['photo_path'] = $path;
                }
            }
        }

        if (!empty($attachments)) {
            $data['attachments'] = $attachments;
        }

        $data['user_id'] = $request->user()?->id;
        $data['status'] = \App\Enums\BorrowerStatus::PENDING_LOAN_MANAGER;

        $documentTypes = [
            'photo',
            'id_copy',
            'spouse_id_copy',
            'work_id_copy',
            'employer_letter',
            'employer_letter_secondary',
            'guarantor_ids',
            'bank_statement',
            'payslip',
            'contract_copy',
            'spouse_photo',
            'proof_of_address',
            'collateral_attachment',
            'local_govt_documents',
            'rent_agreement',
            'guarantor_intro_letter',
            'guarantor_photo',
            'borrower_intro_letter',
            'group_intro_letter',
            'group_members_signed_list',
            'group_photo',
            'permanent_residence_proof',
            'business_license_document',
            'business_photos',
            'guarantor1_photo',
            'guarantor2_photo',
        ];

        $borrower = DB::transaction(function () use ($data, $request, $attachments, $documentTypes) {
            $borrower = $this->service->createBorrower($data);

            foreach ($documentTypes as $type) {
                if (!isset($attachments[$type]) || !$request->hasFile($type)) {
                    continue;
                }

                $file = $request->file($type);
                $borrower->documents()->create([
                    'document_type' => $type,
                    'file_path' => $attachments[$type],
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'is_required' => in_array($type, ['photo', 'id_copy'], true),
                ]);
            }

            $memberSignatories = collect($data['group_member_signatories'] ?? [])
                ->filter(fn ($row) => !empty($row['name']) || !empty($row['phone']) || !empty($row['signatureName']))
                ->map(function ($row) {
                    return [
                        'category' => 'group_member_witness',
                        'role' => 'member',
                        'sequence' => $row['sequence'] ?? null,
                        'name' => $row['name'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'signature_name' => $row['signatureName'] ?? null,
                        'signed_at' => $row['signedAt'] ?? null,
                        'thumbprint_confirmed' => (bool) ($row['thumbprintConfirmed'] ?? false),
                    ];
                })->values()->all();

            $leadershipSignatories = collect($data['group_leadership_acknowledgements'] ?? [])
                ->filter(fn ($row) => !empty($row['name']) || !empty($row['signatureName']))
                ->map(function ($row, $index) {
                    return [
                        'category' => 'group_leadership',
                        'role' => $row['role'] ?? null,
                        'sequence' => $index + 1,
                        'name' => $row['name'] ?? null,
                        'phone' => null,
                        'signature_name' => $row['signatureName'] ?? null,
                        'signed_at' => $row['signedAt'] ?? null,
                        'thumbprint_confirmed' => (bool) ($row['thumbprintConfirmed'] ?? false),
                    ];
                })->values()->all();

            $signatories = array_merge($memberSignatories, $leadershipSignatories);
            if (!empty($signatories)) {
                $borrower->groupSignatories()->createMany($signatories);
            }

            return $borrower->load(['documents', 'groupSignatories']);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Borrower registered and sent for manager review.',
            'data' => $borrower
        ], 201);
    }

    public function approve(Request $request, $id)
    {
        $payload = $this->normalizeReviewPayload($request);
        $validated = validator($payload, [
            'remarks' => 'nullable|string|max:2000',
            'riskAssessment' => 'nullable|string|max:50',
            'riskDescription' => 'nullable|string|max:4000',
            'loanManagerRemarks' => 'nullable|string|max:4000',
            'gmRemarks' => 'nullable|string|max:4000',
            'mdRemarks' => 'nullable|string|max:4000',
            'decision' => 'nullable|string|max:50',
            'decisionRemarks' => 'nullable|string|max:4000',
            'decisionName' => 'nullable|string|max:255',
            'decisionDate' => 'nullable|date',
        ])->validate();

        $borrower = $this->service->getBorrower($id);
        $user = $request->user();
        $nextStatus = null;
        $updateData = [
            'risk_assessment' => $validated['riskAssessment'] ?? $borrower->risk_assessment,
            'risk_description' => $validated['riskDescription'] ?? $borrower->risk_description,
        ];
        $decision = $this->normalizeDecision($validated['decision'] ?? null);

        // Loan Manager Approval
        if ($user->isLoanManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_LOAN_MANAGER) {
            $nextStatus = \App\Enums\BorrowerStatus::PENDING_GENERAL_MANAGER;
            $updateData = array_merge($updateData, [
                'status' => $nextStatus,
                'reviewed_by_loan_manager_id' => $user->id,
                'loan_manager_reviewed_at' => now(),
                'loan_manager_remarks' => $validated['loanManagerRemarks'] ?? $validated['remarks'] ?? $borrower->loan_manager_remarks,
            ]);
        }
        // General Manager Approval
        elseif ($user->isGeneralManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_GENERAL_MANAGER) {
            $nextStatus = \App\Enums\BorrowerStatus::PENDING_MANAGING_DIRECTOR;
            $updateData = array_merge($updateData, [
                'status' => $nextStatus,
                'reviewed_by_gm_id' => $user->id,
                'gm_reviewed_at' => now(),
                'gm_remarks' => $validated['gmRemarks'] ?? $validated['remarks'] ?? $borrower->gm_remarks,
            ]);
        }
        // Managing Director Final Approval
        elseif ($user->isManagingDirector() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_MANAGING_DIRECTOR) {
            $decision = $decision ?? 'Approved';
            $nextStatus = match ($decision) {
                'Conditional' => \App\Enums\BorrowerStatus::CONDITIONAL,
                'Rejected' => \App\Enums\BorrowerStatus::REJECTED,
                default => \App\Enums\BorrowerStatus::APPROVED,
            };
            $updateData = array_merge($updateData, [
                'status' => $nextStatus,
                'reviewed_by_md_id' => $user->id,
                'md_reviewed_at' => now(),
                'md_remarks' => $validated['mdRemarks'] ?? $validated['remarks'] ?? $borrower->md_remarks,
                'board_decision' => $decision,
                'board_decision_remarks' => $validated['decisionRemarks'] ?? $validated['remarks'] ?? $borrower->board_decision_remarks,
                'board_member_name' => $validated['decisionName'] ?? $user->name,
                'board_decision_date' => $validated['decisionDate'] ?? now()->toDateString(),
            ]);

            if ($nextStatus === \App\Enums\BorrowerStatus::REJECTED) {
                $updateData = array_merge($updateData, [
                    'rejected_by_id' => $user->id,
                    'rejected_at' => now(),
                    'rejection_reason' => $validated['decisionRemarks'] ?? $validated['remarks'] ?? 'Rejected by Managing Director',
                ]);
            }

            if ($nextStatus === \App\Enums\BorrowerStatus::APPROVED && $borrower->loan_amount > 0) {
                $loan = $this->createLoanFromBorrower($borrower);
                $updateData['loan_number'] = $loan->loan_number;
            }
        }

        if (!$nextStatus) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized or invalid status transition. Current status: ' . $borrower->status->label()
            ], 403);
        }

        $this->service->updateBorrower($id, $updateData);

        \App\Models\AuditLog::log(
            'borrower_approved',
            'Borrower',
            $borrower->id,
            ['status' => $borrower->status->value],
            ['status' => $nextStatus->value]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Application approved and forwarded to ' . $nextStatus->getCurrentReviewer(),
            'data' => $borrower->fresh()->load(['loanManagerReviewer', 'gmReviewer', 'mdReviewer'])
        ]);
    }

    public function reject(Request $request, $id)
    {
        $payload = $this->normalizeReviewPayload($request);
        $validated = validator($payload, [
            'reason' => 'required|string|max:2000',
            'riskAssessment' => 'nullable|string|max:50',
            'riskDescription' => 'nullable|string|max:4000',
        ])->validate();

        $borrower = $this->service->getBorrower($id);
        $user = $request->user();

        // Check if user can reject at current stage
        $canReject = false;
        if ($user->isLoanManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_LOAN_MANAGER) {
            $canReject = true;
        } elseif ($user->isGeneralManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_GENERAL_MANAGER) {
            $canReject = true;
        } elseif ($user->isManagingDirector() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_MANAGING_DIRECTOR) {
            $canReject = true;
        }

        if (!$canReject) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to reject this application at the current stage.'
            ], 403);
        }

        $updateData = [
            'status' => \App\Enums\BorrowerStatus::REJECTED,
            'rejected_by_id' => $user->id,
            'rejected_at' => now(),
            'rejection_reason' => $validated['reason'],
            'risk_assessment' => $validated['riskAssessment'] ?? $borrower->risk_assessment,
            'risk_description' => $validated['riskDescription'] ?? $borrower->risk_description,
        ];

        if ($user->isLoanManager()) {
            $updateData['reviewed_by_loan_manager_id'] = $user->id;
            $updateData['loan_manager_reviewed_at'] = now();
            $updateData['loan_manager_remarks'] = $validated['reason'];
        } elseif ($user->isGeneralManager()) {
            $updateData['reviewed_by_gm_id'] = $user->id;
            $updateData['gm_reviewed_at'] = now();
            $updateData['gm_remarks'] = $validated['reason'];
        } elseif ($user->isManagingDirector()) {
            $updateData['reviewed_by_md_id'] = $user->id;
            $updateData['md_reviewed_at'] = now();
            $updateData['md_remarks'] = $validated['reason'];
            $updateData['board_decision'] = 'Rejected';
            $updateData['board_decision_remarks'] = $validated['reason'];
            $updateData['board_member_name'] = $user->name;
            $updateData['board_decision_date'] = now()->toDateString();
        }

        $this->service->updateBorrower($id, $updateData);

        \App\Models\AuditLog::log(
            'borrower_rejected',
            'Borrower',
            $borrower->id,
            ['status' => $borrower->status->value],
            ['status' => 'rejected', 'reason' => $validated['reason']]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Application rejected. Reason: ' . $validated['reason']
        ]);
    }

    public function getReviewHistory($id)
    {
        $borrower = $this->service->getBorrower($id);

        return response()->json([
            'status' => 'success',
            'data' => $borrower->getReviewHistory()
        ]);
    }

    private function createLoanFromBorrower($borrower): \App\Models\Loan
    {
        $interestRate = $borrower->interest_rate ?? 15;
        $principal = $borrower->loan_amount;
        $duration = $borrower->repayment_period ?? 12;
        
        $monthlyInterest = $interestRate / 100 / 12;
        $monthlyPayment = $principal * $monthlyInterest * pow(1 + $monthlyInterest, $duration) / (pow(1 + $monthlyInterest, $duration) - 1);
        $totalPayment = $monthlyPayment * $duration;
        $totalInterest = $totalPayment - $principal;

        $loanNumber = 'LN-' . date('Y') . '-' . str_pad($borrower->id, 5, '0', STR_PAD_LEFT);
        $disbursementDate = $borrower->repayment_start_date ?? now()->addMonth();
        $firstPaymentDate = $disbursementDate->copy()->addMonths(1);

        $guarantor1 = $borrower->guarantor1 ?? [];
        $guarantor2 = $borrower->guarantor2 ?? [];

        $collateralParts = [];
        if ($borrower->collateral_vehicle_reg_no) {
            $collateralParts[] = 'Vehicle: ' . $borrower->collateral_vehicle_reg_no;
        }
        if ($borrower->collateral_land_plot_no) {
            $collateralParts[] = 'Land: ' . $borrower->collateral_land_plot_no;
        }

        $loan = \App\Models\Loan::create([
            'borrower_id' => $borrower->id,
            'loan_number' => $loanNumber,
            'amount' => $principal,
            'interest_rate' => $interestRate,
            'duration_months' => $duration,
            'status' => 'active',
            'disbursed_at' => $disbursementDate,
            'first_payment_date' => $firstPaymentDate,
            'monthly_payment' => round($monthlyPayment, 2),
            'total_interest' => round($totalInterest, 2),
            'total_payment' => round($totalPayment, 2),
            'loan_product' => $borrower->loan_product,
            'repayment_method' => $borrower->repayment_method,
            'repayment_frequency' => $borrower->repayment_frequency ?? 'monthly',
            'collateral_description' => implode('; ', $collateralParts),
            'guarantor1_name' => $guarantor1['full_name'] ?? null,
            'guarantor1_phone' => $guarantor1['phone'] ?? null,
            'guarantor2_name' => $guarantor2['full_name'] ?? null,
            'guarantor2_phone' => $guarantor2['phone'] ?? null,
        ]);

        $this->generateLoanSchedules($loan, $disbursementDate, $duration, round($monthlyPayment, 2));

        return $loan;
    }

    private function generateLoanSchedules(\App\Models\Loan $loan, $startDate, int $months, float $monthlyPayment): void
    {
        $schedules = [];
        $currentDate = $startDate->copy()->addMonths(1);

        for ($i = 1; $i <= $months; $i++) {
            $schedules[] = [
                'loan_id' => $loan->id,
                'schedule_number' => $i,
                'due_date' => $currentDate->toDateString(),
                'principal' => round($monthlyPayment * 0.7, 2),
                'interest' => round($monthlyPayment * 0.3, 2),
                'total_payment' => $monthlyPayment,
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $currentDate->addMonths(1);
        }

        \App\Models\LoanSchedule::insert($schedules);
    }

    private function normalizeReviewPayload(Request $request): array
    {
        $payload = $request->all();

        if (isset($payload['remarks']) && is_string($payload['remarks'])) {
            $decoded = json_decode($payload['remarks'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $payload = array_merge($payload, $decoded);
            }
        }

        return $payload;
    }

    private function normalizeDecision(?string $decision): ?string
    {
        if (!$decision) {
            return null;
        }

        return match (strtolower(trim($decision))) {
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'conditional', 'deferred' => 'Conditional',
            default => null,
        };
    }
}
