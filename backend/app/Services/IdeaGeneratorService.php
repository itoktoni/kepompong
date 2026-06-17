<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IdeaGeneratorService
{
    private array $templates = [
        'permainan_edukasi' => [
            'title' => 'Permainan Edukasi Seru',
            'items' => [
                ['name' => 'Tebak Kata dari Gambar', 'desc' => 'Guru menunjukkan gambar, anak menebak kata yang sesuai dengan menyebutkan suku kata atau huruf awal.', 'moral' => 'Melatih daya ingat dan keberanian menjawab'],
                ['name' => 'Hitung Benda Sekitar', 'desc' => 'Anak mencari dan menghitung benda-benda di sekeliling dengan jumlah tertentu, misalnya 5 pensil atau 3 buku.', 'moral' => 'Belajar berhitung dengan cara menyenangkan'],
                ['name' => 'Susun Huruf Menjadi Kata', 'desc' => 'Huruf-huruf acak disusun menjadi kata yang bermakna, seperti H-U-F-A-Z menjadi HUFAZ.', 'moral' => 'Melatih kemampuan membaca dan kosakata'],
                ['name' => 'Warna dan Bentuk', 'desc' => 'Anak mencocokkan benda dengan warna dan bentuk yang sama, seperti balok merah dengan balok merah.', 'moral' => 'Mengenali warna dan bentuk dengan cepat'],
                ['name' => 'Bercerita dengan Gambar', 'desc' => 'Anak melihat urutan gambar dan menyusunnya menjadi cerita yang logis dan runtut.', 'moral' => 'Melatih berpikir sistematis dan kreatif'],
                ['name' => 'Permainan Simon Says', 'desc' => 'Mengikuti perintah yang dimulai dengan "Simon Says", melatih daya listen dan ketangkasan.', 'moral' => 'Melatih konsentrasi dan mendengar dengan baik'],
                ['name' => 'Teka-Teki Lucu', 'desc' => 'Anak menjawab teka-teki sederhana yang jawabannya menggelitik lógica dan membuat tertawa.', 'moral' => 'Berpikir kritis dan memahami hubungan sebab-akibat'],
                ['name' => 'Bernyanyi Sambil Belajar', 'desc' => 'Lagu-lagu edukasi yang mengajarkan angka, huruf, atau kosakata baru dengan melodi yang catchy.', 'moral' => 'Belajar melalui musik meningkatkan daya ingat'],
            ],
        ],
        'permainan_kerjasama' => [
            'title' => 'Permainan Kerja Sama Tim',
            'items' => [
                ['name' => 'Membangun Tower dari Balok', 'desc' => 'Tim berusaha membangun tower tertinggi dari balok dalam waktu tertentu tanpa menjatuhkan.', 'moral' => 'Kerja sama tim menghasilkan sesuatu yang lebih baik'],
                ['name' => 'Lari Estafet Berantai', 'desc' => 'Ti-tim berlari membawa tongkat dan передавая ke rekan satu tim dengan cepat dan tepat.', 'moral' => 'Setiap peran penting dalam keberhasilan tim'],
                ['name' => 'Permainan Kelereng dalam Gelas', 'desc' => 'Tim bekerja sama memindahkan kelereng dari satu容器 ke容器 lain hanya dengan sedotan.', 'moral' => 'Kesabaran dan koordinasi tim sangat penting'],
                ['name' => 'Mencari Harta Karun', 'desc' => 'Tim mencari petunjuk yang Tersebar di area bermain dan bekerja sama memecahkan teka-teki.', 'moral' => 'Berpikir bersama lebih мощный dari pada sendiri'],
                ['name' => 'Gambar Bersama dengan Mata Tertutup', 'desc' => 'Satu anak menggambar sementara yang lain memberikan instruksi lisan tanpa melihat hasil.', 'moral' => 'Komunikasi yang jelas penting untuk успех'],
                ['name' => 'Permainan Piramida Kertas', 'desc' => 'Tim membuat piramida dari kertas yang dilipat bersama-sama dengan presisi tinggi.', 'moral' => 'Detail kecil sangat влияет pada hasil akhir'],
                ['name' => 'Balon dan Sedotan', 'desc' => 'Tim menjaga balon tetap di udara dengan bekerja sama menggunakan bagian tubuh yang berbeda.', 'moral' => 'Semua anggota tim memiliki-contribution yang berarti'],
                ['name' => 'Orchestra Alat Musik Sederhana', 'desc' => 'Setiap anak играет alat musik sederhana dan harus coordinated untuk memainkan lagu bersama.', 'moral' => 'Keselarasan dan saling mendengarkan menghasilkan harmoni'],
            ],
        ],
        'permainan_aktif' => [
            'title' => 'Permainan Aktif dan Gerak',
            'items' => [
                ['name' => 'Tangkap Ekor', 'desc' => 'Setiap anak memiliki "ekor" ( kain ), harus merebut ekor anak lain tanpa kehilangan miliknya.', 'moral' => 'Strategi dan kelincahan sangat penting'],
                ['name' => 'Petak Umpet', 'desc' => 'Satu anak menghitung sementara yang lain bersembunyi, lalu mencarinya satu per satu.', 'moral' => 'Bersembunyi dengan cerdas dan tahu waktu yang tepat untuk muncul'],
                ['name' => 'Lompat Tali', 'desc' => 'Melompati tali yang diayunkan oleh dua anak lain, melatih координация dan ritme.', 'moral' => 'Ketekunan делает everything возможным'],
                ['name' => 'Gobak Sodor', 'desc' => 'Dua tim berusaha melewati garis lawan tanpa tertangkap, игрывает strategi dan kecepatan.', 'moral' => 'Setiap langkah harus dipikirkan dengan matang'],
                ['name' => 'Bola Basket Mini', 'desc' => 'Anak-anak melempar bola ke keranjang rendah dengan jarak yang sudah ditentukan.', 'moral' => 'Latihan terus-menerus menghasilkan perbaikan'],
                ['name' => 'Tari Kelompok', 'desc' => 'Anak-anak menari bersama dengan музыка dan координация gerakan yang seragam.', 'moral' => 'Bersatu dalam perbedaan menciptakan keindahan'],
                ['name' => 'Balap Karung', 'desc' => 'Anak melompat di dalam karung menuju garis finish, latihan keseimbangan dan kekuatan.', 'moral' => 'Tidak boleh menyerah meski terlihat sulit'],
                ['name' => 'Permainan Engklek', 'desc' => 'Melompati petak-petak yang digambar di tanah, melatih ловкость dan konsentrasi.', 'moral' => 'Aturan harus dipatuhi untuk fair play'],
            ],
        ],
    ];

    public function generate(string $type = 'permainan_edukasi'): array
    {
        $type = strtolower(trim($type));

        $template = $this->templates[$type] ?? $this->buildFallback($type);

        return [
            'title' => $template['title'],
            'items' => $template['items'],
        ];
    }

    private function buildFallback(string $type): array
    {
        return [
            'title' => 'Ide Permainan untuk Anak',
            'items' => [
                ['name' => 'Permainan Kartu Memori', 'desc' => 'Mencocokkan kartu dengan gambar yang sama, melatih daya ingat anak.', 'moral' => 'Konsentrasi dan kesabaran membawa hasil'],
                ['name' => 'Puzzle Sederhana', 'desc' => 'Menyusun kepingan puzzle menjadi gambar utuh, melatih координация mata-tangan.', 'moral' => 'Setiap bagian penting dalam keseluruhan'],
                ['name' => 'Mewarnai Bersama', 'desc' => 'Anak-anak mewarnai gambar bersama-sama dengan tema tertentu.', 'moral' => 'Kreativitas tidak memiliki batas'],
                ['name' => 'Bermain Clay atau Playdough', 'desc' => 'Membentuk berbagai bentuk dari clay, melatih motorik halus anak.', 'moral' => 'Imajinasi dapat diwujudkan dalam bentuk nyata'],
                ['name' => 'Permainan Cermin', 'desc' => 'Satu anak membuat gerakan, yang lain mengikuti как cermin.', 'moral' => 'Mengamati dan meniru dengan baik adalah keterampilan'],
                ['name' => 'Cerita Berantai', 'desc' => 'Setiap anak добавляет kalimat dalam cerita secara bergiliran.', 'moral' => 'Setiap kontribusi memiliki nilai dalam bersosialisasi'],
                ['name' => 'Mengelompokkan Benda', 'desc' => 'Anak mengelompokkan benda berdasarkan warna, bentuk, atau ukuran.', 'moral' => 'Klasifikasi membantu memahami dunia sekitar'],
                ['name' => 'Permainan Sorotan Senter', 'desc' => 'Dalam ruangan gelap, senter dinyalakan dan anak harus menyentuh benda yang disorot.', 'moral' => 'Reaksi cepat dan tepat sangat berharga'],
            ],
        ];
    }

    public function generateWithAI(string $type = 'permainan_edukasi', int $count = 8, array $ages = [], ?string $agama = null, array $skills = []): array
    {
        $client = $this->openAiClient();
        $type = strtolower(trim($type));
        $count = max(1, min(20, $count));
        $minAge = !empty($ages) ? min($ages) : 3;
        $maxAge = !empty($ages) ? max($ages) : 8;

        $typeGuide = match ($type) {
            'permainan_kerjasama' => "Generate cooperative and team-building games that require children to work together, communicate, and support each other. Focus on activities that teach teamwork, collaboration, and mutual support.",
            'permainan_aktif' => "Generate active physical games that get children moving, running, jumping, or dancing. Focus on activities that promote physical health, coordination, and gross motor skills.",
            default => "Generate educational games that combine learning with fun. Focus on cognitive development, literacy, numeracy, or skill-building activities that are age-appropriate.",
        };

        $ageGuide = match (true) {
            $maxAge <= 3 => "Target: toddlers ages 1-3. Use VERY SIMPLE activities with basic motor skills. Focus on sensory play, simple imitation, and basic coordination.",
            $maxAge <= 6 => "Target: young children ages 4-6. Use simple rules, short duration activities. Focus on basic social skills, colors, shapes, numbers, and simple physical activities.",
            default => "Target: older children ages 7-10. Use more complex rules, strategy elements, and longer duration. Focus on critical thinking, teamwork, and skill development.",
        };

        $systemPrompt = "You are a children's activity and game designer for Indonesian preschool and elementary school children.\n";
        $systemPrompt .= "CRITICAL: You MUST create EXACTLY {$count} game/activity ideas. Not {$count} minus 1, not {$count} plus 1. EXACTLY {$count} ideas.\n";
        $systemPrompt .= "CRITICAL: Use ONLY Indonesian language with Latin alphabet. NEVER use Chinese, Arabic, Japanese, Korean, or any non-Latin characters. No emojis.\n";
        $systemPrompt .= "{$typeGuide}\n";
        $systemPrompt .= "{$ageGuide}\n";
        $systemPrompt .= "Return ONLY JSON with this structure:\n";
        $systemPrompt .= "{\"title\":\"...\",\"items\":[{\"name\":\"...\",\"desc\":\"...\",\"moral\":\"...\"},...up to EXACTLY {$count} items]}\n";
        $systemPrompt .= "- title: a catchy Indonesian title for this collection of games (max 50 characters)\n";
        $systemPrompt .= "- items: array of EXACTLY {$count} game ideas\n";
        $systemPrompt .= "- Each item must have:\n";
        $systemPrompt .= "  - name: game title/name in Indonesian (max 40 characters)\n";
        $systemPrompt .= "  - desc: brief description of how to play (max 100 characters)\n";
        $systemPrompt .= "  - moral: moral or lesson learned from the game (max 60 characters)\n";
        $systemPrompt .= "- Age range: {$minAge}-{$maxAge} years old\n";

        if ($agama) {
            $agamaGuide = match ($agama) {
                'islam' => "Content must be appropriate for Muslim children. Include Islamic values like kindness, sharing, honesty. Avoid content that contradicts Islamic teachings.",
                'kristen' => "Content must be appropriate for Christian children. Include Christian values like love, kindness, honesty. Avoid content that contradicts Christian teachings.",
                'katholik' => "Content must be appropriate for Catholic children. Include Catholic values like love, kindness, honesty. Avoid content that contradicts Catholic teachings.",
                'hindu' => "Content must be appropriate for Hindu children. Include Hindu values like dharma, kindness, respect. Avoid content that contradicts Hindu teachings.",
                'budha' => "Content must be appropriate for Buddhist children. Include Buddhist values like compassion, mindfulness, kindness. Avoid content that contradicts Buddhist teachings.",
                default => "Content must be culturally appropriate for Indonesian children.",
            };
            $systemPrompt .= "- Religion: {$agama}\n";
            $systemPrompt .= "{$agamaGuide}\n";
        }

        if (!empty($skills)) {
            $skillsList = implode(', ', $skills);
            $systemPrompt .= "- Skills to focus on: {$skillsList}\n";
            $systemPrompt .= "- Each game should help develop at least one of these skills: {$skillsList}\n";
        }

        $systemPrompt .= "CRITICAL: This content is for CHILDREN ages {$minAge}-{$maxAge}. You MUST use ONLY safe, kind, positive language. Never include any dangerous, violent, or inappropriate content.\n";
        $systemPrompt .= "CRITICAL: Each game description must be practical and can be played with simple materials found in daily life.\n";

        try {
            $response = $client->post('/chat/completions', [
                'model' => config('services.openai.model', 'MiniMax-M2.7-highspeed'),
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => 'Buatkan ide permainan untuk anak-anak dengan tema: ' . str_replace('_', ' ', $type)],
                ],
                'temperature' => 0.8,
                'max_tokens' => max(4000, $count * 400),
            ]);

            if (! $response->successful()) {
                Log::warning('Idea AI failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'type' => $type,
                ]);
                return $this->fallback($type, $count);
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            if (empty($content)) {
                Log::warning('Idea AI empty content', [
                    'data' => $data,
                    'type' => $type,
                ]);
                return $this->fallback($type, $count);
            }

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
            $content = preg_replace('/\s*```+\s*$/i', '', $content);
            $content = trim($content);

            $parsed = json_decode($content, true);
            if (! is_array($parsed) || empty($parsed['title']) || empty($parsed['items'])) {
                Log::warning('Idea AI invalid response', [
                    'content' => $content,
                    'type' => $type,
                    'count_requested' => $count,
                ]);
                return $this->fallback($type, $count);
            }

            $items = array_slice($parsed['items'], 0, $count);
            $cleanedItems = [];
            foreach ($items as $index => $item) {
                $cleanedItems[] = [
                    'num' => $index + 1,
                    'name' => $this->cleanText($item['name'] ?? ''),
                    'desc' => $this->cleanText($item['desc'] ?? ''),
                    'moral' => $this->cleanText($item['moral'] ?? ''),
                ];
            }

            return [
                'title' => $this->cleanText($parsed['title']),
                'items' => $cleanedItems,
                'source' => 'ai',
            ];
        } catch (\Throwable $e) {
            Log::warning('Idea AI exception', [
                'message' => $e->getMessage(),
                'type' => $type,
            ]);
            return $this->fallback($type, $count);
        }
    }

    private function fallback(string $type, int $count = 8): array
    {
        $base = $this->generate($type);
        $base['items'] = array_slice($base['items'], 0, $count);
        foreach ($base['items'] as $index => &$item) {
            $item['num'] = $index + 1;
        }
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
