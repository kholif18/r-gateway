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
        $validator = Validator::make($request->all(), [
            'phone'   => 'required|string',
            'message' => 'required|string',
            'client'  => 'required|string',
            'secret'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $phoneRaw = $request->input('phone');
        $message  = $request->input('message');
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

        if (!$session) {
            return response()->json(['error' => 'Session belum dikonfigurasi untuk client ini'], 400);
        }

        // Konfigurasi timeout & retry
        $timeout       = setting("{$session}_timeout", 30);
        $maxRetry      = setting("{$session}_max-retry", 3);
        $retryInterval = setting("{$session}_retry-interval", 10);

        $phone = WhatsappHelper::normalizePhoneNumber($phoneRaw);

        try {
            $response = $this->wa->sendMessage($session, $phone, $message, [
                'timeout'  => $timeout,
                'retries'  => $maxRetry,
                'interval' => $retryInterval,
            ]);

            MessageLog::create([
                'user_id'       => $clientData->user_id,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => $message,
                'status'        => $response['success'] ? 'success' : 'failed',
                'response'      => $response['body'],
                'sent_at'       => $response['success'] ? now() : null,
            ]);

            return response()->json([
                'phone'  => $phone,
                'status' => $response['success'] ? 'success' : 'failed',
                'detail' => $response['body'],
            ]);
        } catch (\Exception $e) {
            Log::error('WA API Send Error', ['error' => $e->getMessage()]);

            MessageLog::create([
                'user_id'       => $clientData->user_id,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => $message,
                'status'        => 'error',
                'response'      => $e->getMessage(),
            ]);

            return response()->json([
                'phone'  => $phone,
                'status' => 'error',
                'detail' => $e->getMessage(),
            ]);
        }
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
                'user_id'       => $clientData->user_id,
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

            MessageLog::create([
                'user_id'       => $clientData->user_id,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => '[MEDIA] ' . $caption,
                'status'        => 'failed',
                'response'      => $e->getMessage(),
                'sent_at'       => now(),
            ]);

            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }

    public function sendMediaUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone'   => 'required|string',
            'file'    => 'required|file|max:51200', // 50MB
            'caption' => 'nullable|string',
            'client'  => 'required|string',
            'secret'  => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $phone   = WhatsappHelper::normalizePhoneNumber($request->input('phone'));
        $caption = $request->input('caption');
        $client  = $request->input('client');
        $secret  = $request->input('secret');

        $clientData = ApiClient::where('client_name', $client)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$clientData) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        $session = $clientData->session_name;

        try {
            // Simpan file temporer
            $originalName = $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->store('temp-uploads', 'public');
            $absolutePath = storage_path('app/public/' . $filePath);

            if (!file_exists($absolutePath)) {
                throw new \Exception("File gagal disimpan: $absolutePath");
            }

            $result = $this->wa->sendLocalMedia($session, $phone, $absolutePath, $caption, $originalName);

            // Simpan log pengiriman
            MessageLog::create([
                'user_id'       => $clientData->user_id,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => '[FILE] ' . ($caption ?? $originalName),
                'status'        => $result['success'] ? 'success' : 'failed',
                'response'      => $result['body'],
                'sent_at'       => $result['success'] ? now() : null,
            ]);

            // Hapus file setelah dikirim
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }

            return $result['success']
                ? response()->json(['message' => 'File berhasil dikirim'])
                : response()->json(['error' => $result['body']], $result['status'] ?? 500);

        } catch (\Exception $e) {
            Log::error("Send media upload error: " . $e->getMessage());

            MessageLog::create([
                'user_id'       => $clientData->user_id,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => $phone,
                'message'       => '[FILE] ' . ($caption ?? $originalName),
                'status'        => 'failed',
                'response'      => $e->getMessage(),
                'sent_at'       => now(),
            ]);

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
                'user_id'       => $clientData->user_id,
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

            MessageLog::create([
                'user_id'       => $clientData->user_id ?? null,
                'client_name'   => $client,
                'session_name'  => $session,
                'phone'         => '[GROUP] ' . $groupName,
                'message'       => $message,
                'status'        => 'failed',
                'response'      => $e->getMessage(),
                'sent_at'       => now(),
            ]);

            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }

    public function sendBulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phones'   => 'required|array',
            'phones.*' => 'required|string',
            'message'  => 'required|string',
            'client'   => 'required|string',
            'secret'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $phones  = $request->input('phones');
        $message = $request->input('message');
        $client  = $request->input('client');
        $secret  = $request->input('secret');

        $clientData = ApiClient::where('client_name', $client)
            ->where('api_token', $secret)
            ->where('is_active', true)
            ->first();

        if (!$clientData) {
            return response()->json(['error' => 'Client tidak valid atau token salah'], 403);
        }

        $session = $clientData->session_name;

        // Normalisasi, filter dan unikkan nomor
        $normalizedPhones = collect($phones)
            ->map(fn($p) => WhatsappHelper::normalizePhoneNumber(trim($p)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($normalizedPhones)) {
            return response()->json(['error' => 'Tidak ada nomor valid ditemukan.'], 422);
        }

        try {
            $result = $this->wa->sendBulkMessage($session, $normalizedPhones, $message);

            $body = is_array($result['body'])
                ? json_encode($result['body'], JSON_UNESCAPED_UNICODE)
                : (is_string($result['body']) ? $result['body'] : json_encode((array) $result['body']));

            $success = $result['success'] ?? false;

            // Simpan log satu kali, phone digabung
            MessageLog::create([
                'user_id'      => $clientData->user_id ?? null,
                'client_name'  => $client,
                'session_name' => $session,
                'phone'        => implode(', ', $normalizedPhones), // Gabung semua nomor
                'message'      => $message,
                'status'       => $success ? 'success' : 'failed',
                'response'     => $body,
                'sent_at'      => $success ? now() : null,
            ]);

            return response()->json([
                'status' => $success ? 'success' : 'failed',
                'detail' => $body,
            ], $success ? 200 : 500);

        } catch (\Exception $e) {
            // Error saat kirim bulk
            MessageLog::create([
                'user_id'      => $clientData->user_id ?? null,
                'client_name'  => $client,
                'session_name' => $session,
                'phone'        => implode(', ', $normalizedPhones),
                'message'      => $message,
                'status'       => 'error',
                'response'     => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }


}
