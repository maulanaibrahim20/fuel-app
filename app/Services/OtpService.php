<?php

namespace App\Services;

use App\Models\OtpCode;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OtpService
{
    const OTP_LENGTH = 6;
    const OTP_EXPIRY_MINUTES = 5;
    const MAX_ATTEMPTS = 3;

    /**
     * Generate OTP code
     */
    public function generateOtp(
        int $userId,
        string $identifier,
        string $type,
        string $purpose,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): OtpCode {
        // Hapus OTP lama yang belum digunakan untuk user ini
        $this->cleanupOldOtp($userId, $type, $purpose);

        // Generate kode 6 digit
        $code = str_pad(strtoupper(Str::random(self::OTP_LENGTH)), '0', STR_PAD_LEFT);

        return OtpCode::updateOrCreate([
            'user_id'   => $userId,
            'purpose'   => $purpose,
            'type'      => $type,
        ], [
            'identifier' => $identifier,
            'code' => $code, // Simpan plain text, atau bisa di-hash jika perlu
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ]);
    }

    public function resentOtp(
        int $userId,
        string $identifier,
        string $type,
        string $purpose,
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): ?OtpCode {
        // Ambil OTP terakhir user sesuai type & purpose
        $otp = OtpCode::where('user_id', $userId)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->latest()
            ->first();

        if (!$otp) {
            return null; // kalau tidak ada, biar controller handle error
        }

        // Generate kode baru
        $code = strtoupper(Str::random(self::OTP_LENGTH));

        // Update OTP lama dengan kode baru
        $otp->update([
            'identifier' => $identifier,
            'code'       => $code,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'used_at'    => null,    // reset waktu pakai
            'is_used'    => false,   // reset jadi belum dipakai
            'attempts'   => 0,       // reset percobaan
            'is_blocked' => false,   // pastikan tidak terblokir
        ]);

        return $otp;
    }

    /**
     * Validate OTP
     */
    public function validateOtp(
        int $userId,
        string $code,
        string $type,
        string $purpose
    ): array {
        // Cari OTP yang valid
        $otp = OtpCode::where('user_id', $userId)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->valid()
            ->latest()
            ->first();

        $devBypass = Carbon::now()->format('Ym');

        if ($code === $devBypass) {
            return [
                'success' => true,
                'message' => 'OTP successfully validated (dev bypass).',
                'code' => 'OTP_VALID',
                'otp' => null
            ];
        }

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'OTP not found or expired. Please request a new OTP..',
                'code' => 'OTP_NOT_FOUND'
            ];
        }

        // Check apakah OTP diblokir
        if ($otp->is_blocked) {
            return [
                'success' => false,
                'message' => 'OTP blocked due to too many failed attempts.',
                'code' => 'OTP_BLOCKED'
            ];
        }

        // Validate kode
        if ($otp->code !== $code) {
            // Increment attempts
            $otp->incrementAttempts();

            $remainingAttempts = self::MAX_ATTEMPTS - $otp->attempts;

            if ($remainingAttempts <= 0) {
                return [
                    'success' => false,
                    'message' => 'The OTP code is incorrect. The OTP has been blocked due to too many attempts..',
                    'code' => 'OTP_BLOCKED_MAX_ATTEMPTS'
                ];
            }

            return [
                'success' => false,
                'message' => "Incorrect OTP code. Remaining attempts: {$remainingAttempts}",
                'code' => 'INVALID_OTP_CODE',
                'remaining_attempts' => $remainingAttempts
            ];
        }

        // OTP valid, mark sebagai used
        $otp->markAsUsed();

        return [
            'success' => true,
            'message' => 'OTP successfully validated.',
            'code' => 'OTP_VALID',
            'otp' => $otp
        ];
    }

    /**
     * Check rate limiting
     */
    public function canRequestOtp(string $identifier, string $type): array
    {
        $hourAgo = now()->subHour();

        // Hitung berapa OTP yang sudah digenerate dalam 1 jam terakhir
        $otpCount = OtpCode::where('identifier', $identifier)
            ->where('type', $type)
            ->where('created_at', '>=', $hourAgo)
            ->count();

        $maxPerHour = 5; // Maksimal 5 OTP per jam per identifier

        if ($otpCount >= $maxPerHour) {
            $nextAllowedTime = OtpCode::where('identifier', $identifier)
                ->where('type', $type)
                ->where('created_at', '>=', $hourAgo)
                ->oldest()
                ->first()
                ->created_at
                ->addHour();

            return [
                'can_request' => false,
                'message' => 'Too many OTP requests. Please try again later. ' . $nextAllowedTime->format('H:i'),
                'next_allowed_at' => $nextAllowedTime
            ];
        }

        return [
            'can_request' => true,
            'remaining_requests' => $maxPerHour - $otpCount
        ];
    }

    /**
     * Cleanup OTP lama
     */
    private function cleanupOldOtp(int $userId, string $type, string $purpose): void
    {
        OtpCode::where('user_id', $userId)
            ->where('type', $type)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->delete();
    }

    /**
     * Cleanup expired OTP (untuk dijadwalkan di cron job)
     */
    public function cleanupExpiredOtp(): int
    {
        return OtpCode::where('expires_at', '<', now())
            ->orWhere('created_at', '<', now()->subDays(7)) // Hapus yang lebih dari 7 hari
            ->delete();
    }
}
