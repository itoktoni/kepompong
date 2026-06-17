<?php

namespace App\Http\Controllers;

use App\Actions\CreateAction;
use App\Actions\UpdateAction;
use App\Concerns\ControllerTrait;
use App\Http\Requests\GeneralRequest;
use App\Models\Activity;
use App\Services\ActivityAssetService;
use App\Services\ImageGeneratorService;
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

    public function postCreate(GeneralRequest $request)
    {
        $hasFile = $request->hasFile('image');

        if ($hasFile) {
            $request->merge(['image' => 'cover.png']);
        }

        $response = CreateAction::run($request, $this->model);

        if ($hasFile && $response['status']) {
            $activity = $response['data'];
            $assetService = app(ActivityAssetService::class);
            $assetService->processUpload($activity, $request->file('image'));
        }

        return $this->response($response);
    }

    public function postUpdate(GeneralRequest $request, $id)
    {
        $activity = Activity::findOrFail($id);
        if ($request->hasFile('file')) {
            $assetService = app(ActivityAssetService::class);
            $assetService->processUpload($activity, $request->file('file'));
            $request->merge(['image' => 'cover.png']);
        }

        $response = UpdateAction::run($request, $id, $this->model);

        return $this->response($response);
    }

    public function xputUpdate(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (! $user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $activity = Activity::findOrFail($id);

        if ($request->hasFile('image')) {
            $assetService = app(ActivityAssetService::class);
            $result = $assetService->processUpload($activity, $request->file('image'));

            return response()->json([
                'activity' => $activity->fresh(),
                'asset'    => $result,
            ]);
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

        if (! $isDeveloper) {
            $query->where('status', 'approved');
        }

        if ($request->has('type')) {
            $query->ofType($request->type);
        }

        $activities = $query->get();

        if ($request->has('grouped')) {
            $activities->transform(function ($a) {
                return [
                    'id' => $a->id,
                    'type' => $a->type,
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'desc' => $a->desc,
                    'image' => $a->image,
                    'moral' => $a->moral,
                    'ages' => $a->ages,
                    'skills' => $a->skills,
                    'plans' => $a->plans,
                    'agama' => $a->agama,
                    'status' => $a->status,
                    'views' => $a->views,
                ];
            });

            return response()->json($activities->groupBy('type'));
        }

        return response()->json($activities);
    }

    public function byType(Request $request, string $type)
    {
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('active', true)->ofType($type)->orderBy('sort_order');

        if (! $isDeveloper) {
            $query->where('status', 'approved');
        }

        $activities = $query->get()->map(function ($a) {
            return [
                'id' => $a->id,
                'type' => $a->type,
                'title' => $a->title,
                'slug' => $a->slug,
                'desc' => $a->desc,
                'image' => $a->image,
                'moral' => $a->moral,
                'ages' => $a->ages,
                'skills' => $a->skills,
                'plans' => $a->plans,
                'agama' => $a->agama,
                'status' => $a->status,
                'views' => $a->views,
                'emoji' => $a->data['emoji'] ?? null,
            ];
        });

        return response()->json($activities);
    }

    public function popular(Request $request)
    {
        $limit = $request->input('limit', 10);
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('active', true)->orderByDesc('views')->limit($limit);

        if (! $isDeveloper) {
            $query->where('status', 'approved');
        }

        $activities = $query->get();

        return response()->json($activities);
    }

    public function trackView(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->incrementView();

        return response()->json($activity);
    }

    public function show(Request $request, $slug)
    {
        $user = auth('sanctum')->user();
        $isDeveloper = $user && $user->role === 'developer';

        $query = Activity::where('slug', $slug)->where('active', true);

        if (! $isDeveloper) {
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

        $assetService = app(ActivityAssetService::class);
        $asset = $assetService->getAsset($activity->type);
        $folder = $asset->getFolder($activity);

        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory($folder);
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
            'tebak_teakan' => ['emoji' => '🤔', 'title' => 'Tebak-tebakan', 'desc' => 'Anak belajar berpikir kreatif dan logis melalui teka-teki seru.', 'color' => '#FF6F00', 'bg' => '#FFF8E1', 'feature' => 'guess'],
            'permainan_tangan' => ['emoji' => '🤲', 'title' => 'Permainan Tangan', 'desc' => 'Anak belajar koordinasi, ritme, dan kerja sama melalui permainan tangan.', 'color' => '#AD1457', 'bg' => '#FCE4EC', 'feature' => 'handgame'],
            'latihan_otak' => ['emoji' => '🧠', 'title' => 'Latihan Otak', 'desc' => 'Anak melatih konsentrasi, daya ingat, dan kemampuan berpikir logis.', 'color' => '#283593', 'bg' => '#E8EAF6', 'feature' => 'braintrain'],
            'komik' => ['emoji' => '💬', 'title' => 'Komik Anak', 'desc' => 'Anak belajar memahami cerita melalui visual komik yang menarik.', 'color' => '#E65100', 'bg' => '#FFF3E0', 'feature' => 'comic'],
        ];

        return response()->json($types);
    }

    public function generateImage(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        if (! $activity->prompt) {
            return response()->json(['message' => 'Activity has no prompt'], 422);
        }

        $assetService = app(ActivityAssetService::class);
        $asset = $assetService->getAsset($activity->type);
        $pagesCount = (int) $request->input('pages', $asset->getPageCount($activity));

        $model = $request->input('model');
        $size = $request->input('size', '2K');

        $generator = new ImageGeneratorService;
        $imageUrl = $generator->generate($activity->prompt, $size, $model);

        if (! $imageUrl) {
            return response()->json(['message' => 'Failed to generate image'], 500);
        }

        $tmpPath = $generator->download($imageUrl);

        if (! $tmpPath) {
            return response()->json(['message' => 'Failed to download image'], 500);
        }

        try {
            $file = new UploadedFile(
                $tmpPath,
                'image.png',
                mime_content_type($tmpPath),
                null,
                true
            );

            $result = $assetService->processUpload($activity, $file, $pagesCount);

            @unlink($tmpPath);

            return response()->json(array_merge([
                'message' => 'Image generated and processed successfully',
            ], $result));
        } catch (\Throwable $e) {
            @unlink($tmpPath);

            return response()->json(['message' => 'Failed to process image: '.$e->getMessage()], 500);
        }
    }
}
