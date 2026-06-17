<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ColoringGeneratorService
{
    private array $templates = [
        'hewan' => [
            'title' => 'Mewarnai Hewan Laut',
            'items' => [
                ['text' => 'Ikan cupang berwarna cerah berenang di akuarium.'],
                ['text' => 'Kura-kura laut sedang berenang di lautan biru.'],
                ['text' => 'Udang mantis dengan warna cerah dan menarik.'],
                ['text' => 'Lumba-lumba meloncat dari permukaan air.'],
                ['text' => 'Kuda laut kecil di antara rumput laut hijau.'],
                ['text' => 'Kepiting berwarna oranye berjalan di pasir.'],
                ['text' => 'Hiu putih besar dengan gigi-gigi tajam.'],
                ['text' => 'Pari manta berenang dengan anggun di laut.'],
                ['text' => 'Gurita dengan lengan-lengan panjang dan lentur.'],
                ['text' => 'Belut moray menyembulkan kepalanya dari karang.'],
                ['text' => 'Penyu hijau sedang makan rumput laut.'],
                ['text' => 'Lobster merah cerah dengan capit yang besar.'],
            ],
        ],
        'buah' => [
            'title' => 'Mewarnai Buah-Buahan Segar',
            'items' => [
                ['text' => 'Apel merah yang matang dan segar di pohon.'],
                ['text' => 'Pisang kuning panjang dan manis.'],
                ['text' => 'Jeruk oranye cerah dengan tetesan air.'],
                ['text' => 'Anggur ungu dalam tandan yang indah.'],
                ['text' => 'Stroberi merah dengan biji-biji kecil.'],
                ['text' => 'Semangka merah juicy dengan bijinya.'],
                ['text' => 'Mangga kuning yang matang dan manis.'],
                ['text' => 'Nanas dengan mahkota daun hijau.'],
                ['text' => 'Cherry merah cerah tergantung di dahan.'],
                ['text' => 'Pepaya oranye dengan buah di dalamnya.'],
                ['text' => 'Rambutan merah berduri di tangkai.'],
                ['text' => 'Durian dengan duri-duri kuning di luar.'],
            ],
        ],
        'kendaraan' => [
            'title' => 'Mewarnai Kendaraan Seru',
            'items' => [
                ['text' => 'Mobil sport merah dengan roda yang keren.'],
                ['text' => 'Sepeda motor tracak dengan warna biru.'],
                ['text' => 'Truk derek berwarna kuning dan hitam.'],
                ['text' => 'Helikopter merah putih di bandara.'],
                ['text' => 'Kapal pesiar besar di lautan biru.'],
                ['text' => 'Kereta api cepat berwarna silver.'],
                ['text' => 'Bus sekolah kuning yang cerah.'],
                ['text' => 'Mobil pemadam kebakaran merah menyala.'],
                ['text' => 'Mobil ambulance putih dengan tanda silang.'],
                ['text' => 'Pesawat jumbo jet di landasan pacu.'],
                ['text' => 'Kapal perang di laut yang biru.'],
                ['text' => 'Motorboat racing dengan warna neon.'],
            ],
        ],
    ];

    public function generate(string $subject, string $childName = 'Anak'): array
    {
        $subject = strtolower(trim($subject));
        $childName = trim($childName) ?: 'Anak';

        $template = $this->templates[$subject] ?? $this->buildFallback($subject);

        $title = str_replace('{nama}', $childName, $template['title']);

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
            'desc' => 'Koleksi halaman mewarnai tentang ' . ucfirst($subject),
            'items' => $items,
        ];
    }

    private function buildFallback(string $subject): array
    {
        $title = 'Mewarnai ' . ucfirst($subject);
        $items = [];
        $examples = $this->getExamplesForSubject($subject);

        $num = 1;
        foreach ($examples as $example) {
            $items[] = ['text' => $example];
            if ($num >= 12) break;
            $num++;
        }

        return [
            'title' => $title,
            'items' => $items,
        ];
    }

    private function getExamplesForSubject(string $subject): array
    {
        $examples = [
            'hewan' => [
                'Kucing lucu dengan mata besar yang imut.',
                'Anjing golden retriever dengan bulu lebat.',
                'Kelinci putih dengan telinga panjang.',
                'Burung merak dengan bulu ekor yang indah.',
                'Kupu-kupu berwarna-warni di taman.',
                'Koala memeluk pohon eukaliptus.',
                'Panda makan bambu di hutan.',
                'Singa jantan dengan surai yang gagah.',
                'Gajah besar dengan gading yang panjang.',
                'Zebra dengan garis-garis hitam putih.',
                'Jerapah dengan leher yang sangat panjang.',
                'Macan tutul dengan pola bulu yang unik.',
                'Penguin berjalan di atas es.',
                'Burung hantu dengan mata besar bulat.',
                'Katak hijau di atas daun teratai.',
            ],
            'buah' => [
                'Apel hijau yang segar dan renyah.',
                'Pir kuning berbentuk lonceng.',
                'Plum ungu kecil yang manis.',
                'Kiwi hijau dengan bijinya yang kecil.',
                'Blueberry biru kecil dalam排.',
                'Lemon kuning cerah berbentuk lonjong.',
                'Delima merah dengan bijinya yang banyak.',
                'Markisa oranye dengan tekstur unik.',
                'Leci berwarna merah muda.',
                'Salak cokelat dalam tandan.',
                'Sawo cokelat lembut dan manis.',
                'Belimbing kuning berbentuk bintang.',
                'Mengkudu hijau tua dengan khasiatnya.',
                'Jambu biji merah muda yang segar.',
                'Sirsak dengan duri-duri kecil.',
            ],
            'kendaraan' => [
                'Mobil sedan biru dengan jendela yang cerah.',
                'Mobil jeep hijau untuk petualangan off-road.',
                'Truk sampah berwarna hijau lingkungan.',
                'Mobil polisi biru dan putih.',
                'Motor skuter untuk kota.',
                'Perahu layar di laut.',
                'Kapal tanker minyak besar.',
                'Pesawat tempur di langit.',
                'Balon udara berwarna-warni.',
                'Skateboard dengan roda cuatro.',
                'Sepeda lipat praktis.',
                'Becak warna-warni Indonesia.',
                'Andong tradisional untuk wisata.',
                'Bemo warna oranye khas.',
                'Angkuta umum berwarna biru.',
            ],
        ];

        return $examples[$subject] ?? [
            'Gambar pertama yang menarik dan mudah.',
            'Gambar kedua dengan detail yang واضح.',
            'Gambar ketiga dengan warna yang cerah.',
            'Gambar keempat untuk latihan.',
            'Gambar kelima yang kreatif.',
            'Gambar keenam dengan bentuk simple.',
            'Gambar ketujuh dengan pola berulang.',
            'Gambar kedelapan dengan detail sedang.',
            'Gambar kesembilan yang lucu.',
            'Gambar kesepuluh yang seru.',
            'Gambar kesebelas dengan bentuk besar.',
            'Gambar keduabelas untuk koleksi.',
        ];
    }

    public function generateWithAI(string $subject, string $childName = 'Anak', int $pagesCount = 12, ?string $style = null, array $ages = []): array
    {
        $client = $this->openAiClient();

        $pagesCount = max(1, min(24, $pagesCount));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;
        $style = $style ?: 'simple';

        $ageGuide = match (true) {
            $maxAge <= 4 => "Target: young children ages 3-4. Use VERY SIMPLE outlines, large shapes, thick lines. Designs should be easy to color within boundaries. Each design should be a single object or animal with minimal details.",
            $maxAge <= 6 => "Target: children ages 5-6. Use simple to medium complexity. Clear outlines, moderate details. Can include 1-2 objects per panel. Good for developing fine motor skills.",
            default => "Target: children ages 7-10. Use medium to detailed complexity. More intricate designs, smaller details. Can include scenes with multiple elements.",
        };

        $styleGuide = match (strtolower($style)) {
            'detailed' => "Make the line art detailed with more intricate patterns and smaller elements to color.",
            'mandala' => "Use mandala-style circular patterns with geometric designs, perfect for meditation and focus while coloring.",
            default => "Keep designs simple and child-friendly with bold, clear lines.",
        };

        $systemPrompt = "You are a children's coloring page designer.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$pagesCount} coloring page designs. Not {$pagesCount} minus 1, not {$pagesCount} plus 1. EXACTLY {$pagesCount} designs.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet for any text. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "{$styleGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"desc\":\"...\",\"items\":[{\"text\":\"...\"},{\"text\":\"...\"},...up to EXACTLY {$pagesCount} items]}\n";
        $systemPrompt .= "- title: a catchy Indonesian title for this coloring collection (max 50 characters)\n";
        $systemPrompt .= "- desc: brief description of what this coloring book is about\n";
        $systemPrompt .= "- items: array of EXACTLY {$pagesCount} coloring page descriptions\n";
        $systemPrompt .= "- Each item text should be MAXIMUM 30 words describing what to draw.\n";
        $systemPrompt .= "- Subject theme: {$subject}\n";
        $systemPrompt .= "- Include variety: different {$subject} subjects, poses, or variations\n";
        $systemPrompt .= "CRITICAL: All descriptions must be for BLACK AND WHITE LINE ART ONLY. Do not mention colors in the descriptions.\n";
        $systemPrompt .= "CRITICAL: This content is for CHILDREN ages {$minAge}-{$maxAge}. You MUST use ONLY safe, kind, positive language.\n";

        try {
            $response = $client->post('/chat/completions', [
                'model' => config('services.openai.model', 'MiniMax-M2.7-highspeed'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => 'Buatkan desain halaman mewarnai tentang tema: ' . $subject],
                ],
                'temperature' => 0.8,
                'max_tokens' => max(6000, $pagesCount * 300),
            ]);

            if (! $response->successful()) {
                Log::warning('Coloring AI failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'subject' => $subject,
                ]);
                return $this->fallback($subject, $childName, $pagesCount);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                Log::warning('Coloring AI empty content', [
                    'data' => $data,
                    'subject' => $subject,
                ]);
                return $this->fallback($subject, $childName, $pagesCount);
            }

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['items'])) {
                Log::warning('Coloring AI invalid response', [
                    'content' => $content,
                    'subject' => $subject,
                    'pages_requested' => $pagesCount,
                ]);
                return $this->fallback($subject, $childName, $pagesCount);
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
            Log::warning('Coloring AI exception', [
                'message' => $e->getMessage(),
                'subject' => $subject,
            ]);
            return $this->fallback($subject, $childName, $pagesCount);
        }
    }

    private function fallback(string $subject, string $childName, int $pagesCount = 12): array
    {
        $base = $this->generate($subject, $childName);
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
