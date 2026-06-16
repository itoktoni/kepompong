<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Checklist;
use Illuminate\Http\Request;

class ChecklistController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeChecklistInput($request->all());

        $rules = [
            'checklist_title' => 'required|string|max:255',
            'checklist_items' => 'nullable|array',
            'checklist_date' => 'nullable|string|max:50',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $checklist = Checklist::create([
            'checklist_id_anak' => $anakId,
            ...$data,
        ]);

        return response()->json($checklist, 201);
    }

    public function update(Request $request, $anakId, $checklistId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeChecklistInput($request->all());

        $rules = [
            'checklist_title' => 'nullable|string|max:255',
            'checklist_items' => 'nullable|array',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $checklist = Checklist::where('checklist_id', $checklistId)->where('checklist_id_anak', $anakId)->firstOrFail();
        $checklist->update($data);

        return response()->json($checklist);
    }

    public function destroy(Request $request, $anakId, $checklistId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        Checklist::where('checklist_id', $checklistId)->where('checklist_id_anak', $anakId)->delete();

        return response()->json(null, 204);
    }
}
