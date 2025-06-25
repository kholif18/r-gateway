<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WhatsappController extends Controller
{
    protected $backendUrl;

    public function __construct()
    {
        $this->backendUrl = env('WA_BACKEND_URL', 'http://wa-backend:3000');
    }

    protected function getSessionName()
    {
        return 'user_' . Auth::id();
    }

    public function startSession()
    {
        $session = $this->getSessionName();

        // Mulai session
        $res = Http::get("{$this->backendUrl}/session/start", [
            'session' => $session
        ]);

        if ($res->failed()) {
            return response()->json(['error' => $res->json('message') ?? 'Gagal membuat sesi'], 400);
        }

        return response()->json(['message' => 'Session dimulai']);
    }

    public function getQr()
    {
        $session = $this->getSessionName();
        $res = Http::get("{$this->backendUrl}/session/qr", [
            'session' => $session
        ]);

        if ($res->ok() && $res->json('qr')) {
            return response()->json(['qr' => $res->json('qr')]);
        }

        return response()->json(['error' => 'QR tidak tersedia'], 400);
    }

    public function status()
    {
        $session = $this->getSessionName();
        $status = Cache::get("whatsapp_status_{$session}", 'DISCONNECTED');

        return response()->json(['status' => $status]);
    }

    public function logout()
    {
        $session = $this->getSessionName();
        $res = Http::get("{$this->backendUrl}/session/logout", [
            'session' => $session
        ]);

        return redirect()->back()->with('success', 'Logout berhasil');
    }
}
