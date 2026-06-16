<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\ChallengeHistory;
use Illuminate\Http\Request;

class ChallengeHistoryController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeChallengeHistoryInput($request->all());

        $rules = [
            'challenge_history_category' => 'required|string|max:100',
            'challenge_history_title' => 'required|string|max:255',
            'challenge_history_date' => 'nullable|string|max:50',
            'challenge_history_meta' => 'nullable|array',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $history = ChallengeHistory::create([
            'challenge_history_id_anak' => $anakId,
            ...$data,
        ]);

        return response()->json($history, 201);
    }
}
