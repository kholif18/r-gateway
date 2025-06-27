<?php

namespace App\Http\Controllers\Api;

use App\Models\Setting;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
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

        $response = Http::withHeaders([
            'X-API-SECRET' => env('API_SECRET'),
        ])->post(env('WA_BACKEND_URL') . '/session/send', [
                'session' => $session,
                'phone'   => $to,
                'message' => $msg,
            ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Gagal mengirim ke backend',
            'backend_response' => $response->body()
        ], $response->status());
    }
}
