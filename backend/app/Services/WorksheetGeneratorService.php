<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorksheetGeneratorService
{
    private array $templates = [
        'matematika' => [
            'title' => 'Latihan Matematika Menyenangkan',
            'items' => [
                ['text' => 'Penjumlahan angka 1-10: soal latihan dasar penjumlahan untuk anak.'],
                ['text' => 'Pengurangan angka 1-10: latihan pengurangan dengan gambar pendukung.'],
                ['text' => 'Mencocokkan angka dengan jumlah benda: latihan pengenalan angka.'],
                ['text' => 'Mewarnai angka yang sesuai: kombinasi belajar dan bermain.'],
                ['text' => 'Soal cerita matematika sederhana: aplikasi matematika dalam kehidupan.'],
                ['text' => 'Pengenalan bangun datar: lingkaran, segitiga, persegi.'],
                ['text' => 'Latihan menghitung maju dan mundur: 1-20.'],
                ['text' => 'Penjumlahan dengan menyimpan: soal tingkat menengah.'],
            ],
        ],
        'bahasa' => [
            'title' => 'Belajar Bahasa Indonesia',
            'items' => [
                ['text' => 'Mencocokkan huruf besar dan kecil: Aa, Bb, Cc, Dd.'],
                ['text' => 'Menebalkan huruf: latihan menulis huruf ABC.'],
                ['text' => 'Mewarnai huruf: belajar huruf melalui permainan.'],
                ['text' => 'Menyusun suku kata menjadi kata: latihan membaca.'],
                ['text' => 'Mencocokkan gambar dengan kata: kosakata baru.'],
                ['text' => 'Menulis kata dari gambar: latihan mengeja.'],
                ['text' => 'Mengisi titik-titik: kalimat sederhana.'],
                ['text' => 'Membaca dan memahami cerita pendek.'],
            ],
        ],
        'sains' => [
            'title' => 'Petualangan Sains untuk Anak',
            'items' => [
                ['text' => 'Bagian tubuh manusia: belajar anatomi dasar.'],
                ['text' => 'Siklus hidup kupu-kupu: metamorfosis lengkap.'],
                ['text' => 'Planets of the solar system: tata surya untuk anak.'],
                ['text' => 'Types of plants and their parts: akar, batang, daun, bunga.'],
                ['text' => 'Properties of water: padat, cair, gas.'],
                ['text' => 'Weather and seasons: cuaca dan musim.'],
                ['text' => 'Magnets and their properties: magnet dan gaya tarik.'],
                ['text' => 'Life cycle of a plant: dari biji hingga tumbuh.'],
            ],
        ],
    ];

    public function generate(string $topic, ?string $subtopic = null, string $childName = 'Anak', int $pagesCount = 8, string $type = 'practice'): array
    {
        $topic = strtolower(trim($topic));
        $subtopic = $subtopic ? strtolower(trim($subtopic)) : null;
        $childName = trim($childName) ?: 'Anak';

        $template = $this->templates[$topic] ?? $this->buildFallback($topic, $subtopic);

        $title = $template['title'];
        if ($subtopic) {
            $title = 'Latihan ' . ucfirst($subtopic) . ' - ' . ucfirst($topic);
        }

        $items = [];
        $num = 1;
        foreach ($template['items'] as $item) {
            $text = str_replace('{nama}', $childName, $item['text']);
            $items[] = [
                'num' => $num++,
                'text' => $text,
            ];
        }

        return [
            'title' => $title,
            'desc' => 'Lembar kerja ' . $type . ' tentang ' . ($subtopic ?: $topic),
            'items' => $items,
        ];
    }

    private function buildFallback(string $topic, ?string $subtopic): array
    {
        $title = 'Lembar Kerja ' . ucfirst($topic);
        if ($subtopic) {
            $title .= ' - ' . ucfirst($subtopic);
        }

        $items = $this->getWorksheetItems($topic, $subtopic);

        return [
            'title' => $title,
            'items' => $items,
        ];
    }

    private function getWorksheetItems(string $topic, ?string $subtopic): array
    {
        $items = [
            'matematika' => [
                'penjumlahan' => [
                    ['text' => 'Latihan penjumlahan 1-5 dengan gambar.'],
                    ['text' => 'Latihan penjumlahan 6-10 dengan angka.'],
                    ['text' => 'Penjumlahan ganda: 1+1, 2+2, 3+3.'],
                    ['text' => 'Soal cerita penjumlahan sederhana.'],
                    ['text' => 'Mewarnai hasil penjumlahan yang benar.'],
                    ['text' => 'Menyelesaikan soal penjumlahan bersusun.'],
                    ['text' => 'Latihan cepat menghitung jumlah benda.'],
                    ['text' => 'Penjumlahan dengan 3 angka.'],
                ],
                'pengurangan' => [
                    ['text' => 'Latihan pengurangan 1-5 dengan gambar.'],
                    ['text' => 'Latihan pengurangan 6-10 dengan angka.'],
                    ['text' => 'Pengurangan ganda: 4-2, 6-3, 8-4.'],
                    ['text' => 'Soal cerita pengurangan sederhana.'],
                    ['text' => 'Mencoret gambar sesuai angka pengurangan.'],
                    ['text' => 'Menyelesaikan soal pengurangan bersusun.'],
                    ['text' => 'Latihan pengurangan dengan bilangan lebih besar.'],
                    ['text' => 'Soal campuran penjumlahan dan pengurangan.'],
                ],
                'perkalian' => [
                    ['text' => 'Perkalian 1-5: latihan dasar perkalian.'],
                    ['text' => 'Perkalian 6-10: latihan lanjutan.'],
                    ['text' => 'Tabel perkalian 1-10 lengkap.'],
                    ['text' => 'Perkalian dengan gambar: konsep perkalian.'],
                    ['text' => 'Soal cerita perkalian sederhana.'],
                    ['text' => 'Latihan perkalian cepat.'],
                    ['text' => 'Perkalian 2 angka dengan 1 angka.'],
                    ['text' => 'Soal campuran: perkalian dan pembagian.'],
                ],
                'pembagian' => [
                    ['text' => 'Pembagian 2-5: latihan dasar pembagian.'],
                    ['text' => 'Pembagian 6-10: latihan lanjutan.'],
                    ['text' => 'Konsep pembagian dengan gambar.'],
                    ['text' => 'Soal cerita pembagian sederhana.'],
                    ['text' => 'Latihan pembagian dengan sisa.'],
                    ['text' => 'Pembagian bersusun pendek.'],
                    ['text' => 'Soal campuran: perkalian dan pembagian.'],
                    ['text' => 'Latihan pembagian cepat.'],
                ],
                'angka' => [
                    ['text' => 'Menulis angka 1-10 dengan benar.'],
                    ['text' => 'Mencocokkan angka dengan jumlah benda.'],
                    ['text' => 'Berhitung maju dan mundur 1-20.'],
                    ['text' => 'Mengurutkan angka dari kecil ke besar.'],
                    ['text' => 'Mengurutkan angka dari besar ke kecil.'],
                    ['text' => 'Menebalkan angka dengan bentuk yang benar.'],
                    ['text' => 'Latihan menulis angka dalam kata.'],
                    ['text' => 'Membandingkan jumlah benda dengan tanda >, <, =.'],
                ],
            ],
            'bahasa' => [
                'huruf' => [
                    ['text' => 'Menebalkan huruf A-Z: latihan menulis.'],
                    ['text' => 'Mencocokkan huruf besar dan kecil.'],
                    ['text' => 'Mewarnai huruf sesuai warna.'],
                    ['text' => 'Menulis huruf yang hilang.'],
                    ['text' => 'Mengurutkan huruf membentuk kata.'],
                    ['text' => 'Latihan huruf vokal A, I, U, E, O.'],
                    ['text' => 'Latihan huruf konsonan.'],
                    ['text' => 'Mencocokkan gambar dengan huruf awal.'],
                ],
                'kata' => [
                    ['text' => 'Menulis nama benda di kelas.'],
                    ['text' => 'Mencocokkan gambar dengan kata.'],
                    ['text' => 'Menyusun huruf menjadi kata.'],
                    ['text' => 'Menulis kata yang tepat.'],
                    ['text' => 'Latihan kosakata baru setiap hari.'],
                    ['text' => 'Mengisi huruf yang hilang.'],
                    ['text' => 'Menulis kalimat sederhana.'],
                    ['text' => 'Membaca dan memahami kata.'],
                ],
                'kalimat' => [
                    ['text' => 'Menulis kalimat dari kata yang tersedia.'],
                    ['text' => 'Melengkapi kalimat dengan kata yang tepat.'],
                    ['text' => 'Membaca cerita pendek dan menjawab pertanyaan.'],
                    ['text' => 'Menulis kalimat sendiri tentang gambar.'],
                    ['text' => 'Mengurutkan kata menjadi kalimat.'],
                    ['text' => 'Latihan tanda baca titik dan koma.'],
                    ['text' => 'Menulis surat pendek untuk teman.'],
                    ['text' => 'Membuat kalimat dari gambar.'],
                ],
            ],
            'sains' => [
                'tubuh' => [
                    ['text' => 'Bagian-bagian tubuh manusia.'],
                    ['text' => 'Organ dalam tubuh manusia.'],
                    ['text' => 'Panca indera dan fungsinya.'],
                    ['text' => 'Cara menjaga kebersihan tubuh.'],
                    ['text' => 'Makanan sehat untuk tubuh.'],
                    ['text' => 'Olahraga untuk kesehatan.'],
                    ['text' => 'Pertumbuhan dan perkembangan manusia.'],
                    ['text' => 'Perbedaan anak laki-laki dan perempuan.'],
                ],
                'tumbuhan' => [
                    ['text' => 'Bagian-bagian tanaman: akar, batang, daun.'],
                    ['text' => 'Proses pertumbuhan tanaman.'],
                    ['text' => 'Jenis-jenis daun dan bunga.'],
                    ['text' => 'Kebutuhan tanaman: air, sunlight, tanah.'],
                    ['text' => 'Buah dan sayuran: dari mana asalnya.'],
                    ['text' => 'Lingkaran hidup tanaman.'],
                    ['text' => 'Tanaman yang bisa dimakan.'],
                    ['text' => 'Tanaman di sekitar kita.'],
                ],
            ],
        ];

        if ($subtopic && isset($items[$topic][$subtopic])) {
            return $items[$topic][$subtopic];
        }

        return [
            ['text' => 'Lembar kerja latihan 1 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 2 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 3 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 4 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 5 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 6 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 7 tentang ' . $topic],
            ['text' => 'Lembar kerja latihan 8 tentang ' . $topic],
        ];
    }

    public function generateWithAI(string $topic, ?string $subtopic, string $childName = 'Anak', int $pagesCount = 8, string $type = 'practice', array $grades = []): array
    {
        $client = $this->openAiClient();

        $pagesCount = max(1, min(24, $pagesCount));
        $grade = !empty($grades) ? min($grades) : 1;
        $topic = strtolower(trim($topic));
        $subtopic = $subtopic ? strtolower(trim($subtopic)) : null;

        $gradeGuide = match (true) {
            $grade <= 1 => "Target: Grade 1 (ages 6-7). Use very simple vocabulary, large fonts, clear images. Focus on basic recognition and matching exercises. Short instructions (3-5 words).",
            $grade <= 3 => "Target: Grade 2-3 (ages 7-9). Use simple vocabulary, moderate complexity. Focus on reading, writing simple words, and basic math. Medium-length instructions.",
            default => "Target: Grade 4+ (ages 9-10). Use complex vocabulary, detailed exercises. Focus on comprehension, problem-solving, and advanced concepts. Longer instructions.",
        };

        $typeGuide = match (strtolower($type)) {
            'exam' => "Format: exam-style questions with clear instructions. Include multiple choice, fill-in-the-blank, and short answer formats.",
            'activity' => "Format: hands-on activity sheets with games, puzzles, and creative exercises. Make it fun and engaging.",
            default => "Format: practice worksheets with clear examples and exercises. Mix of guided practice and independent work.",
        };

        $subjectFocus = $subtopic ? "Topic: {$topic} - Subtopic: {$subtopic}" : "Topic: {$topic}";

        $systemPrompt = "You are an educational worksheet designer for Indonesian elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} worksheet pages. Not {$pagesCount} minus 1, not {$pagesCount} plus 1. EXACTLY {$pagesCount} worksheets.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$gradeGuide}\n";
        $systemPrompt .= "{$typeGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},{\"text\":\"...\"},...up to EXACTLY {$pagesCount} items]}\n";
        $systemPrompt .= "- title: a catchy Indonesian title for this worksheet collection (max 60 characters)\n";
        $systemPrompt .= "- desc: brief description of what this worksheet covers\n";
        $systemPrompt .= "- items: array of EXACTLY {$pagesCount} worksheet page descriptions\n";
        $systemPrompt .= "- Each item text should be MAXIMUM 35 words describing what the worksheet page contains.\n";
        $systemPrompt .= "- {$subjectFocus}\n";
        $systemPrompt .= "- Grade level: {$grade}\n";
        $systemPrompt .= "- Worksheet type: {$type}\n";
        $systemPrompt .= "- Include varied exercise types: matching, fill-in-blank, drawing, counting, writing, etc.\n";
        $systemPrompt .= "CRITICAL: All text must be in proper Indonesian language suitable for Indonesian school children.\n";
        $systemPrompt .= "CRITICAL: This content is for CHILDREN. You MUST use ONLY safe, kind, positive language. Never include any inappropriate content.\n";
        $systemPrompt .= "CRITICAL: Use ONLY simple Indonesian words that children can understand. FORBIDDEN words: colorful, continental, shelf, submarine, misteriosa, magnificent, spectacular, extraordinary, brilliant, gorgeous, elegant, sophisticated, mysterious, enchanting, mesmerizing, breathtaking, astonishing, phenomenal, remarkable, and ANY other complex/foreign words. Use simple words like: cantik, bagus, seru, lucu, menarik, menyenangkan, hebat, luar biasa, keren, asyik.\n";

        $userContent = 'Buatkan lembar kerja untuk topik: ' . $topic;
        if ($subtopic) {
            $userContent .= ' dengan subtopik: ' . $subtopic;
        }
        $userContent .= '. Tipe: ' . $type . '. Grade: ' . $grade;

        try {
            $response = $client->post('/chat/completions', [
                'model' => config('services.openai.model', 'MiniMax-M2.7-highspeed'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userContent],
                ],
                'temperature' => 0.7,
                'max_tokens' => max(5000, $pagesCount * 350),
            ]);

            if (! $response->successful()) {
                Log::warning('Worksheet AI failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'topic' => $topic,
                    'subtopic' => $subtopic,
                ]);
                return $this->fallback($topic, $subtopic, $childName, $pagesCount, $type);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                Log::warning('Worksheet AI empty content', [
                    'data' => $data,
                    'topic' => $topic,
                    'subtopic' => $subtopic,
                ]);
                return $this->fallback($topic, $subtopic, $childName, $pagesCount, $type);
            }

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['items'])) {
                Log::warning('Worksheet AI invalid response', [
                    'content' => $content,
                    'topic' => $topic,
                    'subtopic' => $subtopic,
                    'pages_requested' => $pagesCount,
                ]);
                return $this->fallback($topic, $subtopic, $childName, $pagesCount, $type);
            }

            $items = array_slice($parsed['items'], 0, $pagesCount);
            $renumbered = [];
            foreach ($items as $index => $item) {
                $renumbered[] = [
                    'num' => $index + 1,
                    'text' => $this->cleanText($item['text'] ?? (is_string($item) ? $item : '')),
                ];
            }

            return [
                'title' => $this->cleanText($parsed['title']),
                'desc' => $this->cleanText($parsed['desc'] ?? ''),
                'items' => $renumbered,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            Log::warning('Worksheet AI exception', [
                'message' => $e->getMessage(),
                'topic' => $topic,
                'subtopic' => $subtopic,
            ]);
            return $this->fallback($topic, $subtopic, $childName, $pagesCount, $type);
        }
    }

    private function fallback(string $topic, ?string $subtopic, string $childName, int $pagesCount = 8, string $type = 'practice'): array
    {
        $base = $this->generate($topic, $subtopic, $childName, $pagesCount, $type);
        $base['items'] = array_slice($base['items'], 0, $pagesCount);
        return $base;
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/[^\x00-\x7F]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function openAiClient(): PendingRequest
    {
        $baseUrl = rtrim((string) config('services.openai.base_url', env('OPENAI_BASE_URL', 'https://api.openai.com')), '/');
        $apiKey = (string) config('services.openai.api_key', env('OPENAI_API_KEY', ''));

        return Http::withToken($apiKey)
            ->asJson()
            ->baseUrl($baseUrl)
            ->timeout(60)
            ->retry(2, 500)
            ->acceptJson();
    }
}
