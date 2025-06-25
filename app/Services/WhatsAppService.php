<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $baseUrl;
    protected $apiKey;
    protected $sessionId;

    public function __construct()
    {
        $this->baseUrl = config('services.whatsapp.url');
        $this->apiKey = config('services.whatsapp.key');
        $this->sessionId = config('services.whatsapp.session_id', 'default');
    }

    /**
     * Check WhatsApp connection status
     */
    public function checkStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/session/status/' . $this->sessionId);

            return $response->successful() 
                ? $response->json() 
                : ['error' => 'Failed to check status'];
                
        } catch (\Exception $e) {
            Log::error('WhatsApp status check failed: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $to, string $message, ?string $session = null)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/send', [
                'session' => $session ?? $this->sessionId,
                'to' => $to,
                'content' => $message,
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . '/session/qr/' . $this->sessionId);

            return $response->successful() 
                ? $response->json() 
                : ['error' => 'Failed to get QR code'];
                
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->baseUrl . '/session/logout/' . $this->sessionId);

            return $response->successful();
            
        } catch (\Exception $e) {
            Log::error('WhatsApp logout failed: ' . $e->getMessage());
            return false;
        }
    }
}