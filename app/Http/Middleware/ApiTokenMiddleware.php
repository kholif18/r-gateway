<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('secret') ?? $request->input('secret');
        $clientName = $request->query('client') ?? $request->input('client');

        if (!$token) {
            return response()->json(['error' => 'API token tidak ditemukan'], 401);
        }

        $client = ApiClient::where('api_token', $token)
            ->when($clientName, fn($q) => $q->where('client_name', $clientName))
            ->where('is_active', true)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Token tidak valid atau tidak cocok dengan nama aplikasi'], 403);
        }

        // Jika perlu, kamu bisa inject client ke request
        $request->merge(['_api_client' => $client]);

        return $next($request);
    }
}
