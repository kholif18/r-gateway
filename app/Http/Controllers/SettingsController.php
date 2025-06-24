<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // Default settings yang akan direset
        $default = [
            'api_token' => 'default-token',
            'api_access' => '1',
            'timeout' => '30',
            'max-retry' => '3',
            'retry-interval' => '10',
            'max-queue' => '100',
        ];

        foreach ($default as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json(['status' => 'reset']);
    }
}
