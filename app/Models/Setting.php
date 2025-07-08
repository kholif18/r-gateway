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
        if (!$userId) return $default;

        return Cache::rememberForever(self::cacheKey($userId, $key), function () use ($key, $default, $userId) {
            return static::where('user_id', $userId)->where('key', $key)->value('value') ?? $default;
        });
    }

    /**
     * Simpan setting untuk user tertentu (default: user saat ini)
     */
    public static function set(string $key, $value, ?int $userId = null): self
    {
        $userId = $userId ?? Auth::id();

        $setting = static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value]
        );

        Cache::forget(self::cacheKey($userId, $key));

        return $setting;
    }

    /**
     * Bangun cache key
     */
    protected static function cacheKey(int $userId, string $key): string
    {
        return "setting.{$userId}.{$key}";
    }
}
