<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiClientAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('secret') ?? $request->input('secret');

        if (!$token || !ApiClient::where('api_token', $token)->where('is_active', true)->exists()) {
            return response()->json(['error' => 'Token API tidak valid atau nonaktif'], 403);
        }
        
        return $next($request);
    }
}
