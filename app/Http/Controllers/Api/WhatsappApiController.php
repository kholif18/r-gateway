<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Models\ApiClient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class WhatsappApiController extends Controller
{
    protected $backendUrl;
    protected $apiSecret;

    public function __construct()
    {
        $this->backendUrl = env('WA_BACKEND_URL');
        $this->apiSecret = env('API_SECRET');
    }

    public function send(Request $request)
    {
        $to = $request->input('to');
        $msg = $request->input('msg');
        $secret = $request->input('secret');
        $clientName = $request->input('client');

        // Cari client di DB
        $client = ApiClient::where('client_name', $clientName)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        if (!$to || !$msg) {
            return response()->json(['error' => 'to dan msg wajib diisi'], 400);
        }

        // Ambil session dari DB
        $session = $client->session_name;

        if (!$session) {
            return response()->json(['error' => 'Session belum dikonfigurasi untuk client ini'], 400);
        }

        try {
            $timeout = setting("{$session}_timeout", 30);
            $maxRetry = setting("{$session}_max-retry", 3);
            $retryInterval = setting("{$session}_retry-interval", 10);

            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])
            ->timeout((int) $timeout)
            ->retry((int) $maxRetry, (int) $retryInterval * 1000) // dalam milidetik
            ->post(env('WA_BACKEND_URL') . '/session/send', [
                'session' => $session,
                'phone'   => $to,
                'message' => $msg,
            ]);

            MessageLog::create([
                'client_name'   => $clientName,
                'session_name'  => $session,
                'phone'         => $to,
                'message'       => $msg,
                'status'        => $response->successful() ? 'success' : 'failed',
                'response'      => $response->body(),
                'sent_at'       => $response->successful() ? now() : null,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Gagal mengirim ke backend',
                'backend_response' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            // Log jika error koneksi/timeout
            MessageLog::create([
                'client_name'   => $clientName,
                'session_name'  => $session,
                'phone'         => $to,
                'message'       => $msg,
                'status'        => 'error',
                'response'      => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Exception saat mengirim ke backend',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}