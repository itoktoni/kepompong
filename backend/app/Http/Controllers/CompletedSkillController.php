<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\CompletedSkill;
use Illuminate\Http\Request;

class CompletedSkillController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeCompletedSkillInput($request->all());

        $rules = [
            'completed_skill_key' => 'required|string',
            'completed_skill_emoji' => 'nullable|string|max:10',
            'completed_skill_title' => 'required|string|max:255',
            'completed_skill_pilar' => 'nullable|string',
            'completed_skill_color' => 'nullable|string|max:20',
            'completed_skill_completed_at' => 'nullable|date',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $completed = CompletedSkill::updateOrCreate(
            ['completed_skill_id_anak' => $anakId, 'completed_skill_key' => $data['completed_skill_key']],
            [
                'completed_skill_emoji' => $data['completed_skill_emoji'] ?? null,
                'completed_skill_title' => $data['completed_skill_title'],
                'completed_skill_pilar' => $data['completed_skill_pilar'] ?? null,
                'completed_skill_color' => $data['completed_skill_color'] ?? null,
                'completed_skill_completed_at' => $data['completed_skill_completed_at'] ?? now(),
            ]
        );

        return response()->json($completed, 201);
    }

    public function destroy(Request $request, $anakId, $key)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        CompletedSkill::where('completed_skill_id_anak', $anakId)->where('completed_skill_key', $key)->delete();

        return response()->json(null, 204);
    }
}
