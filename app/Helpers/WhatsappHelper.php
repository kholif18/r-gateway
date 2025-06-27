<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappHelper
{
    public static function checkGatewayStatus(string $session): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->get(env('WA_BACKEND_URL') . "/session/status?session={$session}");

            if ($response->ok()) {
                $status = strtolower($response->json('status'));
                return [
                    'connected' => $status === 'connected',
                    'status' => $status,
                    'raw' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Gagal cek status gateway: ' . $e->getMessage());
        }

        return [
            'connected' => false,
            'status' => 'disconnected'
        ];
    }
}
