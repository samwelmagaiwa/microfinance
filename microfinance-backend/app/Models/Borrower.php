<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

    protected $appends = [
        'final_decision',
        'decision_remarks',
        'decision_by_name',
        'decision_date',
    ];

    protected static function booted()
    {
        static::saving(function ($borrower) {
            if ($borrower->date_of_birth) {
                $borrower->age = $borrower->date_of_birth->age;
            }
        });
    }

    protected $fillable = [
        'user_id',
        'reviewed_by_loan_manager_id',
        'reviewed_by_gm_id',
        'reviewed_by_md_id',
        'rejected_by_id',
        'loan_manager_reviewed_at',
        'gm_reviewed_at',
        'md_reviewed_at',
        'rejected_at',
        'loan_manager_remarks',
        'gm_remarks',
        'md_remarks',
        'rejection_reason',
        'full_name',
        'nick_name',
        'gender',
        'date_of_birth',
        'age',
        'id_type',
        'nida_number',
        'id_issued_at',
        'id_expiry_date',
        'tin_number',
        'phone',
        'alt_phone',
        'email',
        'marital_status',
        'children_count',
        'dependents',
        'spouse_name',
        'spouse_phone',
        'spouse_workplace',
        'spouse_full_name',
        'spouse_dob',
        'spouse_id_number',
        'spouse_occupation',
        'spouse_region',
        'spouse_district',
        'spouse_village',
        'spouse_work_place',
        'spouse_employer',
        'spouse_employer_phone',
        'spouse_monthly_income',
        'spouse_consent',
        'spouse_consent_thumbprint',
        'spouse_signature_name',
        'spouse_signature_date',
        'photo_path',
        'attachments',
        'proof_of_address_description',
        'region',
        'district',
        'ward',
        'village',
        'house_number',
        'residence_description',
        'residence_type',
        'residence_type_other',
        'years_at_address',
        'postal_address',
        'employment_status',
        'employer_name',
        'employer_address',
        'employer_phone',
        'employee_title',
        'tenure_years',
        'contract_type',
        'contract_duration',
        'contract_start_date',
        'salary_payment_method',
        'occupation',
        'monthly_salary',
        'monthly_repayment_capacity',
        'other_income',
        'other_income_source',
        'business_name',
        'business_type',
        'business_location',
        'years_in_business',
        'monthly_revenue',
        'business_capital',
        'business_has_license',
        'business_license_number',
        'average_monthly_profit',
        'products_services',
        'existing_loans',
        'other_institutions',
        'total_existing_amount',
        'current_savings',
        'monthly_expenses',
        'asset_value',
        'other_income_financial',
        'collateral_total_value',
        'other_collaterals',
        'bank_name',
        'bank_account',
        'mobile_money_number',
        'guarantor1',
        'guarantor2',
        'loan_product',
        'loan_amount',
        'loan_purpose',
        'loan_purpose_biashara',
        'loan_purpose_kilimo',
        'loan_purpose_ada',
        'loan_purpose_ujenzi',
        'loan_purpose_ukarabati',
        'loan_purpose_hospitali',
        'loan_purpose_nyingine',
        'loan_purpose_other',
        'loan_main_purpose',
        'repayment_period',
        'repayment_method',
        'repayment_frequency',
        'repayment_capacity',
        'repayment_start_date',
        'interest_rate',
        'mandatory_savings',
        'borrower_account_number',
        'loan_number',
        'loan_officer_name',
        'registration_date',
        'branch',
        'risk_assessment',
        'status',
        'officer_remarks',
        'loan_manager_remarks',
        'gm_remarks',
        'board_decision',
        'board_decision_remarks',
        'board_decision_date',
        'board_member_name',
        'officer_confirmed',
        'borrower_oath',
        'borrower_oath_date',
        'borrower_oath_thumbprint',
        'pf_number',
        'retirement_date',
        'work_station',
        'group_name',
        'group_id_number',
        'group_position',
        'group_members_count',
        'group_established_date',
        'group_meeting_place',
        'group_chairman_name',
        'group_chairman_phone',
        'group_secretary_name',
        'group_secretary_phone',
        'group_treasurer_name',
        'group_treasurer_phone',
        'group_bank_account',
        'group_bank_name',
        'group_region',
        'group_district',
        'group_ward',
        'group_village',
        'date_joined_group',
        'local_govt_chairman_name',
        'local_govt_chairman_phone',
        'local_govt_chairman_title',
        'group_members_list',
        'group_liability_agreed',
        'group_member_signatories',
        'group_leadership_acknowledgements',

        // Jikwamue specific
        'collateral_vehicle_owner',
        'collateral_vehicle_type',
        'collateral_vehicle_reg_no',
        'collateral_vehicle_engine_no',
        'collateral_vehicle_chassis_no',
        'collateral_vehicle_model',
        'collateral_vehicle_color',
        'collateral_vehicle_insurance_type',
        'collateral_vehicle_insurance_provider',
        'collateral_vehicle_value',
        'collateral_vehicle_forced_sale_value',
        'collateral_land_type',
        'collateral_land_owner',
        'collateral_land_kitalu',
        'collateral_land_plot_no',
        'collateral_land_description',
        'collateral_land_value',
        'collateral_land_forced_sale_value',
        'project_description',
        'business_legal_status',
        'business_occupancy',
        'landlord_name',
        'landlord_phone',
        'landlord_address',
        'rent_duration',
        'previous_business_location',
        'risk_description',
        'md_remarks',
        'md_name',
        'employment_guarantee_confirmed',
        'office_location',
        'repayment_means',
        'net_asset_value',

        // Employment & Business Status (new 2026-04-11)
        'is_employed',
        'has_business',

        // Collateral (for non-employed)
        'collateral_type',
        'collateral_registration_number',
        'collateral_ownership',
        'collateral_current_value',
        'collateral_appearance',

        // Employment Loan specific (added 2026-03-16)
        'net_salary',
        'moving_reason',
        'calculated_capacity',
        'local_govt_verification_date',
        'local_govt_stamp',
        'risk_high',
        'risk_medium',
        'risk_low',

        // Digital Signature Fields
        'loan_officer_hash',
        'loan_officer_signed_at',
        'loan_manager_hash',
        'loan_manager_signed_at',
        'gm_hash',
        'gm_signed_at',
        'md_hash',
        'md_signed_at',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'id_expiry_date' => 'date',
            'retirement_date' => 'date',
            'group_established_date' => 'date',
            'date_joined_group' => 'date',
            'spouse_dob' => 'date',
            'spouse_signature_date' => 'date',
            'contract_start_date' => 'date',
            'registration_date' => 'date',
            'board_decision_date' => 'date',
            'guarantor1' => 'array',
            'guarantor2' => 'array',
            'attachments' => 'array',
            'other_collaterals' => 'array',
            'group_member_signatories' => 'array',
            'group_leadership_acknowledgements' => 'array',
            'existing_loans' => 'boolean',
            'spouse_consent' => 'boolean',
            'spouse_consent_thumbprint' => 'boolean',
            'loan_purpose_biashara' => 'boolean',
            'loan_purpose_kilimo' => 'boolean',
            'loan_purpose_ada' => 'boolean',
            'loan_purpose_ujenzi' => 'boolean',
            'loan_purpose_ukarabati' => 'boolean',
            'loan_purpose_hospitali' => 'boolean',
            'loan_purpose_nyingine' => 'boolean',
            'group_liability_agreed' => 'boolean',
            'business_has_license' => 'boolean',
            'officer_confirmed' => 'boolean',
            'borrower_oath' => 'boolean',
            'borrower_oath_thumbprint' => 'boolean',
            'employment_guarantee_confirmed' => 'boolean',
            'local_govt_stamp' => 'boolean',
            'risk_high' => 'boolean',
            'risk_medium' => 'boolean',
            'risk_low' => 'boolean',
            'borrower_oath_date' => 'date',
            'repayment_start_date' => 'date',
            'local_govt_verification_date' => 'date',
            'status' => \App\Enums\BorrowerStatus::class,
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Loan::class);
    }

    public function documents()
    {
        return $this->hasMany(BorrowerDocument::class);
    }

    public function groupSignatories()
    {
        return $this->hasMany(GroupMemberSignatory::class);
    }

    public function affordabilityAssessments()
    {
        return $this->hasMany(AffordabilityAssessment::class);
    }

    public function latestAffordabilityAssessment()
    {
        return $this->hasOne(AffordabilityAssessment::class)->latestOfMany();
    }

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }

    public function loanManagerReviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_loan_manager_id');
    }

    public function gmReviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_gm_id');
    }

    public function mdReviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by_md_id');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by_id');
    }

    public function getReviewHistory(): array
    {
        $history = [];

        if ($this->reviewed_by_loan_manager_id) {
            $history[] = [
                'stage' => 'Loan Manager Review',
                'reviewer' => $this->loanManagerReviewer?->name ?? 'Unknown',
                'date' => $this->loan_manager_reviewed_at,
                'remarks' => $this->loan_manager_remarks,
                'action' => 'Approved',
                'signature_hash' => $this->loan_manager_hash,
            ];
        }

        if ($this->reviewed_by_gm_id) {
            $history[] = [
                'stage' => 'General Manager Review',
                'reviewer' => $this->gmReviewer?->name ?? 'Unknown',
                'date' => $this->gm_reviewed_at,
                'remarks' => $this->gm_remarks,
                'action' => 'Approved',
                'signature_hash' => $this->gm_hash,
            ];
        }

        if ($this->reviewed_by_md_id) {
            $history[] = [
                'stage' => 'Managing Director Review',
                'reviewer' => $this->mdReviewer?->name ?? 'Unknown',
                'date' => $this->md_reviewed_at,
                'remarks' => $this->md_remarks,
                'action' => $this->board_decision ?? 'Final Approval',
                'signature_hash' => $this->md_hash,
            ];
        }

        if ($this->rejected_by_id) {
            $history[] = [
                'stage' => $this->status->getCurrentReviewer() . ' Review',
                'reviewer' => $this->rejectedBy?->name ?? 'Unknown',
                'date' => $this->rejected_at,
                'remarks' => $this->rejection_reason,
                'action' => 'Rejected',
            ];
        }

        return $history;
    }

    protected function finalDecision(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->board_decision,
        );
    }

    protected function decisionRemarks(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->board_decision_remarks,
        );
    }

    protected function decisionByName(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->board_member_name,
        );
    }

    protected function decisionDate(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->board_decision_date,
        );
    }
}
