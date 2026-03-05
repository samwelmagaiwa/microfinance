<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'due_date',
        'principal_amount',
        'interest_amount',
        'total_due',
        'status', // unpaid, partially_paid, paid
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
