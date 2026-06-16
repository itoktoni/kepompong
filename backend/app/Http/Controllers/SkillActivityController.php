<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Skill;
use App\Models\SkillActivity;
use Illuminate\Http\Request;

class SkillActivityController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeActivityInput($request->all());

        $rules = [
            'skill_key' => 'required|string',
            'skill_activity_title' => 'required|string|max:255',
            'skill_activity_emoji' => 'nullable|string|max:10',
            'skill_activity_feature' => 'nullable|string',
            'skill_activity_date' => 'nullable|string|max:50',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $skill = Skill::where('skill_id_anak', $anakId)->where('skill_key', $data['skill_key'])->first();

        if (! $skill) {
            return response()->json(['message' => 'Skill not found'], 404);
        }

        $activity = SkillActivity::create([
            'skill_activity_id_skill' => $skill->skill_id,
            'skill_activity_title' => $data['skill_activity_title'],
            'skill_activity_emoji' => $data['skill_activity_emoji'] ?? '📌',
            'skill_activity_feature' => $data['skill_activity_feature'] ?? null,
            'skill_activity_date' => $data['skill_activity_date'] ?? now()->format('d M Y'),
        ]);

        return response()->json($activity, 201);
    }

    public function destroy(Request $request, $anakId, $activityId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        SkillActivity::where('skill_activity_id', $activityId)
            ->whereHas('has_skill', fn ($q) => $q->where('skill_id_anak', $anakId))
            ->delete();

        return response()->json(null, 204);
    }

    public function toggle(Request $request, $anakId, $activityId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $activity = SkillActivity::where('skill_activity_id', $activityId)
            ->whereHas('has_skill', fn ($q) => $q->where('skill_id_anak', $anakId))
            ->firstOrFail();

        $activity->update(['skill_activity_completed' => ! $activity->skill_activity_completed]);

        return response()->json($activity);
    }
}
