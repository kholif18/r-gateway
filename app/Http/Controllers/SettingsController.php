<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\Setting;
use App\Models\ApiClient;
use Illuminate\Http\Request;
use App\Services\UpdateChecker;
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
            // rate_limit_* bisa divalidasi juga kalau dibutuhkan
        ]);

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => self::castToString($value)]
            );
        }

        return response()->json(['status' => 'success']);
    }

    public function reset()
    {
        foreach ($this->defaults as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Kosongkan rate limit cache
        foreach (ApiClient::all() as $client) {
            Cache::forget("rate_limit:{$client->session_name}");
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
