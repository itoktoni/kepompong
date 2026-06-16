<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Skill as SkillModel;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeSkillInput($request->all());

        $rules = [
            'skill_key' => 'required|string|max:80',
            'skill_emoji' => 'nullable|string|max:10',
            'skill_title' => 'required|string|max:255',
            'skill_pilar' => 'nullable|string|max:50',
            'skill_color' => 'nullable|string|max:20',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $skill = \App\Models\Skill::create([
            'skill_id_anak' => $anakId,
            'skill_key' => $data['skill_key'],
            'skill_emoji' => $data['skill_emoji'] ?? null,
            'skill_title' => $data['skill_title'],
            'skill_pilar' => $data['skill_pilar'] ?? null,
            'skill_color' => $data['skill_color'] ?? null,
            'skill_status' => 'pending',
        ]);

        return response()->json($skill, 201);
    }

    public function update(Request $request, $anakId, $skillId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $skill = \App\Models\Skill::where('skill_id', $skillId)->where('skill_id_anak', $anakId)->firstOrFail();
        $data = $this->normalizeSkillInput($request->all());
        $skill->update($data);

        return response()->json($skill);
    }

    public function destroy(Request $request, $anakId, $skillId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        \App\Models\Skill::where('skill_id', $skillId)->where('skill_id_anak', $anakId)->delete();

        return response()->json(null, 204);
    }
}
