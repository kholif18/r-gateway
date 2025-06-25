<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
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
        $request->validate([
            'number' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $response = $this->wa->sendMessage($request->number, $request->message);

            if (isset($response['status']) && $response['status'] === true) {
                return back()->with('success', 'Pesan berhasil dikirim');
            }

            return back()->with('error', $response['error'] ?? 'Gagal mengirim pesan');
        } catch (\Exception $e) {
            Log::error('Send message error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengirim pesan');
        }
    }
}
