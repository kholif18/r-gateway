<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\Setting;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use App\Services\UpdateChecker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    protected $updater;

    public function __construct(UpdateChecker $updater)
    {
        $this->updater = $updater;
    }

    protected array $defaults = [
        'timeout' => '30',
        'max-retry' => '3',
        'retry-interval' => '10',
        'max-queue' => '100',
        'rate_limit_limit' => '5',
        'rate_limit_decay' => '60',
    ];

    public function index()
    {
        $userId = Auth::id();
        $settings = Setting::where('user_id', $userId)->pluck('value', 'key')->toArray();
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

        $userId = Auth::id();

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['user_id' => $userId, 'key' => $key],
                ['value' => self::castToString($value)]
            );
        }

        return response()->json(['status' => 'success']);
    }

    public function reset()
    {
        $userId = Auth::id();

        foreach ($this->defaults as $key => $value) {
            Setting::updateOrCreate(
                ['user_id' => $userId, 'key' => $key],
                ['value' => $value]
            );
        }

        // Kosongkan cache rate limit hanya milik session user login
        $clientSessions = ApiClient::where('user_id', $userId)->pluck('session_name');

        foreach ($clientSessions as $session) {
            Cache::forget("rate_limit:{$session}");
        }

        return response()->json(['status' => 'reset']);
    }

    /**
     * Pastikan nilai setting disimpan sebagai string
     */
    protected static function castToString(mixed $value): string
    {
        return is_bool($value) ? ($value ? '1' : '0') : (string) $value;
    }
}
