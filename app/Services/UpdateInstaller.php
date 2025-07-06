<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class UpdateInstaller
{
    protected string $updateUrl;
    protected string $tempZip;
    protected string $tmpExtractPath;
    protected string $backupPath;
    protected string $backupZip;
    protected string $timestamp;

    public function __construct()
    {
        $this->updateUrl = Setting::get('update_url');
        $this->tempZip = storage_path('app/update.zip');
        $this->tmpExtractPath = storage_path('app/tmp');
        $this->backupPath = storage_path('app/backup');
        $this->timestamp = now()->format('Ymd_His');
        $this->backupZip = "{$this->backupPath}/r-gateway-backup-{$this->timestamp}.zip";
    }

    public function install(): array
    {
        if (!$this->updateUrl) {
            return $this->fail('URL pembaruan tidak ditemukan.');
        }

        if (!$this->backupApp()) {
            return $this->fail('Gagal membuat backup.');
        }

        if (!$this->downloadUpdate()) {
            return $this->fail('Gagal mengunduh file pembaruan.');
        }

        if (!$this->extractZip()) {
            return $this->fail('Gagal mengekstrak file pembaruan.');
        }

        $extractedPath = $this->getExtractedPath();
        if (!$extractedPath || !file_exists($extractedPath . '/artisan')) {
            return $this->fail('File Laravel tidak lengkap dalam update.');
        }

        try {
            $this->copyAll($extractedPath, base_path());
        } catch (\Exception $e) {
            return $this->fail('Gagal menyalin file update: ' . $e->getMessage());
        }

        // Cleanup
        File::delete($this->tempZip);
        File::deleteDirectory($this->tmpExtractPath);

        $this->updateAppVersion();

        return $this->success('Pembaruan berhasil diinstal. Backup otomatis telah disimpan.');
    }

    protected function backupApp(): bool
    {
        try {
            if (!file_exists($this->backupPath)) {
                mkdir($this->backupPath, 0755, true);
            }

            $zip = new \ZipArchive;
            if ($zip->open($this->backupZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                $exclude = ['vendor', 'node_modules', 'storage', '.git'];

                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator(base_path(), \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($files as $file) {
                    $filePath = $file->getRealPath();
                    $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);

                    foreach ($exclude as $folder) {
                        if (str_starts_with($relativePath, $folder . '/')) {
                            continue 2;
                        }
                    }

                    $zip->addFile($filePath, $relativePath);
                }

                $zip->close();
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    protected function downloadUpdate(): bool
    {
        try {
            $content = Http::get($this->updateUrl)->body();
            file_put_contents($this->tempZip, $content);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function extractZip(): bool
    {
        $zip = new \ZipArchive;
        if ($zip->open($this->tempZip) === TRUE) {
            $zip->extractTo($this->tmpExtractPath);
            $zip->close();
            return true;
        }
        return false;
    }

    protected function getExtractedPath(): ?string
    {
        $folders = glob($this->tmpExtractPath . '/*', GLOB_ONLYDIR);
        return $folders[0] ?? null;
    }

    protected function updateAppVersion(): void
    {
        try {
            $versionCheck = Http::get('https://raw.githubusercontent.com/kholif18/r-gateway/main/version.json');
            if ($versionCheck->ok()) {
                $newVersion = $versionCheck->json('version');
                Setting::set('app_version', $newVersion);
            }
        } catch (\Exception $e) {
            // Diamkan saja jika gagal
        }
    }

    protected function copyAll($source, $destination)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $sourcePath = $file->getRealPath();
            $relativePath = str_replace($source . DIRECTORY_SEPARATOR, '', $sourcePath);
            $targetPath = $destination . DIRECTORY_SEPARATOR . $relativePath;

            if ($file->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $targetDir = dirname($targetPath);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }

                if (!File::copy($sourcePath, $targetPath)) {
                    throw new \Exception("Gagal menyalin file: {$relativePath}");
                }
            }
        }
    }

    // Helper untuk return
    protected function fail(string $message): array
    {
        return ['success' => false, 'message' => $message];
    }

    protected function success(string $message): array
    {
        return ['success' => true, 'message' => $message];
    }
}
