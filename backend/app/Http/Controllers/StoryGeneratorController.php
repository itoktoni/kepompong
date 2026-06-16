<?php

namespace App\Http\Controllers;

use App\Services\LocalImageGeneratorService;
use App\Services\StoryGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryGeneratorController extends Controller
{
    public function __construct(
        private StoryGeneratorService $stories,
        private LocalImageGeneratorService $images
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|string|max:100',
            'child_name' => 'nullable|string|max:50',
            'pages_count' => 'nullable|integer|min:2|max:8',
            'generate_images' => 'nullable|boolean',
        ]);

        $theme = $request->input('theme');
        $childName = $request->input('child_name') ?: 'Anak';
        $pagesCount = (int) ($request->input('pages_count') ?: 4);
        $generateImages = (bool) $request->input('generate_images');

        $generated = $this->stories->generate($theme, $childName);

        $pages = array_slice($generated['pages'], 0, $pagesCount);
        $renumbered = [];
        foreach ($pages as $index => $page) {
            $renumbered[] = [
                'num' => $index + 1,
                'text' => $page['text'],
            ];
        }

        if ($generateImages) {
            foreach ($renumbered as &$page) {
                $prompt = trim($page['text']);
                $page['image'] = $this->images->generate($prompt);
            }
            unset($page);
        }

        $title = $generated['title'];
        $slug = Str::slug($title);

        return response()->json([
            'title' => $title,
            'slug' => $slug,
            'moral' => $generated['moral'],
            'pages' => $renumbered,
            'theme' => $theme,
            'child_name' => $childName,
        ]);
    }

    public function preview(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'moral' => 'required|string|max:500',
            'pages' => 'required|array|min:1',
            'pages.*.text' => 'required|string',
            'generate_images' => 'nullable|boolean',
        ]);

        $pages = $request->input('pages', []);
        $generateImages = (bool) $request->input('generate_images');

        if ($generateImages) {
            foreach ($pages as &$page) {
                $prompt = trim($page['text'] ?? '');
                $page['image'] = $this->images->generate($prompt);
            }
            unset($page);
        }

        return response()->json([
            'title' => $request->input('title'),
            'moral' => $request->input('moral'),
            'pages' => $pages,
        ]);
    }
}
