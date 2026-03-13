<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\BorrowerService;
use Illuminate\Http\Request;

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
            
            // Step 2: Address
            'region' => 'nullable|string',
            'district' => 'nullable|string',
            'ward' => 'nullable|string',
            'village' => 'nullable|string',
            'house_number' => 'nullable|string',
            'residence_description' => 'nullable|string',
            'residence_type' => 'nullable|string',
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
            
            // Financial
            'existing_loans' => 'nullable|boolean',
            'other_institutions' => 'nullable|string',
            'total_existing_amount' => 'nullable|numeric',
            'current_savings' => 'nullable|numeric',
            'monthly_expenses' => 'nullable|numeric',
            'asset_value' => 'nullable|numeric',
            'other_income_financial' => 'nullable|numeric',
            'bank_name' => 'nullable|string',
            'bank_account' => 'nullable|string',
            'mobile_money_number' => 'nullable|string',
            
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
            'repayment_period' => 'nullable|integer',
            'repayment_method' => 'nullable|string',
            'repayment_frequency' => 'nullable|string',
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

            // Jikwamue Collateral
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
            'collateral_land_type' => 'nullable|string',
            'collateral_land_owner' => 'nullable|string',
            'collateral_land_kitalu' => 'nullable|string',
            'collateral_land_plot_no' => 'nullable|string',
            'collateral_land_description' => 'nullable|string',
            'collateral_land_value' => 'nullable|numeric',
            'collateral_land_forced_sale_value' => 'nullable|numeric',
            'project_description' => 'nullable|string',
            'business_legal_status' => 'nullable|string',
            'business_occupancy' => 'nullable|string',
            'landlord_name' => 'nullable|string',
            'landlord_phone' => 'nullable|string',
            'landlord_address' => 'nullable|string',
            'rent_duration' => 'nullable|string',
            'previous_business_location' => 'nullable|string',
            
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
            'employment_guarantee_confirmed' => 'nullable|boolean',
            'risk_description' => 'nullable|string',
        ]);

        $data['user_id'] = auth()->id();
        $data['status'] = \App\Enums\BorrowerStatus::PENDING_LOAN_MANAGER;

        return response()->json([
            'status' => 'success',
            'message' => 'Borrower registered and sent for manager review.',
            'data' => $this->service->createBorrower($data)
        ], 201);
    }

    public function approve(Request $request, $id)
    {
        $borrower = $this->service->getBorrower($id);
        $user = auth()->user();
        $nextStatus = null;

        if ($user->isLoanManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_LOAN_MANAGER) {
            $nextStatus = \App\Enums\BorrowerStatus::PENDING_GENERAL_MANAGER;
        } elseif ($user->isGeneralManager() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_GENERAL_MANAGER) {
            $nextStatus = \App\Enums\BorrowerStatus::PENDING_MANAGING_DIRECTOR;
        } elseif ($user->isManagingDirector() && $borrower->status === \App\Enums\BorrowerStatus::PENDING_MANAGING_DIRECTOR) {
            $nextStatus = \App\Enums\BorrowerStatus::APPROVED;
        }

        if (!$nextStatus) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized or invalid status transition.'], 403);
        }

        $this->service->updateBorrower($id, ['status' => $nextStatus]);

        return response()->json([
            'status' => 'success',
            'message' => 'Application approved to ' . str_replace('_', ' ', $nextStatus->value),
            'data' => $borrower->fresh()
        ]);
    }

    public function reject(Request $request, $id)
    {
        $this->service->updateBorrower($id, ['status' => \App\Enums\BorrowerStatus::REJECTED]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Application rejected.'
        ]);
    }
}
