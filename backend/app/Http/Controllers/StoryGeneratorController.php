<?php

namespace App\Http\Controllers;

use App\Services\ActivityGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoryGeneratorController extends Controller
{
    public function __construct(
        private ActivityGeneratorService $generator
    ) {}

    public function generate(Request $request): JsonResponse
    {
        $request->validate([
            'theme' => 'required|string|max:100',
            'child_name' => 'nullable|string|max:50',
            'pages_count' => 'nullable|integer|min:2|max:24',
        ]);

        $input = [
            'theme' => $request->input('theme'),
            'child' => $request->input('child_name') ?: 'Anak',
            'pages' => (int) ($request->input('pages_count') ?: 16),
            'ages'  => [],
        ];

        $result = $this->generator->generateContent('storytelling', $input);

        return response()->json([
            'title' => $result['title'],
            'moral' => $result['moral'] ?? '',
            'pages' => $result['pages'] ?? [],
        ]);
    }
}
