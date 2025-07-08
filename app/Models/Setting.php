<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['user_id', 'key', 'value'];
    public $timestamps = true;

    protected static function booted()
    {
        static::saved(function ($setting) {
            Cache::forget(self::cacheKey($setting->user_id, $setting->key));
        });

        static::deleted(function ($setting) {
            Cache::forget(self::cacheKey($setting->user_id, $setting->key));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil nilai setting berdasarkan key dan user_id (default: user saat ini)
     */
    public static function get(string $key, $default = null, ?int $userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Coba ambil dari setting user
        if ($userId !== null) {
            return Cache::rememberForever(self::cacheKey($userId, $key), function () use ($key, $default, $userId) {
                return static::where('user_id', $userId)->where('key', $key)->value('value') ?? $default;
            });
        }

        // Ambil global setting (user_id null)
        return Cache::rememberForever("setting.global.{$key}", function () use ($key, $default) {
            return static::whereNull('user_id')->where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Simpan setting untuk user tertentu (default: user saat ini)
     */
    public static function set(string $key, $value, ?int $userId = null): self
    {
        $value = is_bool($value) ? ($value ? '1' : '0') : (string) $value;

        $setting = static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );

        // Hapus cache berdasarkan user
        if ($userId !== null) {
            Cache::forget(self::cacheKey($userId, $key));
        } else {
            Cache::forget("setting.global.{$key}");
        }

        return $setting;
    }

    /**
     * Bangun cache key
     */
    protected static function cacheKey(int $userId, string $key): string
    {
        return "setting.{$userId}.{$key}";
    }

    // Dalam model Setting
    public static function getGlobal(string $key, $default = null)
    {
        return Cache::rememberForever("setting.global.{$key}", function () use ($key, $default) {
            return static::whereNull('user_id')->where('key', $key)->value('value') ?? $default;
        });
    }

    public static function setGlobal(string|array $key, $value = null): void
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::updateOrCreate(
                    ['user_id' => null, 'key' => $k],
                    ['value' => is_bool($v) ? ($v ? '1' : '0') : (string) $v]
                );
                Cache::forget("setting.global.{$k}");
            }
        } else {
            static::updateOrCreate(
                ['user_id' => null, 'key' => $key],
                ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
            );
            Cache::forget("setting.global.{$key}");
        }
    }

}
