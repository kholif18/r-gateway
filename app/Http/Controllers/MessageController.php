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

        $number = '-';

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
                    $caption = $request->caption ?? '[Media without caption]';
                    
                    $result = $this->wa->sendMedia($session, $number, $request->file_url, $request->caption);
                    // Siapkan untuk log
                    $request->merge([
                        'caption' => $caption,
                    ]);
                    break;

                case 'file-upload':
                    $request->validate([
                        'number'  => 'required|string',
                        'file'    => 'required|file|max:51200', // 50MB
                        'caption' => 'nullable|string',
                    ]);

                    $number = WhatsappHelper::normalizePhoneNumber($request->number);
                    $caption = $request->caption ?? '[Media without caption]';

                    // Buat folder jika belum ada
                    if (!Storage::disk('public')->exists('temp-uploads')) {
                        Storage::disk('public')->makeDirectory('temp-uploads');
                    }

                    // Simpan file ke disk public/temp-uploads
                    $path = $request->file('file')->store('temp-uploads', 'public');

                    // Ambil path absolut
                    $absolutePath = storage_path('app/public/' . $path);

                    // Cek apakah file benar-benar ada
                    if (!file_exists($absolutePath)) {
                        throw new \Exception("File gagal disimpan: $absolutePath");
                    }

                    // Kirim media lokal
                    $originalName = $request->file('file')->getClientOriginalName();
                    $result = $this->wa->sendLocalMedia($session, $number, $absolutePath, $request->caption, $originalName);

                    $request->merge([
                        'caption' => $caption,
                    ]);
                    // Hapus file setelah terkirim
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

                    $groupName = $request->input('group-name');
                    $number = '[GROUP] ' . $groupName; // ini untuk pencatatan log
                    $result = $this->wa->sendGroupMessage($session, $groupName, $request->message);

                    $request->merge([
                        'number' => $number, // agar bisa digunakan di log
                    ]);

                    break;

                case 'bulk':
                    $request->validate([
                        'phones'  => 'required|string', // input dari <textarea>
                        'message' => 'required|string',
                    ]);

                    // 1. Parsing dan normalisasi nomor
                    $rawPhones = preg_split('/[\s,]+/', $request->input('phones'), -1, PREG_SPLIT_NO_EMPTY);

                    $phones = collect($rawPhones)
                        ->map(fn($p) => WhatsappHelper::normalizePhoneNumber(trim($p)))
                        ->filter() // buang yang kosong/null
                        ->unique() // hilangkan duplikat
                        ->values()
                        ->all();

                    if (empty($phones)) {
                        return back()->with('error', 'Tidak ada nomor valid ditemukan.');
                    }

                    // 2. Kirim pesan
                    $result = $this->wa->sendBulkMessage($session, $phones, $request->message);

                    $request->merge([
                        'number' => implode(', ', $phones)
                    ]);

                    $body    = is_array($result['body']) 
                        ? json_encode($result['body'], JSON_UNESCAPED_UNICODE)
                        : (is_string($result['body']) ? $result['body'] : json_encode((array) $result['body']));

                    $success = $result['success'];

                    break;

                default:
                    return back()->with('error', 'Tipe pengiriman tidak dikenali.');
            }

            // Setelah switch-case selesai
            if (!isset($success)) {
                $success = $result['success'] ?? false;
            }

            if (!isset($body)) {
                $body = is_array($result['body']) 
                    ? json_encode($result['body'], JSON_UNESCAPED_UNICODE)
                    : (is_string($result['body']) ? $result['body'] : json_encode((array) $result['body']));
            }


            // Simpan log jika ada nomor
            MessageLog::create([
                'user_id'      => Auth::id(),
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
                'user_id'      => Auth::id(),
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
}
