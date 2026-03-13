<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    use HasFactory;

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
        'full_name',
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
        'photo_path',
        'region',
        'district',
        'ward',
        'village',
        'house_number',
        'residence_description',
        'residence_type',
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
        'existing_loans',
        'other_institutions',
        'total_existing_amount',
        'current_savings',
        'monthly_expenses',
        'asset_value',
        'other_income_financial',
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
        'repayment_period',
        'repayment_method',
        'repayment_frequency',
        'repayment_capacity',
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
            'contract_start_date' => 'date',
            'registration_date' => 'date',
            'board_decision_date' => 'date',
            'guarantor1' => 'array',
            'guarantor2' => 'array',
            'existing_loans' => 'boolean',
            'spouse_consent' => 'boolean',
            'loan_purpose_biashara' => 'boolean',
            'loan_purpose_kilimo' => 'boolean',
            'loan_purpose_ada' => 'boolean',
            'loan_purpose_ujenzi' => 'boolean',
            'loan_purpose_ukarabati' => 'boolean',
            'loan_purpose_hospitali' => 'boolean',
            'loan_purpose_nyingine' => 'boolean',
            'group_liability_agreed' => 'boolean',
            'officer_confirmed' => 'boolean',
            'borrower_oath' => 'boolean',
            'employment_guarantee_confirmed' => 'boolean',
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

    public function activeLoan()
    {
        return $this->hasOne(Loan::class)->where('status', 'active');
    }
}
