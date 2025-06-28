<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        // Ambil semua settings dalam bentuk key => value
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('settings', compact('settings'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'timeout' => 'required|numeric|min:5|max:120',
            'max-retry' => 'required|numeric|min:0|max:10',
            'retry-interval' => 'required|numeric|min:5|max:60',
            'max-queue' => 'required|numeric|min:10|max:1000',
        ]);

        // ✅ Simpan ke DB
        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : $value]
            );
        }

        return response()->json(['status' => 'success']);
    }

    public function reset()
    {
        // Default settings
        $default = [
            'timeout' => '30',
            'max-retry' => '3',
            'retry-interval' => '10',
            'max-queue' => '100',
            'rate_limit_limit' => '5',
            'rate_limit_decay' => '60',
        ];

        // Simpan kembali default setting
        foreach ($default as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Hapus cache rate limit untuk semua client
        $clients = \App\Models\ApiClient::all();
        foreach ($clients as $client) {
            Cache::forget('rate_limit:' . $client->session_name);
        }

        return response()->json(['status' => 'reset']);
    }
}
