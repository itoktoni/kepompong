<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\MasterWorksheet;
use App\Models\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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

    public function getTypes(Request $request)
    {
        $planId = $request->query('plan_id');

        $worksheets = MasterWorksheet::where('worksheet_active', true)
            ->orderBy('worksheet_sort_order')
            ->get()
            ->filter(function ($w) use ($planId) {
                if (! $planId) return true;
                if (empty($w->worksheet_plans)) return true;
                return in_array((int) $planId, $w->worksheet_plans);
            })
            ->values()
            ->map(function ($w) {
                return [
                    'id' => $w->worksheet_key,
                    'icon' => $w->worksheet_icon,
                    'title' => $w->worksheet_title,
                    'desc' => $w->worksheet_desc,
                    'age' => $w->worksheet_age,
                    'ageLabel' => $w->worksheet_age_label,
                    'ages' => $w->worksheet_ages ?? [],
                    'skills' => $w->worksheet_skills ?? [],
                    'agama' => $w->worksheet_agama ?? [],
                    'plans' => $w->worksheet_plans ?? [],
                    'bg' => $w->worksheet_bg,
                    'iconColor' => $w->worksheet_icon_color,
                    'isApi' => (bool) $w->worksheet_is_api,
                ];
            });

        return response()->json($worksheets);
    }

    public function xgetDownloadUrl(Request $request, string $worksheetKey)
    {
        $safeKey = basename($worksheetKey);
        $path = "worksheets/{$safeKey}.pdf";

        // if (! Storage::disk('public')->exists($path)) {
        //     $safeKey = 'sample';
        //     $path = "worksheets/sample.pdf";
        // }

        if (! Storage::disk('public')->exists($path)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $url = URL::temporarySignedRoute(
            'worksheet.download.file',
            now()->addMinutes(10),
            ['worksheetKey' => $safeKey]
        );

        return response()->json(['url' => $url]);
    }

    public function getDownloadFile(Request $request, string $worksheetKey)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $safeKey = basename($worksheetKey);
        $fullPath = storage_path("app/worksheets/{$safeKey}.pdf");

        if (! file_exists($fullPath)) {
            $safeKey = 'sample';
            $fullPath = storage_path('app/worksheets/sample.pdf');
        }

        if (! file_exists($fullPath)) {
            abort(404, 'File not found.');
        }

        return response()->download($fullPath, "{$safeKey}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
