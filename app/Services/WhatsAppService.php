<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url');
        $this->apiKey = config('services.whatsapp.key');
    }

    public function startSession(string $session)
    {
        return Http::timeout(30)
            ->withHeaders(['X-API-SECRET' => $this->apiKey])
            ->post("{$this->baseUrl}/session/start", [
                'session' => $session,
            ]);
    }

    public function getSessions()
    {
        try {
            return Http::get("{$this->baseUrl}/session")->json('data') ?? [];
        } catch (\Exception $e) {
            Log::error('Get sessions failed: ' . $e->getMessage());
            return [];
        }
    }

    public function checkSessionStatus(string $session)
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->get("{$this->baseUrl}/session/status?session={$session}");

            return $response->ok() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp session status failed: ' . $e->getMessage());
            return null;
        }
    }

    public function sendMessageToSession(string $session, string $phone, string $message)
    {
        try {
            return Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->post("{$this->baseUrl}/session/send", [
                'session' => $session,
                'phone'   => $phone,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error("Send message failed: " . $e->getMessage());
            return null;
        }
    }

    public function logoutSession(string $session)
    {
        return Http::withHeaders([
            'X-API-SECRET' => $this->apiKey,
        ])->get("{$this->baseUrl}/session/logout?session={$session}");
    }

    public function getQrCode(string $session)
    {
        try {
            return Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->get("{$this->baseUrl}/session/qr.png?session={$session}");
        } catch (\Exception $e) {
            Log::error("QR code fetch failed: " . $e->getMessage());
            return null;
        }
    }

    public function deleteSession(string $session)
    {
        return Http::withHeaders([
            'X-API-SECRET' => $this->apiKey,
        ])->delete("{$this->baseUrl}/session/delete", [
            'session' => $session,
        ]);
    }
}
