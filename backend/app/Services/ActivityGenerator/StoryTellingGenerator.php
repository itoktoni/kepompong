<?php

namespace App\Services\ActivityGenerator;

use App\Services\AiService;
use Illuminate\Support\Facades\Log;

class StoryTellingGenerator extends BaseGenerator
{
    public function generateContent(array $input): array
    {
        Log::info($input);
        $ai = app(AiService::class);
        $provider = config('ai.default_provider') ?? '';
        $model = $ai->getModel($provider);

        $theme = $input['theme'] ?? '';
        $desc = $input['desc'] ?? '';
        $informasi = $input['informasi'] ?? $input['moral'] ?? '';
        $notes = $input['notes'] ?? '';
        $skill = $input['skill'] ?? '';
        $ages = $input['ages'] ?? [];
        $agama = $input['agama'] ?? null;
        $pagesCount = max(1, min(25, $input['pages'] ?? 9));
        $variation = $input['variation'] ?? 1;

        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $themeInput = $theme ?: 'cerita anak';

        $variationHint = $variation > 1
            ? "\nIni cerita variasi ke-{$variation}. WAJIB buat cerita yang BERBEDA dari variasi sebelumnya: beda judul, beda konflik, beda karakter sampingan, beda ending."
            : '';

        $context = "Tema: {$themeInput}\n";
        if (!empty($skill)) $context .= "Skill/Nilai: Cerita HARUS mengajarkan tentang \"{$skill}\" secara natural\n";
        if (!empty($desc)) $context .= "Rencana cerita: {$desc}\n";
        if (!empty($informasi)) $context .= "Fakta: {$informasi}\n";
        if (!empty($notes)) $context .= "Catatan: {$notes}\n";
        if (!empty($agama)) $context .= "Agama: {$agama}\n";
        $context .= $variationHint;

        $systemPrompt = <<<PROMPT
Kamu menulis cerita anak Indonesia.

WAJIB: Output HARUS TEPAT {$pagesCount} halaman dalam array "pages". Jangan kurang, jangan lebih.
Jika diminta 9 halaman, maka "pages" harus berisi TEPAT 9 item.

ATURAN KETAT:
- WAJIB gunakan Bahasa Indonesia saja, TIDAK BOLEH bahasa lain (Cina, Inggris, Jepang, dll)
- TIDAK BOLEH gunakan karakter non-Indonesia (huruf Cina, Jepang, Arab, dll)
- HANYA gunakan huruf Latin A-Z dan angka
- Bahasa Indonesia sederhana, anak usia {$minAge}-{$maxAge} tahun
- Setiap halaman: 2-10 kalimat, MAKSIMAL 40 kata
- Jangan gunakan kata sulit atau bahasa asing
- Cerita HARUS mengikuti tema yang diberikan, termasuk karakter dan alur yang disebutkan
- Jika tema menyebut nama karakter (misal: faqih), GUNAKAN nama itu sebagai tokoh utama
- Jika tema menyebut situasi (misal: gagal mancing, pantang menyerah), JADIKAN itu alur cerita
- Cerita harus menarik, punya alur jelas, banyak tempat berbeda
- Cerita harus menghibur terlebih dahulu, mengajarkan kemudian
- Jangan terasa seperti ceramah
- Moral muncul dari pengalaman tokoh
- Konflik harus alami dan mudah dipahami anak
- Ending hangat dan memuaskan
- buat moral yang di mengerti anak anak, dan jangan cuma sedikit
- Jika ada Skill/Nilai, cerita HARUS mengajarkan nilai tersebut secara natural melalui alur cerita

OUTPUT JSON:
{"title":"Judul","desc":"Deskripsi singkat","moral":"Pelajaran","pages":[{"text":"cerita 1"},{"text":"cerita 2"}]}

Ingat: "pages" HARUS berisi TEPAT {$pagesCount} item!
HANYA output JSON, tidak ada teks lain.
PROMPT;

        $userPrompt = "Buatkan cerita anak tentang: {$themeInput}\n\n{$context}\n\nPENTING: Output HARUS TEPAT {$pagesCount} halaman dalam array pages!";

        $result = $this->callAi($ai, $provider, $model, $systemPrompt, $userPrompt, $pagesCount);
        Log::info($result);

        if ($result) {
            return [
                'title' => $this->cleanText($result['title'] ?? $theme),
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
        for ($attempt = 0; $attempt < 3; $attempt++) {
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

                $got = count($renumbered);
                if ($got < 3) {
                    continue;
                }

                if ($got < $pagesCount) {
                    Log::info("callAi: got {$got}/{$pagesCount} pages, attempt {$attempt}");
                    if ($attempt < 2) continue;
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
            $pages[] = ['num' => $index + 1, 'text' => $page['text'] ?? ''];
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
        return preg_replace('/[^\x00-\x7F]/u', '', $text);
    }
}
