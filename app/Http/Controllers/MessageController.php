<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function index()
    {
        return view('send-message');
    }

    public function send(Request $request)
    {
        try {
            $response = Http::post(env('WA_GATEWAY_API') . '/api/send-message', [
                'number' => $request->number,
                'message' => $request->message,
            ]);

            if ($response->successful()) {
                $resBody = $response->json();

                if (isset($resBody['status']) && $resBody['status'] === false) {
                    return back()->with('error', $resBody['message'] ?? 'Gagal mengirim pesan. Periksa koneksi device.');
                }

                return back()->with('status', 'Pesan berhasil dikirim!');
            } else {
                return back()->with('error', 'Gagal mengirim pesan. Server wa-gateway tidak merespons.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
