<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\WhatsAppService;

class WhatsappLoginController extends Controller
{
    protected WhatsAppService $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    protected function getSession(): ?string
    {
        return Auth::check() ? Auth::user()->username : null;
    }

    public function index()
    {
        $session = $this->getSession();
        $status = Cache::get("whatsapp_status_{$session}", 'DISCONNECTED');

        return view('wa-login', compact('status'));
    }

    public function start(Request $request)
    {
        $session = $this->getSession();

        if (!$session) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        // ✅ Cek status session
        $status = $this->whatsapp->checkSessionStatus($session);

        if ($status && in_array(strtolower($status['status']), ['connected', 'qr'])) {
            return response()->json([
                'message' => 'Session already active or waiting for QR scan',
                'status' => $status['status'],
            ], 200);
        }

        // ✅ Mulai sesi baru
        $response = $this->whatsapp->startSession($session);
        return response()->json($response->json(), $response->status());
    }

    public function getQrImage()
    {
        $session = $this->getSession();

        if (!$session) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        $status = $this->whatsapp->checkSessionStatus($session);

        if ($status && strtolower($status['status']) === 'connected') {
            return response()->noContent(); // Sudah login, tidak perlu QR
        }

        $qrResponse = $this->whatsapp->getQrCode($session);

        return $qrResponse && $qrResponse->ok()
            ? response($qrResponse->body(), 200)->header('Content-Type', 'image/png')
            : response()->json(['error' => 'QR tidak tersedia'], 404);
    }

    public function status()
    {
        $session = $this->getSession();

        if (!$session) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        $status = $this->whatsapp->checkSessionStatus($session);
        return response()->json($status ?? ['status' => 'disconnected']);
    }

    public function logout()
    {
        $session = $this->getSession();

        if (!$session) {
            return response()->json(['error' => 'Unauthorized or username missing'], 403);
        }

        $this->whatsapp->logoutSession($session);

        return redirect()->route('whatsapp.login')->with('status', 'Berhasil logout');
    }
}
