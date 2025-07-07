<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
        $session = Auth::user()->username;
        $client  = Auth::user()->name ?? 'Test Gateway';

        $type = $request->input('type');

        $uploadDir = 'temp-uploads';
        try {
            switch ($type) {
                case 'text':
                    $request->validate([
                        'number'  => 'required|string',
                        'message' => 'required|string',
                    ]);
                    $number = WhatsappHelper::normalizePhoneNumber($request->number);
                    $result = $this->wa->sendMessage($session, $number, $request->message);
                    break;

                case 'file-url':
                    $request->validate([
                        'number'    => 'required|string',
                        'file_url'  => 'required|url',
                        'caption'   => 'nullable|string',
                    ]);
                    $number = WhatsappHelper::normalizePhoneNumber($request->number);
                    $result = $this->wa->sendMedia($session, $number, $request->file_url, $request->caption);
                    break;

                case 'file-upload':
                    $request->validate([
                        'number' => 'required|string',
                        'file'   => 'required|file|max:51200', // 50MB
                        'caption' => 'nullable|string',
                    ]);

                    $number = WhatsappHelper::normalizePhoneNumber($request->number);

                    // PASTIKAN folder ada sebelum menyimpan
                    if (!Storage::exists($uploadDir)) {
                        Storage::makeDirectory($uploadDir);
                        Log::info("Folder $uploadDir dibuat.");
                    }

                    // Cek apakah file ada sebelum store
                    if (!$request->hasFile('file')) {
                        throw new \Exception("File tidak ditemukan dalam permintaan.");
                    }

                    $filePath = $request->file('file')->store($uploadDir);
                    $absolutePath = storage_path('app/' . $filePath);

                    if (!file_exists($absolutePath)) {
                        throw new \Exception("File gagal disimpan: $absolutePath");
                    }

                    $result = $this->wa->sendLocalMedia($session, $number, $absolutePath, $request->caption);

                    // Hapus file temp jika sudah ada
                    if (file_exists($absolutePath)) {
                        unlink($absolutePath);
                    } else {
                        Log::warning("File tidak ditemukan saat akan dihapus: $absolutePath");
                    }
                    break;

                case 'group':
                    $request->validate([
                        'group-name' => 'required|string',
                        'message'    => 'required|string',
                    ]);
                    $result = $this->wa->sendGroupMessage($session, $request->input('group-name'), $request->message);
                    break;

                default:
                    return back()->with('error', 'Tipe pengiriman tidak dikenali.');
            }

            if (is_array($result) && isset($result['success'])) {
                $success = $result['success'];
                $body = $result['body'];
            } else {
                $success = $result === true;
                $body = is_string($result) ? $result : json_encode($result);
            }

            // Simpan log jika ada nomor
            MessageLog::create([
                'client_name'  => $client,
                'session_name' => $session,
                'phone'        => $request->input('number') ?? '-',
                'message'      => $request->input('message') ?? $request->input('caption'),
                'status'       => $success ? 'success' : 'failed',
                'response'     => $body,
                'sent_at'      => now(),
            ]);

            return back()->with($success ? 'success' : 'error', $success
                ? 'Pesan berhasil dikirim!'
                : 'Gagal mengirim pesan: ' . $body
            );

        } catch (\Exception $e) {
            Log::error("Gagal mengirim pesan: " . $e->getMessage());

            MessageLog::create([
                'client_name'  => $client,
                'session_name' => $session,
                'phone'        => $request->input('number') ?? '-',
                'message'      => $request->input('message') ?? $request->input('caption'),
                'status'       => 'failed',
                'response'     => $e->getMessage(),
                'sent_at'      => now(),
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function logMessage($client, $session, $target, $message, $success, $response)
    {
        MessageLog::create([
            'client_name'   => $client,
            'session_name'  => $session,
            'phone'         => $target,
            'message'       => $message,
            'status'        => $success ? 'success' : 'failed',
            'response'      => $response,
            'sent_at'       => now(),
        ]);
    }
}
