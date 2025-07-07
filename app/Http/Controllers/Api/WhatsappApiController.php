<?php

namespace App\Http\Controllers\Api;

use App\Models\ApiClient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use App\Helpers\WhatsappHelper;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
            'phone'     => 'required|array',
            'phone.*'   => 'required|string',
            'message'   => 'required|string',
            'client'    => 'required|string',
            'secret'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $phones  = $request->input('phone'); // array
        $message = $request->input('message');
        $client  = $request->input('client');
        $secret  = $request->input('secret');

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

        $results = [];

        foreach ($phones as $phoneRaw) {
            $phone = WhatsappHelper::normalizePhoneNumber($phoneRaw);

            try {
                $response = $this->wa->sendMessage($session, $phone, $message, [
                    'timeout'  => $timeout,
                    'retries'  => $maxRetry,
                    'interval' => $retryInterval,
                ]);

                MessageLog::create([
                    'client_name'   => $client,
                    'session_name'  => $session,
                    'phone'         => $phone,
                    'message'       => $message,
                    'status'        => $response['success'] ? 'success' : 'failed',
                    'response'      => $response['body'],
                    'sent_at'       => $response['success'] ? now() : null,
                ]);

                $results[] = [
                    'phone'   => $phone,
                    'status'  => $response['success'] ? 'success' : 'failed',
                    'detail'  => $response['body'],
                ];
            } catch (\Exception $e) {
                Log::error('WA API Send Error', ['error' => $e->getMessage()]);

                MessageLog::create([
                    'client_name'   => $client,
                    'session_name'  => $session,
                    'phone'         => $phone,
                    'message'       => $message,
                    'status'        => 'error',
                    'response'      => $e->getMessage(),
                ]);

                $results[] = [
                    'phone'   => $phone,
                    'status'  => 'error',
                    'detail'  => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'results' => $results,
        ]);
    }

    public function sendMedia(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'    => 'required|string',
            'fileUrl'  => 'required|url',
            'caption'  => 'nullable|string',
            'client'   => 'required|string',
            'secret'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $phone    = WhatsappHelper::normalizePhoneNumber($request->input('phone'));
        $fileUrl  = $request->input('fileUrl');
        $caption  = $request->input('caption');
        $client   = $request->input('client');
        $secret   = $request->input('secret');

        $clientData = ApiClient::where('client_name', $client)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$clientData) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        $session = $clientData->session_name;

        try {
            $success = $this->wa->sendMedia($session, $phone, $fileUrl, $caption);

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => '[MEDIA] ' . $caption,
                'status'        => $success ? 'success' : 'failed',
                'response'      => $success ? 'Media terkirim' : 'Gagal kirim media',
                'sent_at'       => $success ? now() : null,
            ]);

            if ($success) {
                return response()->json(['message' => 'Media berhasil dikirim'], 200);
            } else {
                return response()->json(['error' => 'Gagal mengirim media'], 500);
            }
        } catch (\Exception $e) {
            Log::error("Send media error: " . $e->getMessage());

            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }

    public function sendGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'groupName' => 'required|string',
            'message'   => 'required|string',
            'client'    => 'required|string',
            'secret'    => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $groupName = $request->input('groupName');
        $message   = $request->input('message');
        $client    = $request->input('client');
        $secret    = $request->input('secret');

        $clientData = ApiClient::where('client_name', $client)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$clientData) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        $session = $clientData->session_name;

        try {
            $success = $this->wa->sendGroupMessage($session, $groupName, $message);

            MessageLog::create([
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => '[GROUP] ' . $groupName,
                'message'       => $message,
                'status'        => $success ? 'success' : 'failed',
                'response'      => $success ? 'OK' : 'Failed',
                'sent_at'       => $success ? now() : null,
            ]);

            return $success
                ? response()->json(['message' => 'Pesan berhasil dikirim ke grup'], 200)
                : response()->json(['error' => 'Gagal mengirim pesan ke grup'], 500);
        } catch (\Exception $e) {
            Log::error("Send group message error: " . $e->getMessage());

            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }

}
