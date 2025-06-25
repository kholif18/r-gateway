<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappLoginController extends Controller
{
    public function index()
    {
        return view('wa-login');
    }

    public function qr()
    {
        try {
            $res = Http::get('http://wa-gateway:3000/session/start', [ 'session' => 'user_27' ]);

            if ($res->ok() && $res->json('qr')) {
                return response()->json([
                    'qr' => $res->json('qr')
                ]);
            }

            return response()->json(['error' => 'QR tidak tersedia'], 400);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal ambil QR: ' . $e->getMessage()], 500);
        }
    }

    public function status()
    {
        try {
            $res = Http::get('http://wa-gateway:3000/session/start', [ 'session' => 'user_27' ]);

            if ($res->ok()) {
                return response()->json($res->json());
            }

            return response()->json(['status' => 'DISCONNECTED'], 400);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal ambil status: ' . $e->getMessage()], 500);
        }
    }

    public function logout()
    {
        $res = Http::delete('http://wa-gateway:3000/session/logout', [
            'session' => 'user_27'
        ]);

        return redirect()->back()->with('success', 'Berhasil logout dari WhatsApp');
    }
}
