<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiClient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use function App\Helpers\Setting\setting; 

class WhatsappApiController extends Controller
{
    protected WhatsAppService $wa;

    public function __construct(WhatsAppService $wa)
    {
        $this->wa = $wa;
    }

    public function send(Request $request)
    {
        // Validasi input awal
        $validator = Validator::make($request->all(), [
            'to'     => 'required|string',
            'msg'    => 'required|string',
            'client' => 'required|string',
            'secret' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $to     = $request->input('to');
        $msg    = $request->input('msg');
        $client = $request->input('client');
        $secret = $request->input('secret');

        // Cek client valid
        $clientData = ApiClient::where('client_name', $client)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$clientData) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        $session = $clientData->session_name;

        if (!$session) {
            return response()->json(['error' => 'Session belum dikonfigurasi untuk client ini'], 400);
        }

        // Konfigurasi timeout & retry
        $timeout       = setting("{$session}_timeout", 30);
        $maxRetry      = setting("{$session}_max-retry", 3);
        $retryInterval = setting("{$session}_retry-interval", 10);

        try {
            $response = $this->wa->sendMessageToSession($session, $to, $msg, [
                'timeout'  => $timeout,
                'retries'  => $maxRetry,
                'interval' => $retryInterval,
            ]);

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $to,
                'message'       => $msg,
                'status'        => $response?->successful() ? 'success' : 'failed',
                'response'      => $response?->body(),
                'sent_at'       => $response?->successful() ? now() : null,
            ]);

            if ($response && $response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error'            => 'Gagal mengirim ke backend',
                'backend_response' => $response?->body(),
            ], $response?->status() ?? 500);
        } catch (\Exception $e) {
            Log::error('WA API Send Error', ['error' => $e->getMessage()]);

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $to,
                'message'       => $msg,
                'status'        => 'error',
                'response'      => $e->getMessage(),
            ]);

            return response()->json([
                'error'     => 'Exception saat mengirim ke backend',
                'exception' => $e->getMessage(),
            ], 500);
        }
    }
}
