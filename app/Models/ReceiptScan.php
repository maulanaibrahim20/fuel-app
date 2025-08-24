<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptScan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_image_path',
        'processed_image_path',
        'ocr_raw_data',
        'extracted_data',
        'status',
        'scan_type',
        'error_message',
        'confidence_score',
        'is_manually_verified',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'ocr_raw_data' => 'array',
        'extracted_data' => 'array',
        'confidence_score' => 'decimal:2',
        'is_manually_verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    protected $dates = [
        'verified_at',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('scan_type', $type);
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_manually_verified', true);
    }

    public function scopeHighConfidence($query, float $threshold = 75.0)
    {
        return $query->where('confidence_score', '>=', $threshold);
    }

    // Accessors
    public function getFormattedConfidenceScoreAttribute(): string
    {
        return $this->confidence_score ? number_format($this->confidence_score, 1) . '%' : 'N/A';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'processing' => 'badge-info',
            'completed' => 'badge-success',
            'failed' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->scan_type) {
            'fuel_receipt' => 'badge-primary',
            'maintenance_receipt' => 'badge-info',
            'other' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    // Methods
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed'
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function verify(int $verifiedBy): void
    {
        $this->update([
            'is_manually_verified' => true,
            'verified_by' => $verifiedBy,
            'verified_at' => now()
        ]);
    }

    public function getFuelInfo(): ?array
    {
        return $this->extracted_data['fuel_info'] ?? null;
    }

    public function getTransactionInfo(): ?array
    {
        return $this->extracted_data['transaction_info'] ?? null;
    }

    public function getPaymentInfo(): ?array
    {
        return $this->extracted_data['payment_info'] ?? null;
    }

    public function getSpbuInfo(): ?array
    {
        return $this->extracted_data['spbu_info'] ?? null;
    }

    public function isHighConfidence(): bool
    {
        return $this->confidence_score >= 75.0;
    }

    public function hasWarnings(): bool
    {
        return !empty($this->extracted_data['validation']['warnings'] ?? []);
    }

    public function getWarnings(): array
    {
        return $this->extracted_data['validation']['warnings'] ?? [];
    }
}
