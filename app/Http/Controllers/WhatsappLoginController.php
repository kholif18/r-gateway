<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsappLoginController extends Controller
{
    protected $backendUrl;
    protected $apiSecret;

    public function __construct()
    {
        $this->backendUrl = env('WA_BACKEND_URL');
        $this->apiSecret = env('API_SECRET');
    }

    protected function getSessionName()
    {
        return 'user_' . Auth::id();
    }

    public function index()
    {
        $session = Auth::user()->username ?? null;
        $status = Cache::get("whatsapp_status_{$session}", 'DISCONNECTED');

        return view('wa-login', [
            'status' => $status,
        ]);
    }

    public function start(Request $request)
    {
        if (!Auth::check() || !Auth::user()->username) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        $session = Auth::user()->username;

        // ✅ 1. Cek status terlebih dahulu
        $statusResponse = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->get("{$this->backendUrl}/session/status?session={$session}");

        if ($statusResponse->ok()) {
            $status = strtolower($statusResponse->json('status'));

            if ($status === 'connected' || $status === 'qr') {
                return response()->json([
                    'message' => 'Session already active or pending QR',
                    'status' => $status,
                ], 200);
            }
        }

        // ✅ 2. Jika belum aktif, baru start
        $response = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->post("{$this->backendUrl}/session/start", [
            'session' => $session
        ]);

        return response()->json($response->json(), $response->status());
    }

    public function getQrImage()
    {
        if (!Auth::check() || !Auth::user()->username) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        $session = Auth::user()->username;

        // ✅ Cek status dulu
        $statusResponse = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->get("{$this->backendUrl}/session/status?session={$session}");

        if ($statusResponse->ok()) {
            $status = strtolower($statusResponse->json('status'));
            if ($status === 'connected') {
                return response()->noContent(); // 204
            }
        }
        
        $response = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->get("{$this->backendUrl}/session/qr.png?session={$session}");

        if ($response->ok()) {
            return response($response->body(), 200)->header('Content-Type', 'image/png');
        }

        return response()->json(['error' => 'QR tidak tersedia'], 404);
    }

    public function status()
    {
        if (!Auth::check() || !Auth::user()->username) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }
        $session = Auth::user()->username;


        $response = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->get("{$this->backendUrl}/session/status?session={$session}");

        return response()->json($response->json(), $response->status());
    }

    public function logout()
    {
        if (!Auth::check() || !Auth::user()->username) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }
        $session = Auth::user()->username;


        $response = Http::withHeaders([
            'X-API-SECRET' => $this->apiSecret,
        ])->get("{$this->backendUrl}/session/logout?session={$session}");

        return redirect()->route('whatsapp.login')->with('status', 'Berhasil logout');
    }
}
