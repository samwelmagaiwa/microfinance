<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::ADMIN,
            ],
            [
                'name' => 'Managing Director',
                'email' => 'md@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::MANAGING_DIRECTOR,
            ],
            [
                'name' => 'General Manager',
                'email' => 'gm@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::GENERAL_MANAGER,
            ],
            [
                'name' => 'Loan Manager',
                'email' => 'lm@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::LOAN_MANAGER,
            ],
            [
                'name' => 'Loan Officer',
                'email' => 'lo@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::LOAN_OFFICER,
            ],
            [
                'name' => 'Secretary',
                'email' => 'secretary@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::SECRETARY,
            ],
            [
                'name' => 'John Client',
                'email' => 'client@gmail.com',
                'password' => Hash::make('12345678'),
                'role' => UserRole::CLIENT,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }
    }
}
