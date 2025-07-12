<?php

namespace App\Helpers;

use App\Models\Setting;
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
        $backendUrl = config('services.whatsapp.url');

        if (!$backendUrl) {
            Log::warning('WA_BACKEND_URL tidak disetel di .env');
            return [
                'connected' => false,
                'status' => 'unconfigured',
            ];
        }

        try {
            $url = "{$backendUrl}/session/status?session={$session}";
            $response = Http::withHeaders([
                'X-API-SECRET' => config('services.whatsapp.key'),
            ])->timeout(5)->get($url); // ⏱️ Tambahkan timeout

            if ($response->ok()) {
                $json = $response->json();
                $status = strtolower($json['status'] ?? 'unknown');

                Log::info("Status session {$session}: {$status}");

                return [
                    'connected' => $status === 'connected',
                    'status' => $status,
                    'raw' => $json,
                ];
            } else {
                Log::warning("Status check failed ({$response->status()}): " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Gagal cek status gateway ({$session}): " . $e->getMessage());
        }

        return [
            'connected' => false,
            'status' => 'disconnected',
        ];
    }


    /**
     * Normalisasi nomor telepon menjadi format internasional (tanpa +)
     */
    public static function normalizePhoneNumber(string $number, ?string $countryCode = null): string
    {
        $countryCode = $countryCode ?? Setting::get('country_code', config('app.country_code', '62'));

        $number = preg_replace('/[^0-9]/', '', $number);

        if (Str::startsWith($number, '0')) {
            return $countryCode . substr($number, 1);
        }

        return $number;
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
    public static function formatPhoneDisplay(string $number, string $countryCode = null): string
    {
        $countryCode = $countryCode ?? Setting::get('country_code', config('app.country_code', '62'));

        $clean = self::normalizePhoneNumber($number, $countryCode);

        if (Str::startsWith($clean, $countryCode)) {
            return '0' . substr($clean, strlen($countryCode));
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
