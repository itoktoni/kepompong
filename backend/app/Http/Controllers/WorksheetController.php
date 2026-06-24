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
                    'creatorId' => $w->worksheet_creator_id,
                    'addonId' => $w->worksheet_addon_id,
                ];
            });

        return response()->json($worksheets);
    }

    public function xgetDownloadUrl(Request $request, string $worksheetKey)
    {
        $safeKey = basename($worksheetKey);
        $found = $this->findWorksheetFile($safeKey);

        if (! $found) {
            return response()->json(['message' => 'File not found'], 404);
        }

        $url = URL::temporarySignedRoute(
            'worksheet.download.file',
            now()->addMinutes(10),
            ['worksheetKey' => $safeKey]
        );

        return response()->json(['url' => $url, 'ext' => $found['ext']]);
    }

    public function getDownloadFile(Request $request, string $worksheetKey)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }

        $safeKey = basename($worksheetKey);
        $found = $this->findWorksheetFile($safeKey);

        if (! $found) {
            $found = $this->findWorksheetFile('sample');
        }

        if (! $found) {
            abort(404, 'File not found.');
        }

        $mime = match ($found['ext']) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        return response()->download($found['path'], "{$safeKey}.{$found['ext']}", [
            'Content-Type' => $mime,
        ]);
    }

    private function findWorksheetFile(string $key): ?array
    {
        $extensions = ['pdf', 'png', 'jpg', 'jpeg', 'webp'];
        $folders = [
            storage_path('app/worksheets'),
            storage_path('app/public/worksheets'),
        ];

        $variants = array_unique([
            $key,
            str_replace('_', ' ', $key),
            str_replace('-', ' ', $key),
        ]);

        foreach ($folders as $folder) {
            if (!is_dir($folder)) continue;

            // Try exact match first
            foreach ($variants as $variant) {
                foreach ($extensions as $ext) {
                    $path = "{$folder}/{$variant}.{$ext}";
                    if (file_exists($path)) {
                        return ['path' => $path, 'ext' => $ext, 'folder' => $folder];
                    }
                }
            }

            // Case-insensitive scan fallback
            $files = scandir($folder);
            foreach ($files as $file) {
                $fileNoExt = pathinfo($file, PATHINFO_FILENAME);
                $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));

                if (!in_array($fileExt, $extensions)) continue;

                foreach ($variants as $variant) {
                    if (strcasecmp($fileNoExt, $variant) === 0) {
                        return ['path' => "{$folder}/{$file}", 'ext' => $fileExt, 'folder' => $folder];
                    }
                }
            }
        }

        return null;
    }
}
