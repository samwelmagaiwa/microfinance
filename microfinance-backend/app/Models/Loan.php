<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'loan_number',
        'amount',
        'interest_rate',
        'duration_months',
        'status',
        'disbursed_at',
        'first_payment_date',
        'monthly_payment',
        'total_interest',
        'total_payment',
        'loan_product',
        'repayment_method',
        'repayment_frequency',
        'collateral_description',
        'guarantor1_name',
        'guarantor1_phone',
        'guarantor2_name',
        'guarantor2_phone',
        'is_employed',
        'has_business',
        'collateral_type',
        'collateral_registration_number',
        'collateral_ownership',
        'collateral_current_value',
        'collateral_appearance',
        'approval_status',
        'current_approval_step',
        'loan_officer_id',
        'loan_officer_signature_id',
        'loan_officer_approved_at',
        'loan_officer_hash',
        'loan_manager_id',
        'loan_manager_signature_id',
        'loan_manager_approved_at',
        'loan_manager_hash',
        'general_manager_id',
        'general_manager_signature_id',
        'general_manager_approved_at',
        'general_manager_hash',
        'managing_director_id',
        'managing_director_signature_id',
        'managing_director_approved_at',
        'managing_director_hash',
        'rejection_reason',
        'rejected_by',
        'document_hash',
        'hash_generated_at',
    ];

    protected function casts(): array
    {
        return [
            'disbursed_at' => 'date',
            'first_payment_date' => 'date',
            'monthly_payment' => 'decimal:2',
            'total_interest' => 'decimal:2',
            'total_payment' => 'decimal:2',
        ];
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function nextSchedule()
    {
        return $this->hasOne(LoanSchedule::class)
            ->where('status', 'unpaid')
            ->orderBy('due_date', 'asc');
    }

    public function loanOfficer()
    {
        return $this->belongsTo(User::class, 'loan_officer_id');
    }

    public function loanManager()
    {
        return $this->belongsTo(User::class, 'loan_manager_id');
    }

    public function generalManager()
    {
        return $this->belongsTo(User::class, 'general_manager_id');
    }

    public function managingDirector()
    {
        return $this->belongsTo(User::class, 'managing_director_id');
    }

    public function rejectedByUser()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
