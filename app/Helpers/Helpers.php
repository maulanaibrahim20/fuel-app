<?php

namespace App\Helpers;

class Helpers
{
    /**
     * Mask email khusus (agar @ tetap jelas)
     */
    public static function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return self::maskMiddle($email);
        }

        [$name, $domain] = explode('@', $email);
        $maskedName = self::maskMiddle($name, 2, 1);

        return $maskedName . '@' . $domain;
    }
    /**
     * Mask middle part of a string
     *
     * @param string $value
     * @param int $visibleStart Berapa banyak huruf/angka yang ditampilkan di awal
     * @param int $visibleEnd Berapa banyak huruf/angka yang ditampilkan di akhir
     * @return string
     */
    public static function maskMiddle(string $value, int $visibleStart = 2, int $visibleEnd = 2): string
    {
        $len = strlen($value);

        if ($len <= ($visibleStart + $visibleEnd)) {
            // kalau string terlalu pendek, jangan mask
            return $value;
        }

        $start = substr($value, 0, $visibleStart);
        $end = substr($value, -$visibleEnd);
        $masked = str_repeat('*', $len - ($visibleStart + $visibleEnd));

        return $start . $masked . $end;
    }
    /**
     * Format nomor telepon ke format internasional (+62).
     *
     * @param string|null $phone
     * @return string|null
     */
    public static function formatPhoneInternational(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Hilangkan semua karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Sudah format 62...
        if (strpos($phone, '62') === 0) {
            return '+' . $phone;
        }

        // Format 08...
        if (strpos($phone, '0') === 0) {
            return '+62' . substr($phone, 1);
        }

        // Jika tidak ada + tambahkan
        if (strpos($phone, '+') !== 0) {
            return '+' . $phone;
        }

        return $phone;
    }

    /**
     * Format nomor telepon ke lokal (08...).
     *
     * @param string|null $phone
     * @return string|null
     */
    public static function formatPhoneLocal(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Hilangkan karakter non-digit
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Sudah format 08...
        if (strpos($phone, '0') === 0) {
            return $phone;
        }

        // Format 62...
        if (strpos($phone, '62') === 0) {
            return '0' . substr($phone, 2);
        }

        // Format +62...
        if (strpos($phone, '+62') === 0) {
            return '0' . substr($phone, 3);
        }

        return $phone;
    }

    /**
     * Format angka ke Rupiah.
     *
     * @param int|float $amount
     * @param bool $withPrefix
     * @return string
     */
    public static function formatRupiah($amount, bool $withPrefix = true): string
    {
        $formatted = number_format($amount, 0, ',', '.');
        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }

    /**
     * Generate kode unik.
     *
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public static function generateUniqueCode(string $prefix = '', int $length = 8): string
    {
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, $length));
        return $prefix . $random;
    }
}
