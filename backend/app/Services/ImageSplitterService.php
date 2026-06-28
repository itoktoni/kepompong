<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

class ImageSplitterService
{
    private static array $grids = [
        4  => [2, 2],
        6  => [3, 2],
        8  => [4, 2],
        9  => [3, 3],
        10 => [5, 2],
        12 => [4, 3],
        16 => [4, 4],
        20 => [5, 4],
        24 => [6, 4],
        25 => [5, 5],
    ];

    public static function getGrid(int $pages): ?array
    {
        if (isset(self::$grids[$pages])) {
            return self::$grids[$pages];
        }

        $root = (int) sqrt($pages);

        if ($root * $root === $pages) {
            return [$root, $root];
        }

        return null;
    }

    public static function split(
        UploadedFile $file,
        int $activityId,
        int $pages,
        ?string $folderName = null
    ): array {

        $grid = self::getGrid($pages);

        if (!$grid) {
            throw new InvalidArgumentException(
                "Grid untuk {$pages} pages belum didukung."
            );
        }

        [$cols, $rows] = $grid;

        $tmpPath = tempnam(sys_get_temp_dir(), 'split_');

        if (!$tmpPath) {
            throw new InvalidArgumentException('Gagal membuat file temporary.');
        }

        try {
            $moved = $file->move(dirname($tmpPath), basename($tmpPath));
            $filePath = $moved->getRealPath() ?: $moved->getPathname();
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            throw new InvalidArgumentException('Gagal memindahkan file upload: ' . $e->getMessage());
        }

        $imageInfo = @getimagesize($filePath);

        if (!$imageInfo) {
            @unlink($filePath);
            throw new InvalidArgumentException('File bukan gambar yang valid.');
        }

        $mime = $imageInfo['mime'];

        if (!in_array($mime, ['image/png', 'image/jpeg', 'image/webp'], true)) {
            @unlink($filePath);
            throw new InvalidArgumentException('Format tidak didukung: ' . $mime);
        }

        $handle = fopen($filePath, 'rb');
        $header = $handle ? fread($handle, 8192) : '';
        if ($handle) fclose($handle);

        $malicious = ['<\?php', '<\?=', '<script', 'javascript:', 'eval\s*\(', 'exec\s*\(', 'system\s*\(', 'shell_exec', 'base64_decode'];
        foreach ($malicious as $pattern) {
            if (preg_match('/' . $pattern . '/i', $header)) {
                @unlink($filePath);
                throw new InvalidArgumentException('File terdeteksi mengandung konten berbahaya.');
            }
        }

        $img = match ($mime) {
            'image/png'  => imagecreatefrompng($filePath),
            'image/jpeg' => imagecreatefromjpeg($filePath),
            'image/webp' => imagecreatefromwebp($filePath),
        };

        if (!$img) {
            @unlink($filePath);
            throw new InvalidArgumentException('Gagal membaca gambar.');
        }

        $width  = imagesx($img);
        $height = imagesy($img);

        $panelWidth  = (int) floor($width / $cols);
        $panelHeight = (int) floor($height / $rows);

        $folder = $folderName ?: "images/stories/{$activityId}";

        if (Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->deleteDirectory($folder);
        }

        Storage::disk('public')->makeDirectory($folder);

        $masterContent = file_get_contents($filePath);
        Storage::disk('public')->put("{$folder}/master.png", $masterContent);

        $files = [];
        $counter = 1;
        $totalPanels = $cols * $rows;

        for ($row = 0; $row < $rows; $row++) {

            for ($col = 0; $col < $cols; $col++) {

                $cropWidth = ($col === $cols - 1)
                    ? $width - ($col * $panelWidth)
                    : $panelWidth;

                $cropHeight = ($row === $rows - 1)
                    ? $height - ($row * $panelHeight)
                    : $panelHeight;

                $crop = imagecrop($img, [
                    'x'      => $col * $panelWidth,
                    'y'      => $row * $panelHeight,
                    'width'  => $cropWidth,
                    'height' => $cropHeight,
                ]);

                if (!$crop) {
                    $counter++;
                    continue;
                }

                if ($counter === 1) {
                    $filename = 'cover.png';
                } else {
                    $filename = ($counter - 1) . '.png';
                }

                ob_start();
                imagepng($crop);
                $imageContent = ob_get_clean();

                imagedestroy($crop);

                Storage::disk('public')->put(
                    "{$folder}/{$filename}",
                    $imageContent
                );

                $files[] = $filename;

                $counter++;
            }
        }

        imagedestroy($img);
        @unlink($filePath);

        return [
            'folder' => $folder,
            'master' => "{$folder}/master.png",
            'grid' => [
                'cols' => $cols,
                'rows' => $rows,
            ],
            'total_panels' => $totalPanels,
            'cover' => "{$folder}/cover.png",
            'files' => $files,
        ];
    }

    public static function deleteFolder(int $activityId, ?string $folderName = null): void
    {
        $folder = $folderName ?: "images/stories/{$activityId}";

        if (Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->deleteDirectory($folder);
        }
    }
}
