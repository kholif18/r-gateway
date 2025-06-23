<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    private $path = 'storage/settings.json';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = json_decode(File::get(base_path($this->path)), true);
        return view('settings', compact('settings'));
    }

    public function save(Request $request)
    {
        $data = $request->all();

        $save = [
            'api_token' => $data['api_token'],
            'api_access' => isset($data['api_access']),
            'default_sender' => $data['default_sender'],
            'sender_name' => $data['sender_name'],
            'use_sender_name' => isset($data['use_sender_name']),
            'timeout' => (int) $data['timeout'],
            'max_retry' => (int) $data['max_retry'],
            'retry_interval' => (int) $data['retry_interval'],
            'max_queue' => (int) $data['max_queue'],
            'log_level' => $data['log_level'],
            'debug_mode' => isset($data['debug_mode']),
        ];

        File::put(base_path($this->path), json_encode($save, JSON_PRETTY_PRINT));
        return response()->json(['success' => true]);
    }

    public function reset()
    {
        File::put(base_path($this->path), json_encode([
            'api_token' => 'default-token',
            'api_access' => true,
            'default_sender' => '',
            'sender_name' => '',
            'use_sender_name' => false,
            'timeout' => 30,
            'max_retry' => 3,
            'retry_interval' => 10,
            'max_queue' => 100,
            'log_level' => 'info',
            'debug_mode' => false
        ], JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }
}
