<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMemberSignatory extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'category',
        'role',
        'sequence',
        'name',
        'phone',
        'signature_name',
        'signed_at',
        'thumbprint_confirmed',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'date',
            'thumbprint_confirmed' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
