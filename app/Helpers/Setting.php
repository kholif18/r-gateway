<?php

namespace App\Helpers\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Ambil nilai setting berdasarkan key.
 *
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function setting(string $key, $default = null)
{
    return Cache::rememberForever("setting.$key", function () use ($key, $default) {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    });
}

/**
 * Ambil nilai setting dan pastikan boolean.
 *
 * @param string $key
 * @param bool $default
 * @return bool
 */
function setting_bool(string $key, bool $default = false): bool
{
    return filter_var(setting($key, $default), FILTER_VALIDATE_BOOLEAN);
}

/**
 * Ambil semua setting sekaligus sebagai array [key => value]
 *
 * @return array
 */
function settings_all(): array
{
    return Cache::rememberForever("settings.all", function () {
        return Setting::pluck('value', 'key')->toArray();
    });
}
