<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChallengeController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function index(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $challenges = Challenge::where('challenge_id_anak', $anakId)
            ->orderByDesc('challenge_created_at')
            ->get();

        return response()->json($challenges);
    }

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeChallengeInput($request->all());

        $rules = [
            'challenge_category' => 'required|string|max:100',
            'challenge_title' => 'required|string|max:255',
            'challenge_emoji' => 'nullable|string|max:10',
            'challenge_points' => 'nullable|integer|min:0',
            'challenge_maxPoints' => 'nullable|integer|min:0',
            'challenge_status' => 'nullable|string|in:pending,completed,cancelled',
            'challenge_date' => 'nullable|string|max:50',
            'challenge_meta' => 'nullable|array',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $challenge = Challenge::create([
            'challenge_id_anak' => $anakId,
            'challenge_category' => $data['challenge_category'],
            'challenge_title' => $data['challenge_title'],
            'challenge_emoji' => $data['challenge_emoji'] ?? null,
            'challenge_points' => $data['challenge_points'] ?? 0,
            'challenge_status' => $data['challenge_status'] ?? 'pending',
            'challenge_date' => $data['challenge_date'] ?? null,
            'challenge_meta' => $data['challenge_meta'] ?? array_filter([
                'maxPoints' => $data['challenge_maxPoints'] ?? null,
                'color' => $request->input('color'),
                'bg' => $request->input('bg'),
                'notes' => $request->input('notes'),
            ], fn ($v) => $v !== null),
        ]);

        return response()->json($challenge, 201);
    }

    public function update(Request $request, $anakId, $challengeId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeChallengeInput($request->all());

        $rules = [
            'challenge_category' => 'nullable|string|max:100',
            'challenge_title' => 'nullable|string|max:255',
            'challenge_emoji' => 'nullable|string|max:10',
            'challenge_points' => 'nullable|integer|min:0',
            'challenge_status' => 'nullable|string|in:pending,completed,cancelled',
            'challenge_date' => 'nullable|string|max:50',
            'challenge_meta' => 'nullable|array',
        ];
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $challenge = Challenge::where('challenge_id', $challengeId)->where('challenge_id_anak', $anakId)->firstOrFail();

        $meta = $data['challenge_meta'] ?? null;
        if (! $meta) {
            $meta = $challenge->challenge_meta ?? [];
            if ($request->has('maxPoints')) {
                $meta['maxPoints'] = $request->input('maxPoints');
            }
            if ($request->has('color')) {
                $meta['color'] = $request->input('color');
            }
            if ($request->has('bg')) {
                $meta['bg'] = $request->input('bg');
            }
            if ($request->has('notes')) {
                $meta['notes'] = $request->input('notes');
            }
            $data['challenge_meta'] = $meta;
        }

        $challenge->update($data);

        return response()->json($challenge);
    }

    public function destroy(Request $request, $anakId, $challengeId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        Challenge::where('challenge_id', $challengeId)->where('challenge_id_anak', $anakId)->delete();

        return response()->json(null, 204);
    }
}
