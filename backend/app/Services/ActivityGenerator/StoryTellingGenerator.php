<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;
use Illuminate\Support\Facades\Log;

class StoryTellingGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        $ai = app(AiService::class);
        $provider = config('ai.default_provider');
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $skill = $input['skill'] ?? '';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $pagesCount = max(1, min(24, $input['pages'] ?? 16));
        $variation = $input['variation'] ?? 1;

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $parsed = $this->parseKeterangan($desc);
        $titles = $parsed['titles'];
        $latar = $parsed['latar'];

        $selectedTitle = '';
        if (!empty($titles)) {
            $index = ($variation - 1) % count($titles);
            $selectedTitle = $titles[$index];
        }

        $themeInput = $selectedTitle ?: ($theme ?: 'cerita anak');

        $context = "Tema: {$themeInput}\n";
        if (!empty($skill)) $context .= "Skill/Nilai: Cerita harus mengajarkan tentang \"{$skill}\"\n";
        if (!empty($informasi)) $context .= "Fakta: {$informasi}\n";
        if (!empty($latar)) $context .= "Latar: {$latar}\n";
        if (!empty($notes)) $context .= "Catatan: {$notes}\n";
        if (!empty($agama)) $context .= "Agama: {$agama}\n";

        $systemPrompt = <<<PROMPT
Kamu menulis cerita anak Indonesia. Tulis TEPAT {$pagesCount} halaman.

ATURAN:
- Bahasa Indonesia sederhana, anak usia {$minAge}-{$maxAge} tahun
- Setiap halaman: 2-4 kalimat, MAKSIMAL 40 kata
- Jangan gunakan kata sulit atau bahasa asing
- Cerita harus menarik, punya alur jelas, banyak tempat berbeda
- Cerita harus menghibur terlebih dahulu, mengajarkan kemudian.
- Jangan terasa seperti ceramah.
- Moral muncul dari pengalaman tokoh.
- Konflik harus alami dan mudah dipahami anak.
- Ending hangat dan memuaskan.
- Setiap cerita harus benar-benar berbeda.
- Jika pakai nama karakter, gunakan nama Indonesia yang familiar: Paman, Bibi, Ayah, Ibu, Nenek, Kakek, Adik, dll
- jangan pakai nama benda sebagai nama karakter (misal: "Cahaya", "Bintang", "Pelangi", "Putih")
- buat moral yang di mengerti anak anak, dan jangan cuma sedikit misalnya : "Bersyukur adalah sikap indah. Ketika kita mensyukuri apa yang kita punya, kita akan merasa bahagia dan ingin merawatnya dengan baik".
- Jika cerita tentang hewan/benda, JANGAN beri nama manusia, cukup sebut "kucing itu", "ikan itu", dll
- Jika ada Skill/Nilai, cerita HARUS mengajarkan nilai tersebut secara natural melalui alur cerita

OUTPUT JSON:
{"title":"Judul","desc":"Deskripsi singkat","moral":"Pelajaran","pages":[{"text":"cerita 1"},{"text":"cerita 2"}]}

HANYA output JSON, tidak ada teks lain.
PROMPT;

        $userPrompt = "Buatkan cerita anak tentang: {$themeInput}\n\n{$context}";

        $result = $this->callAi($ai, $provider, $model, $systemPrompt, $userPrompt, $pagesCount);

        if ($result) {
            $finalTitle = !empty($selectedTitle) ? $selectedTitle : ($result['title'] ?? $theme);
            return [
                'title' => $this->cleanText($finalTitle),
                'desc'  => $this->cleanText($result['desc'] ?? $desc),
                'moral' => $this->cleanText($result['moral'] ?? $informasi),
                'pages' => $result['pages'],
                'source' => 'ai',
            ];
        }

        return $this->fallback($theme, $desc, $informasi, $pagesCount);
    }

    private function callAi(AiService $ai, string $provider, string $model, string $system, string $user, int $pagesCount): ?array
    {
        for ($attempt = 0; $attempt < 2; $attempt++) {
            try {
                $result = $ai->chat($provider, $model, $system, $user);

                if (!is_array($result) || empty($result['pages'])) {
                    continue;
                }

                $pages = array_slice($result['pages'], 0, $pagesCount);
                $renumbered = [];
                foreach ($pages as $index => $page) {
                    $text = $this->cleanText($page['text'] ?? (is_string($page) ? $page : ''));
                    if (empty($text)) continue;
                    $renumbered[] = ['num' => $index + 1, 'text' => $text];
                }

                if (count($renumbered) < 3) {
                    continue;
                }

                return [
                    'title' => $result['title'] ?? '',
                    'desc'  => $result['desc'] ?? '',
                    'moral' => $result['moral'] ?? '',
                    'pages' => $renumbered,
                ];
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    public function buildActivityData(array $result, array $input): array
    {
        $pages = [];
        foreach ($result['pages'] as $index => $page) {
            if ($index === 0) continue;
            $pages[] = ['num' => $index, 'text' => $page['text'] ?? ''];
        }

        return array_merge($this->baseActivityData('storytelling', $result, $input), [
            'moral' => $result['moral'] ?? '',
            'data'  => ['pages' => $pages],
        ]);
    }

    private function fallback(string $theme, string $desc, string $informasi, int $pagesCount): array
    {
        $title = 'Kisah tentang ' . ucfirst($theme);
        $pages = [];
        for ($i = 1; $i <= $pagesCount; $i++) {
            $pages[] = ['num' => $i, 'text' => "Halaman {$i} tentang {$theme}"];
        }
        return ['title' => $title, 'desc' => $desc, 'moral' => $informasi, 'pages' => $pages, 'source' => 'fallback'];
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}
