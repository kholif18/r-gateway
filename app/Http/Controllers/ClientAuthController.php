<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use Illuminate\Http\Request;

class ClientAuthController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'client_name' => 'required|string',
            'api_token' => 'required|string',
        ]);

        $client = ApiClient::where('client_name', $request->client_name)
            ->where('api_token', $request->api_token)
            ->first();

        if (!$client) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token tidak valid',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Terhubung dengan r-gateway',
        ]);
    }
}
