<?php

namespace App\Enums;

enum BorrowerStatus: string
{
    case PENDING_LOAN_MANAGER = 'pending_loan_manager';
    case PENDING_GENERAL_MANAGER = 'pending_general_manager';
    case PENDING_MANAGING_DIRECTOR = 'pending_managing_director';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
