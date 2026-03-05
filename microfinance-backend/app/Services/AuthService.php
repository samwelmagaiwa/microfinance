<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = Auth::user();
        
        // Define base abilities based on role
        $abilities = $this->getRoleAbilities($user->role->value);
        
        $token = $user->createToken('auth_token', $abilities)->plainTextToken;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->value,
            ],
            'token' => $token,
            'permissions' => $abilities
        ];
    }

    protected function getRoleAbilities(string $role): array
    {
        return match ($role) {
            'admin', 'managing_director', 'general_manager' => ['*'], // Full access
            'loan_manager' => ['loans:view', 'loans:approve', 'borrowers:view', 'payments:view'],
            'loan_officer' => ['loans:create', 'loans:view', 'borrowers:create', 'borrowers:view', 'payments:create'],
            'secretary' => ['borrowers:view', 'loans:view', 'payments:view', 'payments:create'],
            'client' => ['profile:view', 'my-loans:view', 'my-payments:view'],
            default => []
        };
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::user()->currentAccessToken()->delete();
        }
    }
}
