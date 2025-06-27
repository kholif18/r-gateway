<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RateLimitPerClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = $request->input('client');
        $key = 'rate_limit:' . $client;

        $limit = 10; // maksimal 10 request
        $decay = 60; // dalam 60 detik (1 menit)

        $count = Cache::get($key, 0);

        if ($count >= $limit) {
            return response()->json([
                'error' => 'Rate limit exceeded. Coba lagi nanti.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        Cache::put($key, $count + 1, now()->addSeconds($decay));
        
        return $next($request);
    }
}
