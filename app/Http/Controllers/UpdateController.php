<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\UpdateChecker;
use App\Services\UpdateInstaller;
use Illuminate\Support\Facades\Cache;

class UpdateController extends Controller
{
    public function check()
{
    try {
        $currentVersion = Setting::get('app_version', '1.0.0');

        $checker = new UpdateChecker();
        $info = Cache::remember('update_check', 60, function () {
            return (new UpdateChecker())->check();
        });

        if ($info['is_outdated']) {
            Setting::set('update_url', $info['url']);
        }

        return response()->json([
            'update_available' => $info['is_outdated'],
            'version' => $info['latest_version'],
            'message' => $info['is_outdated']
                ? "Tersedia versi baru: v{$info['latest_version']}"
                : "Aplikasi Anda sudah menggunakan versi terbaru ({$currentVersion}).",
            'download_url' => $info['url'],
            'changelog' => $info['changelog'],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'update_available' => false,
            'message' => 'Gagal memeriksa pembaruan.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public function install(UpdateInstaller $installer)
    {
        $result = $installer->install();
        return response()->json($result, $result['success'] ? 200 : 500);
    }
}
