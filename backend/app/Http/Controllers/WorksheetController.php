<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Worksheet;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeWorksheetInput($request->all());

        $rules = [
            'worksheet_type' => 'required|string|max:50',
            'worksheet_data' => 'nullable|array',
            'worksheet_date' => 'nullable|string|max:50',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $worksheet = Worksheet::create([
            'worksheet_id_anak' => $anakId,
            ...$data,
            'worksheet_status' => 'pending',
        ]);

        return response()->json($worksheet, 201);
    }

    public function destroy(Request $request, $anakId, $worksheetId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        Worksheet::where('worksheet_id', $worksheetId)->where('worksheet_id_anak', $anakId)->delete();

        return response()->json(null, 204);
    }
}
