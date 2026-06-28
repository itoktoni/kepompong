<?php

namespace App\Services;

use App\ActivityType;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ZipArchive;

class ZipUploadService
{
    private const MAX_ZIP_SIZE = 50 * 1024 * 1024;

    private const PAGE_COUNTS = [
        3 => [2, 2],
        8 => [3, 3],
        15 => [4, 4],
        24 => [5, 5],
    ];

    private const DATA_KEYS = [
        'pages', 'roles', 'how', 'rules', 'script', 'tips',
        'duration', 'difficulty', 'materials', 'steps', 'lyrics',
        'moves', 'questions', 'benefit', 'observation', 'explanation',
        'slides', 'tags', 'exercises', 'audio_url', 'fun_fact',
    ];

    private const ALLOWED_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'json'];

    public function process(string $zipPath, ?int $activityId = null, ?int $createdBy = null, ?string $creator = null): array
    {
        if (!file_exists($zipPath)) {
            throw new InvalidArgumentException('File ZIP tidak ditemukan.');
        }

        if (filesize($zipPath) > self::MAX_ZIP_SIZE) {
            throw new InvalidArgumentException('Ukuran ZIP terlalu besar. Maksimal 50MB.');
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new InvalidArgumentException('Gagal membuka file ZIP.');
        }

        $extractDir = sys_get_temp_dir() . '/zipupload_' . Str::random(8);
        mkdir($extractDir, 0755, true);

        try {
            $zip->extractTo($extractDir);
            $zip->close();

            $searchDir = $extractDir;
            $entries = array_diff(scandir($extractDir), ['.', '..']);
            if (count($entries) === 1 && is_dir($extractDir . '/' . $entries[0])) {
                $searchDir = $extractDir . '/' . $entries[0];
            }

            $this->validateNoForeignFiles($searchDir);

            $dataJson = $this->findFile($searchDir, 'data.json');
            if (!$dataJson) {
                throw new InvalidArgumentException('data.json tidak ditemukan di dalam ZIP.');
            }

            $data = json_decode(file_get_contents($dataJson), true);
            if (!$data || !is_array($data)) {
                throw new InvalidArgumentException('data.json tidak valid.');
            }

            if (empty($data['title'])) {
                throw new InvalidArgumentException('data.json harus memiliki field "title".');
            }

            $type = $data['type'] ?? 'storytelling';
            if (!ActivityType::tryFrom($type)) {
                throw new InvalidArgumentException("Type tidak valid: {$type}. Gunakan: " . implode(', ', array_column(ActivityType::cases(), 'value')));
            }

            $pageCount = $this->resolvePageCount($data);

            if ($pageCount > 0 && !isset(self::PAGE_COUNTS[$pageCount])) {
                throw new InvalidArgumentException(
                    "Jumlah pages tidak valid: {$pageCount}. Harus: " . implode(', ', array_keys(self::PAGE_COUNTS)) . "."
                );
            }

            $slug = Str::slug($data['title']);

            $coverPath = $this->findFile($searchDir, 'cover');
            $pageImages = [];

            if ($pageCount > 0) {
                for ($i = 1; $i <= $pageCount; $i++) {
                    $found = $this->findFile($searchDir, (string) $i);
                    if (!$found) {
                        throw new InvalidArgumentException("Gambar page {$i} ({$i}.png) tidak ditemukan di ZIP.");
                    }
                    $this->validateImage($found);
                    $pageImages[] = $found;
                }

                if (!$coverPath) {
                    $coverPath = $pageImages[0];
                } else {
                    $this->validateImage($coverPath);
                }

                $imageCount = count($this->findAllImages($searchDir));
                $expectedImages = $pageCount + ($coverPath && $coverPath !== $pageImages[0] ? 1 : 0);
                $expectedMin = $coverPath === $pageImages[0] ? $pageCount : $pageCount + 1;
                if ($imageCount > $expectedMin + 1) {
                    throw new InvalidArgumentException(
                        "Jumlah gambar di ZIP ({$imageCount}) tidak sesuai. Diharapkan {$expectedMin} gambar ({$pageCount} pages + 1 cover)."
                    );
                }
            } else {
                if (!$coverPath) {
                    $allImages = $this->findAllImages($searchDir);
                    if (empty($allImages)) {
                        throw new InvalidArgumentException('Tidak ada gambar ditemukan di ZIP.');
                    }
                    $coverPath = $allImages[0];
                }
                $this->validateImage($coverPath);
            }

            $activityData = $this->buildActivityData($data, $type);

            if ($activityId) {
                $activity = Activity::findOrFail($activityId);
                $oldSlug = $activity->slug;
                $activity->title = $data['title'];
                $activity->slug = $slug;
                $activity->type = $type;
                $activity->desc = $data['desc'] ?? $activity->desc;
                $activity->moral = $data['moral'] ?? $activity->moral;
                $activity->data = $activityData;
                $activity->image = 'cover.png';
                $activity->status = 'pending';
                $activity->save();

                if ($oldSlug && $oldSlug !== $slug) {
                    $oldFolder = "images/{$type}/{$oldSlug}";
                    if (Storage::disk('public')->exists($oldFolder)) {
                        Storage::disk('public')->deleteDirectory($oldFolder);
                    }
                }
            } else {
                $existing = Activity::where('slug', $slug)
                    ->where('status', 'pending')
                    ->first();

                if ($existing) {
                    $existing->type = $type;
                    $existing->title = $data['title'];
                    $existing->desc = $data['desc'] ?? $existing->desc;
                    $existing->moral = $data['moral'] ?? $existing->moral;
                    $existing->data = $activityData;
                    $existing->image = 'cover.png';
                    $existing->created_by = $createdBy;
                    $existing->creator = $creator;
                    $existing->save();
                    $activity = $existing;
                } else {
                    $activity = Activity::create([
                        'type' => $type,
                        'title' => $data['title'],
                        'slug' => $slug,
                        'desc' => $data['desc'] ?? '',
                        'moral' => $data['moral'] ?? '',
                        'data' => $activityData,
                        'image' => 'cover.png',
                        'status' => 'pending',
                        'active' => true,
                        'created_by' => $createdBy,
                        'creator' => $creator,
                    ]);
                }
            }

            $folder = "images/{$type}/{$slug}";

            if (Storage::disk('public')->exists($folder)) {
                Storage::disk('public')->deleteDirectory($folder);
            }
            Storage::disk('public')->makeDirectory($folder);

            $secureUpload = app(SecureImageUploadService::class);

            $secureUpload->uploadFromPath($coverPath, $folder, 'cover.png');
            $storedFiles = ['cover.png'];

            for ($i = 0; $i < $pageCount; $i++) {
                $filename = ($i + 1) . '.png';
                $secureUpload->uploadFromPath($pageImages[$i], $folder, $filename);
                $storedFiles[] = $filename;
            }

            $this->protectDirectory($folder);

            return [
                'activity_id' => $activity->id,
                'type' => $type,
                'folder' => $folder,
                'slug' => $slug,
                'pages' => $pageCount,
                'files' => $storedFiles,
                'title' => $data['title'],
            ];
        } finally {
            $this->deleteDirectory($extractDir);
        }
    }

    private function resolvePageCount(array $data): int
    {
        if (!empty($data['pages']) && is_array($data['pages'])) {
            return count($data['pages']);
        }

        foreach (['steps', 'questions', 'roles', 'slides'] as $key) {
            if (!empty($data[$key]) && is_array($data[$key])) {
                return count($data[$key]);
            }
        }

        return 0;
    }

    private function buildActivityData(array $data, string $type): array
    {
        $activityData = [
            'emoji' => $data['emoji'] ?? ActivityType::from($type)->emoji(),
        ];

        foreach (self::DATA_KEYS as $key) {
            if (isset($data[$key])) {
                $activityData[$key] = $data[$key];
            }
        }

        if (!empty($activityData['pages']) && is_array($activityData['pages'])) {
            foreach ($activityData['pages'] as $i => &$page) {
                if (!isset($page['num'])) {
                    $page['num'] = $i + 1;
                }
            }
            unset($page);
        }

        return $activityData;
    }

    private function validateNoForeignFiles(string $dir): void
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                throw new InvalidArgumentException("ZIP tidak boleh mengandung subfolder: {$file}/");
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                throw new InvalidArgumentException("File tidak diizinkan: {$file}. Hanya .png, .jpg, .jpeg, .webp, .json");
            }
            if ($ext === 'json') {
                $this->scanForMaliciousContent($path, $file);
            }
        }
    }

    private function validateImage(string $path): void
    {
        $basename = basename($path);

        $mime = mime_content_type($path);
        $allowed = ['image/png', 'image/jpeg', 'image/webp'];
        if (!in_array($mime, $allowed, true)) {
            throw new InvalidArgumentException("File {$basename} bukan gambar yang valid ({$mime}).");
        }

        $info = @getimagesize($path);
        if (!$info) {
            throw new InvalidArgumentException("File {$basename} tidak bisa dibaca sebagai gambar.");
        }

        $this->scanForMaliciousContent($path, $basename);
    }

    private function scanForMaliciousContent(string $path, string $filename): void
    {
        $handle = fopen($path, 'rb');
        if (!$handle) return;

        $header = fread($handle, 8192);
        fclose($handle);

        $patterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<script\b/i',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/eval\s*\(/i',
            '/exec\s*\(/i',
            '/system\s*\(/i',
            '/passthru\s*\(/i',
            '/shell_exec\s*\(/i',
            '/base64_decode\s*\(/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $header)) {
                throw new InvalidArgumentException("File {$filename} terdeteksi mengandung konten berbahaya.");
            }
        }
    }

    private function findFile(string $dir, string $basename): ?string
    {
        $extensions = ['', '.png', '.jpg', '.jpeg', '.webp'];
        foreach ($extensions as $ext) {
            $path = $dir . '/' . $basename . $ext;
            if (file_exists($path) && is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    private function findAllImages(string $dir): array
    {
        $images = [];
        $files = glob($dir . '/*.{png,jpg,jpeg,webp}', GLOB_BRACE);
        sort($files);
        foreach ($files as $file) {
            if (is_file($file)) {
                $images[] = $file;
            }
        }
        return $images;
    }

    private function protectDirectory(string $folder): void
    {
        $fullPath = Storage::disk('public')->path($folder);
        if (!is_dir($fullPath)) return;

        $htaccess = $fullPath . '/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, <<<HTACCESS
<FilesMatch "\.(php|phtml|php3|php4|php5|php7|phps|cgi|pl|asp|aspx|jsp|sh|bash)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

<FilesMatch "\.(jpg|jpeg|png|gif|webp|svg|ico)$">
    Order Deny,Allow
    Allow from all
</FilesMatch>

Options -ExecCGI
AddHandler default-handler .jpg .jpeg .png .gif .webp
HTACCESS
            );
        }
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}
