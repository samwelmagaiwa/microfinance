<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;
use App\Enums\UserRole;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role->value, $roles);
        }
        return $this->role->value === $roles;
    }

    public function isManagingDirector(): bool { return $this->role === UserRole::MANAGING_DIRECTOR; }
    public function isGeneralManager(): bool { return $this->role === UserRole::GENERAL_MANAGER; }
    public function isLoanManager(): bool { return $this->role === UserRole::LOAN_MANAGER; }
    public function isLoanOfficer(): bool { return $this->role === UserRole::LOAN_OFFICER; }
    public function isSecretary(): bool { return $this->role === UserRole::SECRETARY; }
    public function isClient(): bool { return $this->role === UserRole::CLIENT; }
}
