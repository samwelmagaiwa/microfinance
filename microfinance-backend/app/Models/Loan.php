<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'amount',
        'interest_rate',
        'duration_months',
        'status', // pending, active, completed, defaulted
    ];

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
}
