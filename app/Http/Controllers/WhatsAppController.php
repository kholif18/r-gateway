<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function qr()
    {
        $sessionId = 'user_23';
        $response = Http::get("http://localhost:3000/start", [
            'sessionId' => $sessionId,
        ]);

        if ($response->ok()) {
            $qrBase64 = $response->json()['qr'];
            return view('wa.qr', ['qr' => $qrBase64]);
        }

        return "Gagal ambil QR code.";
    }

    public function send(Request $request)
    {
        $sessionId = 'user_23';

        $response = Http::post("http://localhost:3000/send-message", [
            'sessionId' => $sessionId,
            'phone' => $request->phone,
            'message' => $request->message
        ]);

        return $response->json();
    }
}