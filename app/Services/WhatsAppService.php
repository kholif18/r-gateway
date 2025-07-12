<?php

namespace App\Services;

use RuntimeException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected ?string $baseUrl;
    protected ?string $apiKey;
    protected ?string $socketUrl;

    public function __construct()
    {
        $this->baseUrl   = config('services.whatsapp.url');         // dari WA_BACKEND_URL
        $this->apiKey    = config('services.whatsapp.key');         // dari API_SECRET
        $this->socketUrl = config('services.whatsapp.socket_url');  // dari WHATSAPP_SOCKET_URL

        if (!$this->baseUrl) {
            throw new RuntimeException("WA_BACKEND_URL belum disetel di file .env");
        }

        if (!$this->apiKey) {
            throw new RuntimeException("API_SECRET belum disetel di file .env");
        }

        if (!$this->socketUrl) {
            throw new RuntimeException("WHATSAPP_SOCKET_URL belum disetel di file .env");
        }
    }

    public function startSession(string $session)
    {
        return Http::timeout(30)
            ->withHeaders(['X-API-SECRET' => $this->apiKey])
            ->post("{$this->baseUrl}/session/start", [
                'session' => $session,
        ]);
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

    public function sendMessage(string $session, string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->post("{$this->baseUrl}/session/send", [
                'session' => $session,
                'phone' => $phone,
                'message' => $message,
            ]);

            return [
                'success' => $response->successful(),
                'body' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("Failed to send message: " . $e->getMessage());
            return [
                'success' => false,
                'body' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function sendMedia(string $session, string $phone, string $fileUrl, ?string $caption = null): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->post("{$this->baseUrl}/session/send-media", [
                'session' => $session,
                'phone'   => $phone,
                'fileUrl' => $fileUrl,
                'caption' => $caption,
            ]);

            Log::debug('Send media request received', [
                'session' => $session,
                'phone' => $phone,
                'fileUrl' => $fileUrl,
                'caption' => $caption
            ]);

            if (!$response->successful()) {
                Log::error("HTTP request gagal saat kirim media. Status: {$response->status()} | Body: " . $response->body());
                return [
                    'success' => false,
                    'body' => $response->body(),
                    'status' => $response->status(),
                ];
            }

            $body = $response->json();

            if (isset($body['error'])) {
                Log::warning("Gagal kirim media: {$body['error']}");
                return [
                    'success' => false,
                    'body' => $body['error'],
                    'status' => $response->status(),
                ];
            }

            Log::info("Media berhasil dikirim");
            return [
                'success' => $response->successful(),
                'body' => $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("Exception saat mengirim media: " . $e->getMessage());
            return [
                'success' => false,
                'body' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function sendLocalMedia(string $session, string $phone, string $filePath, ?string $caption = null, ?string $originalName = null): array
    {
        try {
            if (!file_exists($filePath)) {
                Log::error("File tidak ditemukan: $filePath");
                return [
                    'success' => false,
                    'body' => 'File tidak ditemukan',
                    'status' => 404,
                ];
            }

            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->attach(
                'file',           // Nama field file di backend
                file_get_contents($filePath),
                $originalName ?? basename($filePath)
            )->post("{$this->baseUrl}/session/send-media-upload", [
                'session' => $session,
                'phone'   => $phone,
                'caption' => $caption,
            ]);

            Log::debug('Send local media request', [
                'session' => $session,
                'phone'   => $phone,
                'file'    => $filePath,
                'caption' => $caption,
                'originalName' => $originalName,
            ]);

            if (!$response->successful()) {
                Log::error("HTTP request gagal saat kirim media lokal. Status: {$response->status()} | Body: " . $response->body());
                return [
                    'success' => false,
                    'body' => $response->body(),
                    'status' => $response->status(),
                ];
            }

            $body = $response->json();
            if (isset($body['error'])) {
                Log::warning("Gagal kirim media lokal: {$body['error']}");
                return [
                    'success' => false,
                    'body' => $body['error'],
                    'status' => $response->status(),
                ];
            }

            Log::info("Media lokal berhasil dikirim");
            return [
                'success' => true,
                'body'    => $response->body(),
                'status'  => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error("Exception saat mengirim media lokal: " . $e->getMessage());
            return [
                'success' => false,
                'body' => $e->getMessage(),
                'status' => 500,
            ];
        }
    }

    public function sendGroupMessage(string $session, string $groupName, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->post("{$this->baseUrl}/session/send-group", [
                'session'   => $session,
                'groupName' => $groupName,
                'message'   => $message,
            ]);

            return [
                'success' => $response->successful(),
                'body'    => $response->body(),
                'status'  => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error("Failed to send group message: " . $e->getMessage());

            return [
                'success' => false,
                'body'    => $e->getMessage(),
                'status'  => 500,
            ];
        }
    }

    public function sendBulkMessage(string $session, array $phones, string $message): array
    {
        try {
            // Susun ulang array agar sesuai dengan backend
            $messages = array_map(function ($phone) use ($message) {
                return [
                    'phone'   => $phone,
                    'message' => $message,
                ];
            }, $phones);

            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->post("{$this->baseUrl}/session/send-bulk", [
                'session'  => $session,
                'messages' => $messages,
            ]);

            if (!$response->successful()) {
                Log::error("Gagal kirim bulk message. Status: {$response->status()} | Body: " . $response->body());
                return [
                    'success' => false,
                    'body'    => $response->body(),
                    'status'  => $response->status(),
                ];
            }

            return [
                'success' => true,
                'body'    => $response->json(),
                'status'  => $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error("Exception kirim bulk message: " . $e->getMessage());
            return [
                'success' => false,
                'body'    => $e->getMessage(),
                'status'  => 500,
            ];
        }
    }


    public function logoutSession(string $session)
    {
        return Http::withHeaders([
            'X-API-SECRET' => $this->apiKey,
        ])->get("{$this->baseUrl}/session/logout?session={$session}");
    }

    public function getSessions(): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => $this->apiKey,
            ])->get("{$this->baseUrl}/session/sessions");

            return $response->ok() ? $response->json() : [];
        } catch (\Exception $e) {
            Log::error("Get sessions failed: " . $e->getMessage());
            return [];
        }
    }
}
