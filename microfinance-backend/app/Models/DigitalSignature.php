<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DigitalSignature extends Model
{
    protected $fillable = [
        'signable_type',
        'signable_id',
        'user_id',
        'signature_id',
        'signature_data',
        'hash',
        'document_hash',
        'encryption_key',
        'signed_by_name',
        'signed_by_role',
        'signed_at',
        'status',
        'rejection_reason',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function signable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateSignatureId(): string
    {
        return 'SIG-' . strtoupper(bin2hex(random_bytes(8))) . '-' . date('Ymd');
    }

    public static function generateDocumentHash(string $data): string
    {
        return hash('sha256', $data);
    }

    public static function encryptSignature(string $data, string $key): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decryptSignature(string $encryptedData, string $key): string
    {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, 0, $iv);
    }

    public function isValid(): bool
    {
        return $this->status === 'approved' && !empty($this->document_hash);
    }

    public function verifyIntegrity(string $currentData): bool
    {
        return $this->document_hash === self::generateDocumentHash($currentData);
    }
}
