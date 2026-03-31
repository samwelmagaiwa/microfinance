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
}
