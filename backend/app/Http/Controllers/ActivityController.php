<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Activity;
use App\Services\ImageGeneratorService;
use App\Services\ImageSplitterService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    use ControllerTrait;

    public function __construct(Activity $model)
    {
        $this->model = $model::getModel();
    }

    protected function splitAndStore(Request $request, int $activityId, string $slug = null): string
    {
        $pages = (int) $request->input('pages', 0);
        $folderName = $slug ?: $activityId;

        if ($pages >= 2) {
            $result = ImageSplitterService::split($request->file('file'), $activityId, $pages, $folderName);
            return 'cover.png';
        }

        $folder = "images/stories/{$folderName}";
        $path = $request->file('file')->store($folder, 'public');
        return 'cover.png';
    }

    public function postCreate(GeneralRequest $request)
    {
        $hasFile = $request->hasFile('image');

        if ($hasFile) {
            $request->merge(['image' => 'cover.png']);
        }

        $response = CreateAction::run($request, $this->model);

        if ($hasFile && $response['status']) {
            $activity = $response['data'];
            $this->splitAndStore($request, $activity->getKey(), $activity->slug);
        }

        return $this->response($response);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $activity = Activity::findOrFail($id);
        if ($request->hasFile('file')) {
            ImageSplitterService::deleteFolder($id, $activity->slug);
            $request->merge(['image' => $this->splitAndStore($request, $id, $activity->slug)]);
        }

        $response = UpdateAction::run($request, $id, $this->model);

        return $this->response($response);
    }

    public function xputUpdate(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $activity = Activity::findOrFail($id);

        if ($request->hasFile('image')) {
            ImageSplitterService::deleteFolder($id, $activity->slug);
            $folder = "images/stories/{$activity->slug}";
            $request->file('image')->store($folder, 'public');
            $activity->image = 'cover.png';
        }

        if ($request->has('status')) {
            $activity->status = $request->input('status');
        }

        $activity->save();

        return response()->json($activity);
    }

    public function index(Request $request)
    {
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('active', true)->orderBy('sort_order');

        if (!$isDeveloper) {
            $query->where('status', 'approved');
        }

        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $activities = $query->get();

        if ($request->has('grouped')) {
            return response()->json($activities->groupBy('type'));
        }

        return response()->json($activities);
    }

    public function popular(Request $request)
    {
        $limit = $request->input('limit', 10);
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('active', true)->orderByDesc('views')->limit($limit);

        if (!$isDeveloper) {
            $query->where('status', 'approved');
        }

        $activities = $query->get();

        return response()->json($activities);
    }

    public function trackView(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $views = $activity->incrementView();

        return response()->json(['views' => $views]);
    }

    public function show(Request $request, $slug)
    {
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('slug', $slug)->where('active', true);

        if (!$isDeveloper) {
            $query->where('status', 'approved');
        }

        $activity = $query->firstOrFail();

        return response()->json($activity);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'nullable|file|image|max:10240',
            'pages' => 'nullable|integer|min:2|max:25',
            'moral' => 'nullable|string|max:500',
            'ages' => 'nullable|array',
            'skills' => 'nullable|array',
            'data' => 'nullable|array',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
            'status' => 'nullable|in:pending,review,approved,rejected',
            'created_by' => 'nullable|integer',
            'prompt' => 'nullable|string',
            'notes' => 'nullable|string',
            'creator' => 'nullable|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['active'] = $data['active'] ?? true;
        $data['status'] = $data['status'] ?? 'pending';
        $data['created_by'] = $data['created_by'] ?? ($request->user()?->id ?? 1);

        if ($request->hasFile('image')) {
            $data['image'] = 'cover.png';
        }

        $activity = Activity::create($data);

        if ($request->hasFile('image')) {
            $this->splitAndStore($request, $activity->getKey());
        }

        return response()->json($activity, 201);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);

        ImageSplitterService::deleteFolder($id);
        $activity->delete();

        return response()->json(null, 204);
    }

    public function types()
    {
        $types = [
            'storytelling' => ['emoji' => '📖', 'title' => 'Story Telling', 'desc' => 'Anak belajar mendengar, bercerita dan menyampaikan ide secara verbal.', 'color' => '#4CAF50', 'bg' => '#E8F5E9', 'feature' => 'story'],
            'bermain_peran' => ['emoji' => '🎭', 'title' => 'Bermain Peran', 'desc' => 'Anak belajar memahami perspektif orang lain melalui peran.', 'color' => '#FF9800', 'bg' => '#FFF3E0', 'feature' => 'roleplay'],
            'permainan' => ['emoji' => '🎲', 'title' => 'Permainan', 'desc' => 'Anak belajar aturan, kerja sama, dan sportivitas.', 'color' => '#E91E63', 'bg' => '#FCE4EC', 'feature' => 'game'],
            'monolog' => ['emoji' => '🎤', 'title' => 'Monolog', 'desc' => 'Anak belajar berani tampil dan berbicara di depan umum.', 'color' => '#9C27B0', 'bg' => '#F3E5F5', 'feature' => 'monolog'],
            'proyek_kreatif' => ['emoji' => '🎨', 'title' => 'Proyek Kreatif & Seni', 'desc' => 'Anak belajar mengekspresikan diri melalui seni.', 'color' => '#2196F3', 'bg' => '#E3F2FD', 'feature' => 'project'],
            'musik_gerak' => ['emoji' => '🎵', 'title' => 'Musik & Gerak', 'desc' => 'Anak belajar ritme, koordinasi, dan ekspresi tubuh.', 'color' => '#FF5722', 'bg' => '#FBE9E7', 'feature' => 'music'],
            'puzzle' => ['emoji' => '🧩', 'title' => 'Puzzle & Problem Solving', 'desc' => 'Anak belajar berpikir logis dan memecahkan masalah.', 'color' => '#673AB7', 'bg' => '#EDE7F6', 'feature' => 'puzzle'],
            'mindfulness' => ['emoji' => '🧘', 'title' => 'Mindfulness & Refleksi', 'desc' => 'Anak belajar mengenali perasaan dan menenangkan diri.', 'color' => '#795548', 'bg' => '#EFEBE9', 'feature' => 'mindfulness'],
            'outdoor' => ['emoji' => '🌿', 'title' => 'Outdoor Exploration', 'desc' => 'Anak belajar mengenal alam dan lingkungan sekitar.', 'color' => '#009688', 'bg' => '#E0F2F1', 'feature' => 'outdoor'],
            'ilmu_pengetahuan' => ['emoji' => '🔬', 'title' => 'Ilmu Pengetahuan & Literasi', 'desc' => 'Anak belajar sains, eksperimen, dan meningkatkan kemampuan literasi.', 'color' => '#0D47A1', 'bg' => '#E3F2FD', 'feature' => 'ilmu_pengetahuan'],
        ];

        return response()->json($types);
    }

    public function generateImage(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        if (!$activity->prompt) {
            return response()->json(['message' => 'Activity has no prompt'], 422);
        }

        $model = $request->input('model');
        $size = $request->input('size', '2K');
        $pagesCount = (int) $request->input('pages', 0);

        if (!$pagesCount) {
            $pagesCount = isset($activity->data['pages'])
                ? count($activity->data['pages']) + 1
                : 16;
        }

        $grid = ImageSplitterService::getGrid($pagesCount);

        if (!$grid) {
            return response()->json(['message' => "Unsupported page count: {$pagesCount}"], 422);
        }

        $generator = new ImageGeneratorService();

        $imageUrl = $generator->generate($activity->prompt, $size, $model);

        if (!$imageUrl) {
            return response()->json(['message' => 'Failed to generate image'], 500);
        }

        $tmpPath = $generator->download($imageUrl);

        if (!$tmpPath) {
            return response()->json(['message' => 'Failed to download image'], 500);
        }

        try {
            $file = new UploadedFile(
                $tmpPath,
                'story.png',
                mime_content_type($tmpPath),
                null,
                true
            );

            $result = ImageSplitterService::split($file, $activity->id, $pagesCount, $activity->slug);

            @unlink($tmpPath);

            return response()->json([
                'message' => 'Image generated and split successfully',
                'folder' => $result['folder'],
                'files' => $result['files'],
                'grid' => $result['grid'],
            ]);
        } catch (\Throwable $e) {
            @unlink($tmpPath);
            return response()->json(['message' => 'Failed to split image: ' . $e->getMessage()], 500);
        }
    }
}
