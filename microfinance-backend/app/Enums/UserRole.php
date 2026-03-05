<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGING_DIRECTOR = 'managing_director';
    case GENERAL_MANAGER = 'general_manager';
    case LOAN_MANAGER = 'loan_manager';
    case LOAN_OFFICER = 'loan_officer';
    case SECRETARY = 'secretary';
    case CLIENT = 'client';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
