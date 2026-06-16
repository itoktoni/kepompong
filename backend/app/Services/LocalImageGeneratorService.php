<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class LocalImageGeneratorService
{
    private string $disk = 'public';
    private int $cols = 6;
    private int $rows = 4;

    private array $pageTypes = [
        'cover' => 'cover cerita',
        'moral' => 'panel moral dan kesimpulan',
        'default' => 'isi panel cerita',
    ];

    public function generate(string $prompt, int $width = 400, int $height = 600, int $pageNumber = 1, int $totalPages = 24): ?string
    {
        $dir = 'images/stories';

        if (!Storage::disk($this->disk)->exists($dir)) {
            Storage::disk($this->disk)->makeDirectory($dir);
        }

        $activityId = time();
        $folder = (string) $activityId;
        $baseDir = $dir . '/' . $folder;

        if (!Storage::disk($this->disk)->exists($baseDir)) {
            Storage::disk($this->disk)->makeDirectory($baseDir);
        }

        $panelNumber = max(1, min($this->cols * $this->rows, $pageNumber));

        if ($pageNumber <= 1) {
            $filename = '01.png';
        } elseif ($pageNumber >= $totalPages) {
            $filename = str_pad((string) $totalPages, 2, '0', STR_PAD_LEFT) . '.png';
        } else {
            $filename = str_pad((string)$pageNumber, 2, '0', STR_PAD_LEFT) . '.png';
        }

        $path = $baseDir . '/' . $filename;

        if ($this->tryGeneratePanelGd($prompt, $pageNumber, $totalPages, $width, $height, $path)) {
            return Storage::disk($this->disk)->url($path);
        }

        if ($this->generateFallback($pageNumber, $totalPages, $path)) {
            return Storage::disk($this->disk)->url($path);
        }

        return null;
    }

    private function tryGeneratePanelGd(string $prompt, int $pageNumber, int $totalPages, int $width, int $height, string $path): bool
    {
        if (!extension_loaded('gd')) {
            return false;
        }

        $image = imagecreatetruecolor($width, $height);
        if (!$image) {
            return false;
        }

        $bg = imagecolorallocate($image, 232, 245, 233);
        $primary = imagecolorallocate($image, 23, 108, 51);
        $accent = imagecolorallocate($image, 183, 217, 188);
        $text = imagecolorallocate($image, 22, 22, 22);

        imagefilledrectangle($image, 0, 0, $width, $height, $bg);
        imagefilledellipse($image, $width / 2, 180, 260, 260, $accent);

        $label = $pageNumber === 1 ? 'COVER' : ($pageNumber === $totalPages ? 'MORAL' : 'Halaman ' . $pageNumber);
        imagestring($image, 5, 40, 110, $label, $primary);
        imagestring($image, 3, 35, 310, mb_strimwidth($prompt, 0, 28, '...'), $text);

        imagerectangle($image, 10, 10, $width - 10, $height - 10, $primary);

        $fullPath = Storage::disk($this->disk)->path($path);
        $result = imagepng($image, $fullPath);
        imagedestroy($image);

        return $result;
    }

    private function generateFallback(int $pageNumber, int $totalPages, string $path): bool
    {
        $width = 400;
        $height = 600;
        $innerWidth = $width - 24;
        $innerHeight = $height - 24;

        $label = $pageNumber === 1 ? 'COVER' : ($pageNumber === $totalPages ? 'MORAL' : 'Halaman ' . $pageNumber);

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect width="{$width}" height="{$height}" fill="#E8F5E9" />
  <circle cx="200" cy="180" r="120" fill="#B7D9BC" />
  <text x="200" y="190" font-family="sans-serif" font-size="18" text-anchor="middle" fill="#176c33">{$label}</text>
  <text x="200" y="360" font-family="sans-serif" font-size="16" text-anchor="middle" fill="#333">Panel komik</text>
  <rect x="12" y="12" width="{$innerWidth}" height="{$innerHeight}" fill="none" stroke="#176c33" stroke-width="4" rx="24" />
</svg>
SVG;

        return Storage::disk($this->disk)->put($path, $svg);
    }
}
