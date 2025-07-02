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

    public function checkUpdate()
    {
        $update = $this->updater->check();

        if ($update['is_outdated']) {
            session()->flash('update_available', $update['latest_version']);
            session()->flash('update_changelog', $update['changelog']);
        } else {
            session()->flash('update_status', 'Aplikasi sudah menggunakan versi terbaru.');
        }

        return back();
    }

    public function installUpdate()
    {
        try {
            $update = $this->updater->check();

            if (!$update['is_outdated']) {
                return back()->with('update_status', 'Tidak ada pembaruan yang tersedia.');
            }

            $version = $update['latest_version'];
            $downloadUrl = $update['url'];
            $zipPath = storage_path("app/update-v{$version}.zip");
            $tempDir = storage_path("app/update-temp");

            // Unduh ZIP update
            file_put_contents($zipPath, file_get_contents($downloadUrl));

            // Buat folder sementara jika belum ada
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Ekstrak ZIP
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($tempDir);
                $zip->close();
            } else {
                return back()->with('update_status', 'Gagal mengekstrak file update.');
            }

            // Cari folder hasil ekstrak
            $extractedFolder = File::directories($tempDir)[0] ?? null;
            if ($extractedFolder) {
                File::copyDirectory($extractedFolder, base_path());
            } else {
                return back()->with('update_status', 'Folder hasil ekstrak tidak ditemukan.');
            }

            // Bersihkan file sementara
            File::delete($zipPath);
            File::deleteDirectory($tempDir);

            // Artisan commands
            Artisan::call('migrate --force');
            Artisan::call('optimize:clear');

            return back()->with('update_status', "Update ke versi {$version} berhasil diinstal.");
        } catch (\Exception $e) {
            return back()->with('update_status', 'Gagal menginstal update: ' . $e->getMessage());
        }
    }

}
