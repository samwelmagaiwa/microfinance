<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffordabilityAssessment extends Model
{
    protected $fillable = [
        'borrower_id',
        'user_id',
        'salary',
        'business_income',
        'other_income',
        'total_income',
        'rent',
        'food',
        'transport',
        'utilities',
        'school_fees',
        'existing_loan_repayments',
        'other_expenses',
        'total_expenses',
        'net_disposable_income',
        'max_affordable_installment',
        'affordability_threshold_percent',
        'risk_level',
        'risk_message',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'business_income' => 'decimal:2',
        'other_income' => 'decimal:2',
        'total_income' => 'decimal:2',
        'rent' => 'decimal:2',
        'food' => 'decimal:2',
        'transport' => 'decimal:2',
        'utilities' => 'decimal:2',
        'school_fees' => 'decimal:2',
        'existing_loan_repayments' => 'decimal:2',
        'other_expenses' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_disposable_income' => 'decimal:2',
        'max_affordable_installment' => 'decimal:2',
        'affordability_threshold_percent' => 'decimal:2',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
