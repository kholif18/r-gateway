<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class UpdateChecker
{
    protected $currentVersion;
    protected $updateUrl;

    public function __construct()
    {
        // Ambil versi dari database (fallback ke config jika tidak ada)
        $this->currentVersion = Setting::getGlobal('app_version', config('app.version'));
        $this->updateUrl = 'https://raw.githubusercontent.com/kholif18/r-gateway/main/version.json';
    }

    public function check()
    {
        $response = Http::get($this->updateUrl);

        if ($response->ok()) {
            $latest = $response->json();

            $isOutdated = version_compare($this->currentVersion, $latest['version'], '<');

            return [
                'is_outdated'    => $isOutdated,
                'latest_version' => $latest['version'],
                'changelog'      => $latest['changelog'] ?? '',
                'url'            => $latest['url'] ?? null,
                'release_page'   => $latest['release_page'] ?? null,
            ];
        }

        // fallback jika gagal mengambil dari remote
        return [
            'is_outdated'    => false,
            'latest_version' => $this->currentVersion,
            'changelog'      => '',
            'url'            => null,
            'release_page'   => null,
        ];
    }
}
