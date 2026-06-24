<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SearchImages extends Command
{
    protected $signature = 'search:images
        {id : Activity ID or slug}
        {--provider= : Image provider: unsplash or pexels (default: pexels)}
        {--cover : Only update cover image}
        {--slides : Only update slide/page images}
        {--dry-run : Show results without saving}';

    protected $description = 'Search and assign images from Unsplash/Pexels to an activity';

    private int $rateLimitDelay = 1500;

    public function handle(): int
    {
        $idOrSlug = $this->argument('id');
        $provider = $this->option('provider') ?: 'pexels';
        $dryRun = $this->option('dry-run');

        $activity = is_numeric($idOrSlug)
            ? Activity::find($idOrSlug)
            : Activity::where('slug', $idOrSlug)->first();

        if (!$activity) {
            $this->error("Activity not found: {$idOrSlug}");
            return self::FAILURE;
        }

        $data = $activity->data ?? [];
        $this->info("=== {$activity->title} (ID: {$activity->id}, Type: {$activity->type}) ===");
        $this->line("Provider: {$provider}");
        $this->newLine();

        $updated = false;

        // Cover image
        if (!$this->option('slides')) {
            $coverQuery = $this->buildCoverQuery($activity);
            if ($coverQuery) {
                $this->line("Searching cover: \"{$coverQuery}\"");
                $url = $this->searchImage($provider, $coverQuery, 'landscape');
                if ($url) {
                    $this->info("  Cover: {$url}");
                    if (!$dryRun) {
                        $activity->image = $url;
                        $updated = true;
                    }
                } else {
                    $this->warn("  Cover: no result");
                }
                $this->newLine();
            }
        }

        // Slide/page images
        if (!$this->option('cover')) {
            $slides = $data['slides'] ?? $data['pages'] ?? [];
            $isSlide = !empty($data['slides']);

            foreach ($slides as $i => $slide) {
                $query = $this->buildSlideQuery($activity, $slide, $i);
                if (!$query) continue;

                $num = $i + 1;
                $label = $slide['nama'] ?? $slide['text'] ?? "Item {$num}";
                $this->line("[{$num}] Searching: \"{$query}\" ({$label})");

                $url = $this->searchImage($provider, $query, 'squarish');
                if ($url) {
                    $this->info("  Image: {$url}");
                    if (!$dryRun) {
                        if ($isSlide) {
                            $data['slides'][$i]['image'] = $url;
                        } else {
                            $data['pages'][$i]['image'] = $url;
                        }
                        $updated = true;
                    }
                } else {
                    $this->warn("  No result");
                }

                usleep($this->rateLimitDelay * 1000);
            }
        }

        if ($updated && !$dryRun) {
            $activity->data = $data;
            $activity->save();
            $this->newLine();
            $this->info("✓ Saved to database.");
        } elseif ($dryRun) {
            $this->newLine();
            $this->comment("(Dry run — nothing saved)");
        }

        return self::SUCCESS;
    }

    private function buildCoverQuery(Activity $activity): string
    {
        $type = $activity->type;
        $title = $activity->title;

        return match ($type) {
            'mengenal_kata' => trim($title) . ' objects',
            'storytelling' => trim($title) . ' illustration',
            'komik' => trim($title) . ' comic',
            'coloring' => trim($title) . ' coloring page',
            'puzzle' => trim($title) . ' puzzle',
            default => trim($title),
        };
    }

    private function buildSlideQuery(Activity $activity, array $slide, int $index): string
    {
        $type = $activity->type;

        // mengenal_kata: search by english name
        if ($type === 'mengenal_kata') {
            $name = $slide['english'] ?? $slide['nama'] ?? '';
            return $name ? "{$name} object isolated white background" : '';
        }

        // Prefer english text for better image search results
        $english = $slide['english'] ?? null;

        // storytelling / komik: search by english panel text
        if (isset($slide['text'])) {
            $text = $english ?? $slide['text'];
            $words = array_filter(explode(' ', $text), fn($w) => strlen($w) > 3);
            $query = implode(' ', array_slice($words, 0, 5));
            return $query ? "{$query} illustration" : '';
        }

        // slides with nama (e.g. vocabulary): prefer english
        if (isset($slide['nama'])) {
            $name = $english ?? $slide['nama'];
            return "{$name} illustration";
        }

        // generic fallback
        return ($english ?? $activity->title) . ' ' . ($index + 1);
    }

    private function searchImage(string $provider, string $query, string $orientation = 'landscape'): ?string
    {
        return match ($provider) {
            'pexels' => $this->searchPexels($query, $orientation),
            default => $this->searchUnsplash($query, $orientation),
        };
    }

    private function searchUnsplash(string $query, string $orientation): ?string
    {
        $key = config('services.unsplash.access_key');
        if (!$key) {
            $this->error('UNSPLASH_ACCESS_KEY not set in .env');
            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => "Client-ID {$key}",
            'Accept-Version' => 'v1',
        ])->get('https://api.unsplash.com/search/photos', [
            'query' => $query,
            'per_page' => 3,
            'orientation' => $orientation,
            'content_filter' => 'high',
            'lang' => 'en',
        ]);

        if (!$response->successful()) {
            $this->warn("  Unsplash API error: {$response->status()}");
            return null;
        }

        $results = $response->json('results', []);
        if (empty($results)) return null;

        // Pick a random result from top 3 for variety
        $photo = $results[array_rand($results)];

        // Use small URL with crop params for consistent sizing
        $url = $photo['urls']['small'] ?? $photo['urls']['regular'] ?? null;
        if ($url && !str_contains($url, '?')) {
            $url .= '?w=400&h=300&fit=crop';
        }

        return $url;
    }

    private function searchPexels(string $query, string $orientation): ?string
    {
        $key = config('services.pexels.api_key');
        if (!$key) {
            $this->error('PEXELS_API_KEY not set in .env');
            return null;
        }

        $pexelsOrientation = match ($orientation) {
            'landscape' => 'landscape',
            'portrait' => 'portrait',
            default => 'square',
        };

        $response = Http::withHeaders([
            'Authorization' => $key,
        ])->get('https://api.pexels.com/v1/search', [
            'query' => $query,
            'per_page' => 3,
            'orientation' => $pexelsOrientation,
            'size' => 'small',
        ]);

        if (!$response->successful()) {
            $this->warn("  Pexels API error: {$response->status()}");
            return null;
        }

        $results = $response->json('photos', []);
        if (empty($results)) return null;

        $photo = $results[array_rand($results)];
        return $photo['src']['medium'] ?? $photo['src']['small'] ?? $photo['src']['original'] ?? null;
    }
}
