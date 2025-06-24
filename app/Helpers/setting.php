<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

function setting(string $key, $default = null)
{
    return Cache::rememberForever("setting.$key", function () use ($key, $default) {
        $setting = Setting::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    });
}