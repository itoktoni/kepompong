<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Addon;
use App\Models\MasterWorksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AddonController extends Controller
{
    private function isPurchased(Addon $addon, int $userId): bool
    {
        return in_array($userId, $addon->addon_buyers ?? []);
    }

    public function xgetIndex(Request $request)
    {
        $userId = Auth::id();
        $addons = Addon::where('addon_active', true)
            ->with('has_user:id,name')
            ->orderBy('addon_created_at', 'desc')
            ->get()
            ->map(function ($a) use ($userId) {
                return [
                    'id' => $a->addon_id,
                    'creator_id' => $a->addon_id_user,
                    'creator_name' => $a->has_user?->name,
                    'nama' => $a->addon_nama,
                    'desc' => $a->addon_desc,
                    'harga' => $a->addon_harga,
                    'ages' => $a->addon_ages ?? [],
                    'agama' => $a->addon_agama ?? [],
                    'plans' => $a->addon_plans ?? [],
                    'bg' => $a->addon_bg,
                    'icon' => $a->addon_icon,
                    'purchased' => $this->isPurchased($a, $userId),
                    'activity_count' => $a->has_activities()->count(),
                    'created_at' => $a->addon_created_at,
                ];
            });

        return response()->json($addons);
    }

    public function xgetMyAddons(Request $request)
    {
        $userId = Auth::id();
        $addons = Addon::where('addon_id_user', $userId)
            ->orderBy('addon_created_at', 'desc')
            ->get()
            ->map(function ($a) {
                return [
                    'id' => $a->addon_id,
                    'nama' => $a->addon_nama,
                    'desc' => $a->addon_desc,
                    'harga' => $a->addon_harga,
                    'ages' => $a->addon_ages ?? [],
                    'agama' => $a->addon_agama ?? [],
                    'bg' => $a->addon_bg,
                    'icon' => $a->addon_icon,
                    'active' => $a->addon_active,
                    'activity_count' => Activity::where('addon_id', $a->addon_id)->count(),
                    'buyer_count' => count($a->addon_buyers ?? []),
                    'created_at' => $a->addon_created_at,
                ];
            });

        return response()->json($addons);
    }

    public function xpostCreate(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'harga' => 'required|integer|min:0',
            'ages' => 'nullable|array',
            'agama' => 'nullable|array',
            'bg' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:80',
        ]);

        $addon = Addon::create([
            'addon_id_user' => $user->id,
            'addon_nama' => $data['nama'],
            'addon_desc' => $data['desc'] ?? null,
            'addon_harga' => $data['harga'],
            'addon_ages' => $data['ages'] ?? null,
            'addon_agama' => $data['agama'] ?? null,
            'addon_bg' => $data['bg'] ?? '#E8F5E9',
            'addon_icon' => $data['icon'] ?? '📦',
            'addon_buyers' => [],
            'addon_active' => true,
            'addon_created_at' => now(),
        ]);

        return response()->json([
            'id' => $addon->addon_id,
            'nama' => $addon->addon_nama,
            'harga' => $addon->addon_harga,
        ], 201);
    }

    public function xputUpdate(Request $request, int $addonId)
    {
        $user = Auth::user();
        $addon = Addon::where('addon_id', $addonId)
            ->where('addon_id_user', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'desc' => 'nullable|string',
            'harga' => 'sometimes|integer|min:0',
            'ages' => 'nullable|array',
            'agama' => 'nullable|array',
            'bg' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:80',
            'active' => 'sometimes|boolean',
        ]);

        $update = [];
        if (isset($data['nama'])) $update['addon_nama'] = $data['nama'];
        if (array_key_exists('desc', $data)) $update['addon_desc'] = $data['desc'];
        if (isset($data['harga'])) $update['addon_harga'] = $data['harga'];
        if (array_key_exists('ages', $data)) $update['addon_ages'] = $data['ages'];
        if (array_key_exists('agama', $data)) $update['addon_agama'] = $data['agama'];
        if (isset($data['bg'])) $update['addon_bg'] = $data['bg'];
        if (isset($data['icon'])) $update['addon_icon'] = $data['icon'];
        if (isset($data['active'])) $update['addon_active'] = $data['active'];
        $update['addon_updated_at'] = now();

        $addon->update($update);

        return response()->json(['message' => 'Updated']);
    }

    public function xdeleteDestroy(Request $request, int $addonId)
    {
        $user = Auth::user();
        Addon::where('addon_id', $addonId)
            ->where('addon_id_user', $user->id)
            ->delete();

        return response()->json(null, 204);
    }

    public function xpostPurchase(Request $request, int $addonId)
    {
        $user = Auth::user();
        $addon = Addon::where('addon_id', $addonId)
            ->where('addon_active', true)
            ->firstOrFail();

        $buyers = $addon->addon_buyers ?? [];
        if (in_array($user->id, $buyers)) {
            return response()->json(['message' => 'Already purchased'], 409);
        }

        $buyers[] = $user->id;
        $addon->update(['addon_buyers' => $buyers]);

        return response()->json([
            'addon_id' => $addon->addon_id,
            'nama' => $addon->addon_nama,
        ], 201);
    }

    public function xgetPurchased(Request $request)
    {
        $userId = Auth::id();
        $addons = Addon::where('addon_active', true)
            ->get()
            ->filter(fn ($a) => in_array($userId, $a->addon_buyers ?? []))
            ->values()
            ->map(function ($a) {
                return [
                    'addon_id' => $a->addon_id,
                    'nama' => $a->addon_nama,
                    'icon' => $a->addon_icon,
                    'harga' => $a->addon_harga,
                ];
            });

        return response()->json($addons);
    }

    public function xpostCreateActivity(Request $request, int $addonId)
    {
        $user = Auth::user();
        $addon = Addon::where('addon_id', $addonId)
            ->where('addon_id_user', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'type' => 'required|string|max:100',
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'image' => 'nullable|string',
            'moral' => 'nullable|string',
            'ages' => 'nullable|array',
            'skills' => 'nullable|array',
            'data' => 'nullable|array',
        ]);

        $slug = Str::slug($data['title']) . '-' . Str::random(5);

        $activity = Activity::create([
            'type' => $data['type'],
            'title' => $data['title'],
            'slug' => $slug,
            'desc' => $data['desc'] ?? null,
            'image' => $data['image'] ?? null,
            'moral' => $data['moral'] ?? null,
            'ages' => $data['ages'] ?? null,
            'skills' => $data['skills'] ?? null,
            'data' => $data['data'] ?? null,
            'addon_id' => $addon->addon_id,
            'created_by' => $user->id,
            'creator' => $user->name,
            'status' => 'pending',
            'active' => true,
            'sort_order' => 0,
        ]);

        return response()->json([
            'id' => $activity->id,
            'title' => $activity->title,
            'slug' => $activity->slug,
        ], 201);
    }

    public function xgetAddonActivities(Request $request, int $addonId)
    {
        $userId = Auth::id();
        $addon = Addon::findOrFail($addonId);

        if ($addon->addon_id_user !== $userId && ! $this->isPurchased($addon, $userId)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        return response()->json(
            Activity::where('addon_id', $addonId)->where('active', true)->get()
        );
    }

    public function xpostCreateWorksheet(Request $request, int $addonId)
    {
        $user = Auth::user();
        $addon = Addon::where('addon_id', $addonId)
            ->where('addon_id_user', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'key' => 'required|string|max:80',
            'title' => 'required|string|max:255',
            'desc' => 'nullable|string',
            'ages' => 'nullable|array',
            'agama' => 'nullable|array',
            'bg' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:80',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $ages = $data['ages'] ?? [];
        if (isset($ages[0]) && is_string($ages[0])) {
            $ages = array_map(function ($a) {
                $decoded = json_decode($a, true);
                return is_array($decoded) ? $decoded : [$a];
            }, $ages);
        }

        if ($request->hasFile('file')) {
            $safeKey = basename($data['key']);
            $request->file('file')->storeAs('worksheets', $safeKey . '.pdf', 'local');
        }

        $worksheet = MasterWorksheet::create([
            'worksheet_key' => $data['key'],
            'worksheet_title' => $data['title'],
            'worksheet_desc' => $data['desc'] ?? null,
            'worksheet_ages' => $ages ?: null,
            'worksheet_agama' => $data['agama'] ?? null,
            'worksheet_bg' => $data['bg'] ?? '#E8F5E9',
            'worksheet_icon' => $data['icon'] ?? '📝',
            'worksheet_creator_id' => $user->id,
            'worksheet_addon_id' => $addon->addon_id,
            'worksheet_active' => true,
            'worksheet_sort_order' => 0,
        ]);

        return response()->json([
            'id' => $worksheet->worksheet_id,
            'key' => $worksheet->worksheet_key,
            'title' => $worksheet->worksheet_title,
        ], 201);
    }

    public function xgetAddonWorksheets(Request $request, int $addonId)
    {
        $userId = Auth::id();
        $addon = Addon::findOrFail($addonId);

        if ($addon->addon_id_user !== $userId && ! $this->isPurchased($addon, $userId)) {
            return response()->json(['message' => 'Not authorized'], 403);
        }

        return response()->json(
            MasterWorksheet::where('worksheet_addon_id', $addonId)
                ->where('worksheet_active', true)
                ->get()
        );
    }
}
