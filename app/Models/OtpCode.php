<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'identifier',
        'type',
        'user_id',
        'code',
        'purpose',
        'expires_at',
        'is_used',
        'used_at',
        'ip_address',
        'user_agent',
        'attempts',
        'is_blocked'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
        'is_used' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk OTP yang valid
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->where('is_used', false)
            ->where('is_blocked', false);
    }

    // Check apakah OTP sudah expired
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    // Check apakah OTP masih valid
    public function isValid(): bool
    {
        return !$this->is_used &&
            !$this->is_blocked &&
            !$this->isExpired();
    }

    // Mark OTP sebagai used
    public function markAsUsed(): void
    {
        $this->update([
            'is_used' => true,
            'used_at' => now()
        ]);
    }

    // Increment attempts
    public function incrementAttempts(): void
    {
        $this->increment('attempts');

        // Block jika attempts sudah terlalu banyak (misal 3 kali)
        if ($this->attempts >= 3) {
            $this->update(['is_blocked' => true]);
        }
    }
}
