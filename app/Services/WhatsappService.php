<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected static function baseUrl(): string
    {
        return config('services.sidobe.base_url') ?? env('SIDOBE_API_BASE_URL');
    }

    protected static function secretKey(): string
    {
        return config('services.sidobe.secret_key') ?? env('SIDOBE_SECRET_KEY');
    }

    protected static function post(string $endpoint, array $data): ?object
    {
        $response = Http::withHeaders([
            'X-Secret-Key' => self::secretKey(),
            'Content-Type' => 'application/json',
        ])->post(self::baseUrl() . $endpoint, $data);

        // Jika error HTTP, balikin object dengan info error
        if (!$response->successful()) {
            return (object)[
                'success' => false,
                'status'  => $response->status(),
                'message' => $response->body(),
            ];
        }

        // Convert JSON ke object (stdClass)
        return json_decode($response->body(), false);
    }

    public static function sendMessage(string $phone, string $message, bool $isAsync = false): ?object
    {
        return self::post('/send-message', [
            'phone' => $phone,
            'message' => $message,
            'is_async' => $isAsync,
        ]);
    }

    public static function sendImage(string $phone, string $imageUrl, ?string $caption = null, bool $isAsync = false): ?object
    {
        $payload = [
            'phone' => $phone,
            'image_url' => $imageUrl,
            'is_async' => $isAsync,
        ];
        if ($caption !== null) {
            $payload['message'] = $caption;
        }
        return self::post('/send-message-image', $payload);
    }

    public static function sendDocument(string $phone, string $docUrl, string $docName, ?string $caption = null, bool $isAsync = false): ?object
    {
        $payload = [
            'phone' => $phone,
            'document_url' => $docUrl,
            'document_name' => $docName,
            'is_async' => $isAsync,
        ];
        if ($caption !== null) {
            $payload['message'] = $caption;
        }
        return self::post('/send-message-doc', $payload);
    }

    public static function checkNumber(string $phone): ?object
    {
        return self::post('/utilities/check-number', [
            'phone' => $phone,
        ]);
    }
}
