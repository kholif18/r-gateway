<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use App\Helpers\WhatsappHelper;

class MessageController extends Controller
{
    protected $wa;

    public function __construct()
    {
        $this->wa = new WhatsAppService();
    }

    public function index()
    {
        $session = Auth::user()->username;
        $statusData = WhatsappHelper::checkGatewayStatus($session);

        return view('send-message', [
            'waStatus' => $statusData['connected'] ? 'Terhubung' : 'Tidak Terhubung',
            'waConnected' => $statusData['connected'],
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'number'  => 'required|string',
            'message' => 'required|string',
        ]);

        $session = Auth::user()->username;
        $client  = Auth::user()->name ?? 'Test Gateway'; // bisa diganti dari config jika perlu
        $number  = WhatsappHelper::normalizePhoneNumber($validated['number']);
        $message = $validated['message'];

        try {
            $response = $this->wa->sendMessageToSession($session, $number, $message);
            $success  = $response && $response->successful();

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $number,
                'message'       => $message,
                'status'        => $success ? 'success' : 'failed',
                'response'      => $response?->body(),
                'sent_at'       => now(),
            ]);

            return back()->with($success ? 'success' : 'error', $success
                ? 'Pesan berhasil dikirim!'
                : 'Gagal mengirim pesan. Server membalas: ' . $response?->body()
            );

        } catch (\Exception $e) {
            Log::error('Send message error: ' . $e->getMessage());

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $number,
                'message'       => $message,
                'status'        => 'failed',
                'response'      => $e->getMessage(),
                'sent_at'       => now(),
            ]);

            return back()->with('error', 'Error saat mengirim pesan: ' . $e->getMessage());
        }
    }
}
