<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use App\Models\ApiClient;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RateLimitPerClient
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');
        
        $client = $request->input('client');

        // Logging awal untuk debug
        Log::info('RateLimit middleware triggered', ['client' => $client]);

        if (!$client) {
            return response()->json([
                'error' => 'Parameter client wajib diisi.'
            ], 400);
        }

        // Ambil data client dari DB
        $clientData = ApiClient::where('client_name', $client)->first();

        if (!$clientData) {
            return response()->json([
                'error' => 'Client tidak ditemukan untuk proses rate limit.'
            ], 403);
        }

        // Buat cache key berdasarkan session name client
        $key = 'rate_limit:' . $clientData->session_name;

        // Ambil setting limit dan decay
        $limit = (int) Setting::where('key', 'rate_limit_limit')->value('value') ?: 10;
        $decay = (int) Setting::where('key', 'rate_limit_decay')->value('value') ?: 60;

        $count = Cache::get($key, 0);

        if ($count >= $limit) {
            Log::info('LIMIT EXCEEDED: Logging to message_logs');
            // Log ke dalam database
            MessageLog::create([
                'client_name'   => $clientData->client_name,
                'session_name'  => $clientData->session_name,
                'phone'         => $request->input('to'),
                'message'       => $request->input('msg'),
                'status'        => 'rate_limited',
                'response'      => 'Rate limit exceeded',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Rate limit exceeded. Silakan coba lagi nanti.'
                ], 429);
            }
        }

        // Simpan kembali ke cache dengan expiry
        Cache::put($key, $count + 1, now()->addSeconds($decay));

        return $next($request);
    }
}
