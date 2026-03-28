<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrower_id',
        'document_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_required',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }
}
