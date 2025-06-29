<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsappHelper
{
    /**
     * Cek status session WA di backend
     */
    public static function checkGatewayStatus(string $session): array
    {
        try {
            $url = env('WA_BACKEND_URL') . "/session/status?session={$session}";
            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->get($url);

            if ($response->ok()) {
                $status = strtolower($response->json('status', 'unknown'));

                return [
                    'connected' => $status === 'connected',
                    'status' => $status,
                    'raw' => $response->json(),
                ];
            } else {
                Log::warning("Status check failed with code {$response->status()}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gagal cek status gateway: ' . $e->getMessage());
        }

        return [
            'connected' => false,
            'status' => 'disconnected',
        ];
    }

    /**
     * Normalisasi nomor telepon menjadi format internasional (tanpa +)
     */
    public static function normalizePhoneNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (Str::startsWith($number, '0')) {
            return '62' . substr($number, 1);
        }

        return ltrim($number, '+');
    }

    /**
     * Validasi format nomor telepon/WA internasional
     * Contoh valid: +6281234567890, 081234567890
     */
    public static function isValidPhoneNumber(string $number): bool
    {
        return preg_match('/^(\+62|62|08)[0-9]{9,15}$/', $number);
    }

    /**
     * Format nomor untuk ditampilkan (contoh: 0812-3456-7890)
     */
    public static function formatPhoneDisplay(string $number): string
    {
        $clean = self::normalizePhoneNumber($number);

        if (Str::startsWith($clean, '62')) {
            return '0' . substr($clean, 2);
        }

        return $clean;
    }

    /**
     * Deteksi operator/provider dari prefix nomor (optional)
     */
    public static function detectProvider(string $number): string
    {
        $normalized = self::normalizePhoneNumber($number);

        $prefix = substr($normalized, 2, 4); // setelah 62
        $mapping = [
            '811' => 'Telkomsel',
            '812' => 'Telkomsel',
            '813' => 'Telkomsel',
            '821' => 'Telkomsel',
            '822' => 'Telkomsel',
            '823' => 'Telkomsel',
            '852' => 'Telkomsel',
            '853' => 'Telkomsel',
            '857' => 'Indosat',
            '858' => 'Indosat',
            '859' => 'Indosat',
            '878' => 'XL',
            '877' => 'XL',
            '876' => 'XL',
            '895' => 'Tri',
            '896' => 'Tri',
            '897' => 'Tri',
            '898' => 'Tri',
            '899' => 'Tri',
        ];

        return $mapping[$prefix] ?? 'Unknown';
    }
}
