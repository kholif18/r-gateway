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

    public function startSession(string $sessionName)
    {
        return Http::timeout(30)
        ->withHeaders(['apikey' => $this->apiKey])
        ->post($this->baseUrl . '/session/create', [
            'session' => $sessionName,
            'multiDevice' => true,
            'browserName' => 'Laravel WA Gateway', // Nama custom
            'browserVersion' => '1.0' // Versi browser
        ]);
    }

    public function getSessions()
    {
        try {
            $res = Http::get($this->baseUrl . '/session');
            return $res->json('data') ?? [];
        } catch (\Exception $e) {
            Log::error('Get sessions failed: ' . $e->getMessage());
            return [];
        }
    }
    /**
     * Check WhatsApp connection status
     */
    public function checkStatus()
    {
        try {
            $response = Http::get($this->baseUrl . '/status');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('WhatsApp status check failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $to, string $message)
    {
        try {
            $response = Http::post($this->baseUrl . '/send-message', [
                'number' => $to,
                'message' => $message,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('WhatsApp message sending failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get QR code for authentication
     */
    public function getQrCode()
    {
        try {
            $response = Http::get($this->baseUrl . '/qr');
            return $response->json();
        } catch (\Exception $e) {
            Log::error('WhatsApp QR code fetch failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Logout from session
     */
    public function logout()
    {
        try {
            $response = Http::get($this->baseUrl . '/logout');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('WhatsApp logout failed: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteSession(string $sessionName)
    {
        return Http::withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json'
        ])
        ->delete($this->baseUrl . '/session/delete', [
            'session' => $sessionName
        ]);
    }

}