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
                    'emoji' => $a->data['emoji'] ?? null,
                    'how' => $a->data['how'] ?? null,
                    'rules' => $a->data['rules'] ?? null,
                    'steps' => $a->data['steps'] ?? null,
                    'materials' => $a->data['materials'] ?? null,
                    'lyrics' => $a->data['lyrics'] ?? null,
                    'moves' => $a->data['moves'] ?? null,
                    'audio_url' => $a->data['audio_url'] ?? null,
                    'script' => $a->data['script'] ?? null,
                    'tips' => $a->data['tips'] ?? null,
                    'observation' => $a->data['observation'] ?? null,
                    'explanation' => $a->data['explanation'] ?? null,
                    'fun_fact' => $a->data['fun_fact'] ?? null,
                    'duration' => $a->data['duration'] ?? null,
                    'difficulty' => $a->data['difficulty'] ?? null,
                    'questions' => $a->data['questions'] ?? null,
                    'pages' => $a->data['pages'] ?? null,
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
                'emoji' => $a->data['emoji'] ?? null,
                'how' => $a->data['how'] ?? null,
                'rules' => $a->data['rules'] ?? null,
                'steps' => $a->data['steps'] ?? null,
                'materials' => $a->data['materials'] ?? null,
                'lyrics' => $a->data['lyrics'] ?? null,
                'moves' => $a->data['moves'] ?? null,
                'audio_url' => $a->data['audio_url'] ?? null,
                'script' => $a->data['script'] ?? null,
                'tips' => $a->data['tips'] ?? null,
                'observation' => $a->data['observation'] ?? null,
                'explanation' => $a->data['explanation'] ?? null,
                'fun_fact' => $a->data['fun_fact'] ?? null,
                'duration' => $a->data['duration'] ?? null,
                'difficulty' => $a->data['difficulty'] ?? null,
                'questions' => $a->data['questions'] ?? null,
                'pages' => $a->data['pages'] ?? null,
                'moral' => $a->moral,
                'ages' => $a->ages,
                'skills' => $a->skills,
                'plans' => $a->plans,
                'agama' => $a->agama,
                'status' => $a->status,
                'views' => $a->views,
            ];
        });

        return response()->json($activities);
    }

    public function syncByType(Request $request, string $type)
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
                'emoji' => $a->data['emoji'] ?? null,
                'how' => $a->data['how'] ?? null,
                'rules' => $a->data['rules'] ?? null,
                'steps' => $a->data['steps'] ?? null,
                'materials' => $a->data['materials'] ?? null,
                'lyrics' => $a->data['lyrics'] ?? null,
                'moves' => $a->data['moves'] ?? null,
                'audio_url' => $a->data['audio_url'] ?? null,
                'script' => $a->data['script'] ?? null,
                'tips' => $a->data['tips'] ?? null,
                'observation' => $a->data['observation'] ?? null,
                'explanation' => $a->data['explanation'] ?? null,
                'fun_fact' => $a->data['fun_fact'] ?? null,
                'duration' => $a->data['duration'] ?? null,
                'difficulty' => $a->data['difficulty'] ?? null,
                'questions' => $a->data['questions'] ?? null,
                'pages' => $a->data['pages'] ?? null,
                'exercises' => $a->data['exercises'] ?? null,
                'benefit' => $a->data['benefit'] ?? null,
                'roles' => $a->data['roles'] ?? null,
                'moral' => $a->moral,
                'ages' => $a->ages,
                'skills' => $a->skills,
                'plans' => $a->plans,
                'agama' => $a->agama,
                'status' => $a->status,
                'views' => $a->views,
            ];
        });

        return response()->json([$type => $activities]);
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

    public function generateIdea(Request $request)
    {
        $user = auth('sanctum')->user();
        if (!$user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'type'   => 'required|string',
            'theme'  => 'nullable|string',
            'count'  => 'nullable|integer|min:1|max:50',
            'ages'   => 'nullable|array',
            'skills' => 'nullable|array',
            'agama'  => 'nullable|string',
        ]);

        $type = $request->input('type');

        try {
            app(\App\Services\IdeaGeneratorService::class)->getGenerator($type);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unknown type: '.$type], 422);
        }

        \App\Jobs\GenerateIdeaJob::dispatch(
            type:   $type,
            theme:  $request->input('theme', ''),
            count:  (int) $request->input('count', 20),
            ages:   $request->input('ages', []),
            skills: $request->input('skills', []),
            agama:  $request->input('agama'),
        );

        return response()->json([
            'message' => 'Job dispatched. Ideas will be generated in the background.',
            'type'    => $type,
            'count'   => $request->input('count', 20),
        ]);
    }

    public function ideaToActivity(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $idea = \App\Models\Idea::findOrFail($id);

        $provider = config('ai.default_provider', 'openai');

        $idea->idea_implementor = $provider;
        $idea->idea_tanggal = now()->format('Y-m-d H:i:s');
        $idea->save();

        \App\Jobs\GenerateActivityJob::dispatch(
            type:  $idea->idea_type,
            theme: $idea->idea_nama,
            child: $request->input('child'),
            pages: $request->input('pages'),
            ages:  $idea->idea_ages ?? [],
            agama: !empty($idea->idea_agama) ? $idea->idea_agama[0] : null,
        );

        return response()->json([
            'message' => 'Activity generation job dispatched.',
            'idea_id' => $id,
            'type'    => $idea->idea_type,
            'theme'   => $idea->idea_nama,
        ]);
    }

    public function aiProviders()
    {
        return response()->json(app(\App\Services\AiService::class)->listProviders());
    }

    public function activityTypes()
    {
        $types = [];
        foreach (\App\ActivityType::cases() as $case) {
            $types[] = [
                'value' => $case->value,
                'label' => $case->description(),
                'emoji' => $case->emoji(),
            ];
        }
        return response()->json($types);
    }

    public function skillsList()
    {
        $skills = [
            ['key' => 'bersyukur', 'label' => 'Bersyukur', 'pilar' => 'spiritual'],
            ['key' => 'jujur', 'label' => 'Jujur', 'pilar' => 'spiritual'],
            ['key' => 'peduli_sesama', 'label' => 'Peduli Sesama', 'pilar' => 'spiritual'],
            ['key' => 'menghormati_ortu', 'label' => 'Menghormati Orang Tua', 'pilar' => 'spiritual'],
            ['key' => 'tidak_mudah_menyerah', 'label' => 'Tidak Mudah Menyerah', 'pilar' => 'karakter'],
            ['key' => 'berani_bicara', 'label' => 'Berani Bicara', 'pilar' => 'karakter'],
            ['key' => 'berani_mencoba', 'label' => 'Berani Mencoba', 'pilar' => 'karakter'],
            ['key' => 'menyelesaikan_tugas', 'label' => 'Menyelesaikan Tugas', 'pilar' => 'karakter'],
            ['key' => 'berpikir_kreatif', 'label' => 'Berpikir Kreatif', 'pilar' => 'kreatifitas'],
            ['key' => 'eksperimen', 'label' => 'Eksperimen', 'pilar' => 'kreatifitas'],
            ['key' => 'memecahkan_masalah', 'label' => 'Memecahkan Masalah', 'pilar' => 'kreatifitas'],
            ['key' => 'berimajinasi', 'label' => 'Berimajinasi', 'pilar' => 'kreatifitas'],
            ['key' => 'fokus', 'label' => 'Fokus', 'pilar' => 'disiplin'],
            ['key' => 'atur_waktu', 'label' => 'Atur Waktu', 'pilar' => 'disiplin'],
            ['key' => 'rutin_belajar', 'label' => 'Rutin Belajar', 'pilar' => 'disiplin'],
            ['key' => 'patuh_aturan', 'label' => 'Patuh Aturan', 'pilar' => 'disiplin'],
            ['key' => 'masak_sederhana', 'label' => 'Masak Sederhana', 'pilar' => 'kemandirian'],
            ['key' => 'beres_beres', 'label' => 'Beres-beres', 'pilar' => 'kemandirian'],
            ['key' => 'kebersihan_diri', 'label' => 'Kebersihan Diri', 'pilar' => 'kemandirian'],
            ['key' => 'mengatur_uang', 'label' => 'Mengatur Uang', 'pilar' => 'kemandirian'],
            ['key' => 'berbagi', 'label' => 'Berbagi', 'pilar' => 'sosial'],
            ['key' => 'kerja_sama', 'label' => 'Kerja Sama', 'pilar' => 'sosial'],
            ['key' => 'mendengarkan', 'label' => 'Mendengarkan', 'pilar' => 'sosial'],
            ['key' => 'empati', 'label' => 'Empati', 'pilar' => 'sosial'],
            ['key' => 'mengenali_emosi', 'label' => 'Mengenali Emosi', 'pilar' => 'emosi'],
            ['key' => 'mengelola_marah', 'label' => 'Mengelola Marah', 'pilar' => 'emosi'],
            ['key' => 'quality_time', 'label' => 'Quality Time', 'pilar' => 'emosi'],
            ['key' => 'komunikasi_keluarga', 'label' => 'Komunikasi Keluarga', 'pilar' => 'emosi'],
            ['key' => 'olahraga_teratur', 'label' => 'Olahraga Teratur', 'pilar' => 'kesehatan'],
            ['key' => 'makan_sehat', 'label' => 'Makan Sehat', 'pilar' => 'kesehatan'],
            ['key' => 'tidur_cukup', 'label' => 'Tidur Cukup', 'pilar' => 'kesehatan'],
            ['key' => 'kebersihan_lingkungan', 'label' => 'Kebersihan Lingkungan', 'pilar' => 'kesehatan'],
        ];
        return response()->json($skills);
    }

    public function activitiesList()
    {
        $activities = Activity::select('id', 'type', 'title', 'slug', 'status')
            ->orderBy('type')
            ->orderBy('title')
            ->get()
            ->groupBy('type')
            ->map(fn($items) => $items->map(fn($item) => [
                'id'     => $item->id,
                'title'  => $item->title,
                'slug'   => $item->slug,
                'status' => $item->status,
            ]))
            ->toArray();

        return response()->json($activities);
    }

    public function ideasList(Request $request)
    {
        $query = \App\Models\Idea::orderBy('idea_id', 'desc');

        if ($request->has('type')) {
            $query->where('idea_type', $request->input('type'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('idea_nama', 'like', "%{$search}%")
                  ->orWhere('idea_keterangan', 'like', "%{$search}%");
            });
        }

        $ideas = $query->paginate($request->input('per_page', 50));

        return response()->json($ideas);
    }

    public function ideaUpdate(Request $request, $id)
    {
        $user = auth('sanctum')->user();
        if (!$user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $idea = \App\Models\Idea::findOrFail($id);

        $request->validate([
            'idea_nama'       => 'nullable|string|max:255',
            'idea_keterangan' => 'nullable|string',
            'idea_moral'      => 'nullable|string',
            'idea_type'       => 'nullable|string',
            'idea_agama'      => 'nullable|array',
            'idea_ages'       => 'nullable|array',
            'idea_skills'     => 'nullable|array',
        ]);

        $idea->fill($request->only([
            'idea_nama', 'idea_keterangan', 'idea_moral', 'idea_type',
            'idea_agama', 'idea_ages', 'idea_skills',
        ]));
        $idea->save();

        return response()->json($idea);
    }

    public function ideaDelete($id)
    {
        $user = auth('sanctum')->user();
        if (!$user || ($user->role !== 'developer' && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $idea = \App\Models\Idea::findOrFail($id);
        $idea->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
