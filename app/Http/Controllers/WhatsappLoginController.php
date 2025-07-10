<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $status = WhatsappHelper::checkGatewayStatus($session);

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
