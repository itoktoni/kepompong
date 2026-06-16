<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Evaluation;
use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function index(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $evaluations = Evaluation::where('evaluation_id_anak', $anakId)
            ->orderByDesc('evaluation_created_at')
            ->get();

        $active = $evaluations->filter(fn ($e) => $e->evaluation_points < $e->evaluation_max_points);
        $completed = $evaluations->filter(fn ($e) => $e->evaluation_points >= $e->evaluation_max_points);

        $totalPoints = $completed->sum('evaluation_points');
        $totalMax = $completed->sum('evaluation_max_points');

        return response()->json([
            'evaluations' => $evaluations,
            'active' => $active->values(),
            'completed_count' => $completed->count(),
            'total_points' => $totalPoints,
            'total_max' => $totalMax,
        ]);
    }

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeEvaluationInput($request->all());

        $rules = [
            'evaluation_skill_key' => 'required|string',
            'evaluation_skill_title' => 'nullable|string|max:255',
            'evaluation_pilar' => 'nullable|string|max:100',
            'evaluation_points' => 'required|integer|min:0|max:100',
            'evaluation_max_points' => 'nullable|integer|min:1|max:100',
            'evaluation_notes' => 'nullable|string|max:500',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $maxPoints = $data['evaluation_max_points'] ?? 10;

        $incomplete = Evaluation::where('evaluation_id_anak', $anakId)
            ->where('evaluation_skill_key', $data['evaluation_skill_key'])
            ->whereRaw('evaluation_points < evaluation_max_points')
            ->first();

        if ($incomplete) {
            $incomplete->update([
                'evaluation_skill_title' => $data['evaluation_skill_title'] ?? $incomplete->evaluation_skill_title,
                'evaluation_pilar' => $data['evaluation_pilar'] ?? $incomplete->evaluation_pilar,
                'evaluation_points' => $data['evaluation_points'],
                'evaluation_max_points' => $maxPoints,
                'evaluation_notes' => $data['evaluation_notes'] ?? $incomplete->evaluation_notes,
            ]);
            $evaluation = $incomplete;
        } else {
            $evaluation = Evaluation::create([
                'evaluation_id_anak' => $anakId,
                'evaluation_skill_key' => $data['evaluation_skill_key'],
                'evaluation_skill_title' => $data['evaluation_skill_title'] ?? null,
                'evaluation_pilar' => $data['evaluation_pilar'] ?? null,
                'evaluation_points' => $data['evaluation_points'],
                'evaluation_max_points' => $maxPoints,
                'evaluation_notes' => $data['evaluation_notes'] ?? null,
            ]);
        }

        return response()->json($evaluation, 201);
    }

    public function destroy(Request $request, $anakId, $evalId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        Evaluation::where('evaluation_id', $evalId)->where('evaluation_id_anak', $anakId)->delete();

        return response()->json(null, 204);
    }
}
