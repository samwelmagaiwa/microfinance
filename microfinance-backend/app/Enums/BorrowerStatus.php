<?php

namespace App\Enums;

enum BorrowerStatus: string
{
    case PENDING_LOAN_MANAGER = 'pending_loan_manager';
    case PENDING_GENERAL_MANAGER = 'pending_general_manager';
    case PENDING_MANAGING_DIRECTOR = 'pending_managing_director';
    case APPROVED = 'approved';
    case CONDITIONAL = 'conditional';
    case REJECTED = 'rejected';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::PENDING_LOAN_MANAGER => 'Pending Loan Manager Review',
            self::PENDING_GENERAL_MANAGER => 'Pending General Manager Review',
            self::PENDING_MANAGING_DIRECTOR => 'Pending Managing Director Review',
            self::APPROVED => 'Approved',
            self::CONDITIONAL => 'Conditional Approval',
            self::REJECTED => 'Rejected',
        };
    }

    public function getCurrentReviewer(): string
    {
        return match($this) {
            self::PENDING_LOAN_MANAGER => 'Loan Manager',
            self::PENDING_GENERAL_MANAGER => 'General Manager',
            self::PENDING_MANAGING_DIRECTOR => 'Managing Director',
            self::APPROVED => 'Fully Approved',
            self::CONDITIONAL => 'Conditional Approval',
            self::REJECTED => 'Rejected',
        };
    }
}
