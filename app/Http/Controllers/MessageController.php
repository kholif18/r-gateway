<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    protected $wa;

    public function __construct()
    {
        $this->wa = new WhatsAppService();
    }

    public function index()
    {
        return view('send-message');
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'number'  => 'required|string',
            'message' => 'required|string',
        ]);

        $session = Auth::user()->username ?? 'user_1';

        try {
            $response = Http::withHeaders([
                'X-API-SECRET' => env('API_SECRET'),
            ])->post(env('WA_BACKEND_URL') . '/session/send', [
                'session' => $session,
                'phone'   => $validated['number'],
                'message' => $validated['message'],
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pesan berhasil dikirim!');
            }

            return back()->with('error', 'Gagal mengirim pesan. Server membalas: ' . $response->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Error saat mengirim pesan: ' . $e->getMessage());
        }
    }
}
