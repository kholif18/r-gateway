<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WebhookController extends Controller
{
    public function session(Request $request)
    {
        $session = $request->session;
        $status = $request->status;

        if ($session && $status) {
            Cache::put("whatsapp_status_{$session}", strtoupper($status), now()->addMinutes(5));
        }

        return response()->json(['ok' => true]);
    }

    public function message(Request $request)
    {
        Log::info('Pesan Masuk:', $request->all());
        // Simpan ke DB jika ingin
        return response()->json(['ok' => true]);
    }
}
