<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsappLoginController extends Controller
{
    public function index()
    {
        return view('wa-login');
    }

    public function qr()
    {
        $response = Http::get(env('WA_GATEWAY_API') . '/instance/default/qr');
        return response()->json($response->json());
    }

    public function status()
    {
        $response = Http::get(env('WA_GATEWAY_API') . '/instance/default/status');
        return response()->json($response->json());
    }
}
