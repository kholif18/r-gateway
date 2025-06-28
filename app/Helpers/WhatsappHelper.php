<?php

namespace App\Helpers\WhatsappHelper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappHelper
{
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
                Log::warning("Status check failed with status code {$response->status()}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Gagal cek status gateway: ' . $e->getMessage());
        }

        return [
            'connected' => false,
            'status' => 'disconnected',
        ];
    }
}
