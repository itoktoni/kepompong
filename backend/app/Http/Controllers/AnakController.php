<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Anak;
use App\Models\Skill;
use App\Models\CompletedSkill;
use App\Models\Challenge;
use App\Models\ChallengeHistory;
use App\Models\Checklist;
use App\Models\Schedule;
use App\Models\Worksheet;
use App\Models\SkillActivity;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnakController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function index(Request $request)
    {
        $userId = $request->user()->id ?? null;
        $user = $request->user();
        $familyIds = $user->family ?? [];

        $anak = Anak::where(function ($q) use ($userId, $familyIds) {
                $q->where('anak_id_user', $userId);
                if (! empty($familyIds)) {
                    $q->orWhereIn('anak_id_user', $familyIds);
                }
            })
            ->with(['has_skills.has_activities', 'has_completed_skills', 'has_challenges', 'has_challenge_histories', 'has_checklists', 'has_schedules', 'has_worksheets'])
            ->get();

        return response()->json($anak);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $userId = $user->id ?? null;

        $maxChildren = 1;
        if ($userId && $user->subscribe) {
            $sub = Subscribe::find($user->subscribe);
            if ($sub && $sub->subscribe_end_at && now()->lt($sub->subscribe_end_at)) {
                $maxChildren = (int) ($sub->subsribe_value ?? 1);
            }
        }

        $currentCount = Anak::where('anak_id_user', $userId)->count();
        if ($currentCount >= $maxChildren) {
            return response()->json([
                'message' => "Maksimal {$maxChildren} anak. Upgrade plan untuk menambah lebih banyak.",
            ], 422);
        }

        $input = $request->all();
        $data = $this->normalizeAnakInput($input);

        $rules = [
            'anak_nama' => 'required|string|max:255',
            'anak_gender' => 'nullable|string|max:20',
            'anak_umur' => 'nullable|integer|min:1|max:18',
            'anak_tanggal_lahir' => 'nullable|integer|min:1|max:31',
            'anak_bulan_lahir' => 'nullable|integer|min:1|max:12',
            'anak_tahun_lahir' => 'nullable|integer|min:2000|max:2030',
            'anak_emoji' => 'nullable|string|max:10',
            'anak_agama' => 'nullable|in:islam,kristen_protestan,kristen_katolik,hindu,buddha,konghucu',
            'anak_avatar' => 'nullable|string|max:255',
            'anak_settings' => 'nullable|array',
            'anak_created_at' => 'nullable|array',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'The given data was invalid.',
                'data' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        $data['anak_id_user'] = $userId;
        if (! isset($data['anak_umur']) && isset($data['anak_tahun_lahir'])) {
            $data['anak_umur'] = (int) date('Y') - (int) $data['anak_tahun_lahir'];
        }

        $data = array_merge($data, [
            'anak_created_at' => date('Y-m-d H:i:s')
        ]);

        $anak = Anak::create($data);

        return response()->json($anak->load(['has_skills.has_activities', 'has_completed_skills']), 201);
    }

    public function update(Request $request, $id)
    {
        if (! $this->authorizeAnak($request, (int) $id)) {
            return $this->unauthorized();
        }

        $input = $request->all();
        $data = $this->normalizeAnakInput($input);

        $rules = [
            'anak_nama' => 'sometimes|string|max:255',
            'anak_gender' => 'nullable|string|max:20',
            'anak_agama' => 'nullable|in:islam,kristen_protestan,kristen_katolik,hindu,buddha,konghucu',
            'anak_umur' => 'nullable|integer|min:1|max:18',
            'anak_tanggal_lahir' => 'nullable|integer|min:1|max:31',
            'anak_bulan_lahir' => 'nullable|integer|min:1|max:12',
            'anak_tahun_lahir' => 'nullable|integer|min:2000|max:2030',
            'anak_emoji' => 'nullable|string|max:10',
            'anak_avatar' => 'nullable|string|max:255',
            'anak_settings' => 'nullable|array',
            'anak_updated_at' => 'nullable|array',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 422,
                'message' => 'The given data was invalid.',
                'data' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        if (! isset($data['anak_umur']) && isset($data['anak_tahun_lahir'])) {
            $data['anak_umur'] = (int) date('Y') - (int) $data['anak_tahun_lahir'];
        }

        $anak = Anak::findOrFail($id);
        $data = array_merge($data, [
            'anak_updated_at' => date('Y-m-d H:i:s')
        ]);

        Log::info($data);

        $anak->update($data);

        return response()->json($anak->load(['has_skills.has_activities', 'has_completed_skills']));
    }

    public function destroy(Request $request, $id)
    {
        if (! $this->authorizeAnak($request, (int) $id)) {
            return $this->unauthorized();
        }

        Anak::where('anak_id', $id)->delete();

        return response()->json(null, 204);
    }

    public function sync(Request $request)
    {
        $request->validate([
            'anak_list' => 'required|array',
        ]);

        $userId = $request->user()->id ?? null;
        $results = [];

        DB::beginTransaction();
        try {
            foreach ($request->anak_list as $anakData) {
                $localId = $anakData['id'] ?? null;
                $nama = $anakData['anak_nama'] ?? $anakData['nama'] ?? 'Anak';

                $anak = Anak::when($userId, fn ($q) => $q->where('anak_id_user', $userId))
                    ->where('anak_nama', $nama)
                    ->first();

                if (! $anak) {
                    $anak = Anak::create([
                        'anak_id_user' => $userId,
                        'anak_nama' => $nama,
                        'anak_gender' => $anakData['anak_gender'] ?? $anakData['gender'] ?? null,
                        'anak_umur' => $anakData['anak_umur'] ?? $anakData['umur'] ?? ($anakData['anak_tahun_lahir'] ?? $anakData['tahun_lahir'] ? (int) date('Y') - (int) ($anakData['anak_tahun_lahir'] ?? $anakData['tahun_lahir']) : null),
                        'anak_tanggal_lahir' => $anakData['anak_tanggal_lahir'] ?? $anakData['tanggal_lahir'] ?? $anakData['tanggal'] ?? null,
                        'anak_bulan_lahir' => $anakData['anak_bulan_lahir'] ?? $anakData['bulan_lahir'] ?? $anakData['bulan'] ?? null,
                        'anak_tahun_lahir' => $anakData['anak_tahun_lahir'] ?? $anakData['tahun_lahir'] ?? $anakData['tahun'] ?? null,
                        'anak_emoji' => $anakData['anak_emoji'] ?? $anakData['emoji'] ?? '👶',
                        'anak_settings' => $anakData['anak_settings'] ?? $anakData['settings'] ?? [],
                    ]);
                } else {
                    $tahun = $anakData['anak_tahun_lahir'] ?? $anakData['tahun_lahir'] ?? $anakData['tahun'] ?? $anak->anak_tahun_lahir;
                    $anak->update([
                        'anak_gender' => $anakData['anak_gender'] ?? $anakData['gender'] ?? $anak->anak_gender,
                        'anak_umur' => $anakData['anak_umur'] ?? $anakData['umur'] ?? ($tahun ? (int) date('Y') - (int) $tahun : $anak->anak_umur),
                        'anak_tanggal_lahir' => $anakData['anak_tanggal_lahir'] ?? $anakData['tanggal_lahir'] ?? $anakData['tanggal'] ?? $anak->anak_tanggal_lahir,
                        'anak_bulan_lahir' => $anakData['anak_bulan_lahir'] ?? $anakData['bulan_lahir'] ?? $anakData['bulan'] ?? $anak->anak_bulan_lahir,
                        'anak_tahun_lahir' => $tahun,
                        'anak_settings' => $anakData['anak_settings'] ?? $anakData['settings'] ?? $anak->anak_settings,
                    ]);
                }

                if (isset($anakData['skills']) && is_array($anakData['skills'])) {
                    foreach ($anakData['skills'] as $s) {
                        $skill = Skill::updateOrCreate(
                            ['skill_id_anak' => $anak->anak_id, 'skill_key' => $s['skill_key'] ?? $s['key'] ?? ''],
                            [
                                'skill_emoji' => $s['skill_emoji'] ?? $s['emoji'] ?? null,
                                'skill_title' => $s['skill_title'] ?? $s['title'] ?? '',
                                'skill_pilar' => $s['skill_pilar'] ?? $s['pilar'] ?? null,
                                'skill_progress' => $s['skill_progress'] ?? $s['progress'] ?? 0,
                                'skill_color' => $s['skill_color'] ?? $s['color'] ?? null,
                                'skill_status' => $s['skill_status'] ?? $s['status'] ?? 'pending',
                            ]
                        );
                        if (isset($s['activities']) && is_array($s['activities'])) {
                            $skill->has_activities()->delete();
                            foreach ($s['activities'] as $a) {
                                $skill->has_activities()->create([
                                    'skill_activity_title' => $a['skill_activity_title'] ?? $a['title'] ?? '',
                                    'skill_activity_emoji' => $a['skill_activity_emoji'] ?? $a['emoji'] ?? null,
                                    'skill_activity_feature' => $a['skill_activity_feature'] ?? $a['feature'] ?? null,
                                    'skill_activity_date' => $a['skill_activity_date'] ?? $a['date'] ?? null,
                                ]);
                            }
                        }
                    }
                }

                if (isset($anakData['completed_skills']) && is_array($anakData['completed_skills'])) {
                    foreach ($anakData['completed_skills'] as $cs) {
                        CompletedSkill::updateOrCreate(
                            ['completed_skill_id_anak' => $anak->anak_id, 'completed_skill_key' => $cs['completed_skill_key'] ?? $cs['key'] ?? ''],
                            [
                                'completed_skill_emoji' => $cs['completed_skill_emoji'] ?? $cs['emoji'] ?? null,
                                'completed_skill_title' => $cs['completed_skill_title'] ?? $cs['title'] ?? '',
                                'completed_skill_pilar' => $cs['completed_skill_pilar'] ?? $cs['pilar'] ?? null,
                                'completed_skill_color' => $cs['completed_skill_color'] ?? $cs['color'] ?? null,
                                'completed_skill_completed_at' => $cs['completed_skill_completed_at'] ?? $cs['completed_at'] ?? $cs['completedAt'] ?? null,
                            ]
                        );
                    }
                }

                if (isset($anakData['challenges']) && is_array($anakData['challenges'])) {
                    Challenge::where('challenge_id_anak', $anak->anak_id)->delete();
                    foreach ($anakData['challenges'] as $c) {
                        Challenge::create([
                            'challenge_id_anak' => $anak->anak_id,
                            'challenge_category' => $c['challenge_category'] ?? $c['category'] ?? '',
                            'challenge_title' => $c['challenge_title'] ?? $c['title'] ?? '',
                            'challenge_emoji' => $c['challenge_emoji'] ?? $c['emoji'] ?? null,
                            'challenge_points' => $c['challenge_points'] ?? $c['points'] ?? 0,
                            'challenge_status' => $c['challenge_status'] ?? $c['status'] ?? 'pending',
                            'challenge_date' => $c['challenge_date'] ?? $c['date'] ?? null,
                            'challenge_meta' => $c['challenge_meta'] ?? $c['meta'] ?? null,
                        ]);
                    }
                }

                if (isset($anakData['challengeHistory']) && is_array($anakData['challengeHistory'])) {
                    ChallengeHistory::where('challenge_history_id_anak', $anak->anak_id)->delete();
                    foreach ($anakData['challengeHistory'] as $h) {
                        ChallengeHistory::create([
                            'challenge_history_id_anak' => $anak->anak_id,
                            'challenge_history_category' => $h['challenge_history_category'] ?? $h['category'] ?? '',
                            'challenge_history_title' => $h['challenge_history_title'] ?? $h['title'] ?? '',
                            'challenge_history_date' => $h['challenge_history_date'] ?? $h['date'] ?? null,
                            'challenge_history_meta' => $h['challenge_history_meta'] ?? $h['meta'] ?? null,
                        ]);
                    }
                }

                if (isset($anakData['checklists']) && is_array($anakData['checklists'])) {
                    Checklist::where('checklist_id_anak', $anak->anak_id)->delete();
                    foreach ($anakData['checklists'] as $cl) {
                        Checklist::create([
                            'checklist_id_anak' => $anak->anak_id,
                            'checklist_title' => $cl['checklist_title'] ?? $cl['title'] ?? '',
                            'checklist_items' => $cl['checklist_items'] ?? $cl['items'] ?? [],
                            'checklist_date' => $cl['checklist_date'] ?? $cl['date'] ?? null,
                        ]);
                    }
                }

                if (isset($anakData['schedules']) && is_array($anakData['schedules'])) {
                    Schedule::where('schedule_id_anak', $anak->anak_id)->delete();
                    foreach ($anakData['schedules'] as $s) {
                        Schedule::create([
                            'schedule_id_anak' => $anak->anak_id,
                            'schedule_label' => $s['schedule_label'] ?? $s['label'] ?? '',
                            'schedule_time' => $s['schedule_time'] ?? $s['time'] ?? null,
                            'schedule_done' => $s['schedule_done'] ?? $s['done'] ?? false,
                            'schedule_date' => $s['schedule_date'] ?? $s['date'] ?? null,
                        ]);
                    }
                }

                if (isset($anakData['worksheets']) && is_array($anakData['worksheets'])) {
                    Worksheet::where('worksheet_id_anak', $anak->anak_id)->delete();
                    foreach ($anakData['worksheets'] as $w) {
                        Worksheet::create([
                            'worksheet_id_anak' => $anak->anak_id,
                            'worksheet_type' => $w['worksheet_type'] ?? $w['type'] ?? '',
                            'worksheet_data' => $w['worksheet_data'] ?? $w['data'] ?? [],
                            'worksheet_date' => $w['worksheet_date'] ?? $w['date'] ?? null,
                            'worksheet_status' => $w['worksheet_status'] ?? $w['status'] ?? 'pending',
                        ]);
                    }
                }

                $results[] = [
                    'local_id' => $localId,
                    'server_id' => $anak->anak_id,
                    'nama' => $anak->anak_nama,
                ];
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'synced' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
