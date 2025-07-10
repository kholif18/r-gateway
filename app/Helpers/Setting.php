<?php

namespace App\Helpers\Setting;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
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
    $userId = Auth::id();
    if (!$userId) return $default;

    return Cache::rememberForever("setting.{$userId}.{$key}", function () use ($key, $default, $userId) {
        $setting = Setting::where('user_id', $userId)
            ->where('key', $key)
            ->first();

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
    $userId = Auth::id();
    if (!$userId) return [];

    return Cache::rememberForever("settings.all.{$userId}", function () use ($userId) {
        return Setting::where('user_id', $userId)
            ->pluck('value', 'key')
            ->toArray();
    });
}
