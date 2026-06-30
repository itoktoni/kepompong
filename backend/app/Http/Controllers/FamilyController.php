<?php

namespace App\Http\Controllers;

use App\Models\FamilyRequest;
use App\Models\User;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    public function xpostRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'label' => 'nullable|string|max:50',
        ]);

        $user = $request->user();
        $email = strtolower(trim($request->email));

        if ($email === strtolower($user->email)) {
            return response()->json(['message' => 'Tidak bisa mengirim permintaan ke diri sendiri'], 422);
        }

        $target = User::where('email', $email)->first();
        if (! $target) {
            return response()->json(['message' => 'Email tidak ditemukan'], 422);
        }

        $existingFamily = $user->family ?? [];
        if (in_array($target->id, $existingFamily)) {
            return response()->json(['message' => 'Sudah tergabung dalam keluarga ini'], 422);
        }

        $pending = FamilyRequest::where('family_request_id_user', $user->id)
            ->where('family_request_id_target', $target->id)
            ->where('family_request_status', 'pending')
            ->first();

        if ($pending) {
            return response()->json(['message' => 'Permintaan sudah dikirim, menunggu persetujuan'], 422);
        }

        $pendingReverse = FamilyRequest::where('family_request_id_user', $target->id)
            ->where('family_request_id_target', $user->id)
            ->where('family_request_status', 'pending')
            ->first();

        if ($pendingReverse) {
            return response()->json(['message' => 'User ini sudah mengirim permintaan ke Anda, silakan approve'], 422);
        }

        $fr = FamilyRequest::create([
            'family_request_id_user' => $user->id,
            'family_request_id_target' => $target->id,
            'family_request_email' => $email,
            'family_request_status' => 'pending',
            'family_request_label' => $request->label,
            'family_request_created_at' => now(),
            'family_request_updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Permintaan berhasil dikirim',
            'request' => $fr->load(['has_user', 'has_target']),
        ], 201);
    }

    public function xgetIncoming(Request $request)
    {
        $user = $request->user();

        $requests = FamilyRequest::where('family_request_id_target', $user->id)
            ->where('family_request_status', 'pending')
            ->with(['has_user'])
            ->orderByDesc('family_request_created_at')
            ->get();

        return response()->json(['requests' => $requests]);
    }

    public function xgetSent(Request $request)
    {
        $user = $request->user();

        $requests = FamilyRequest::where('family_request_id_user', $user->id)
            ->with(['has_target'])
            ->orderByDesc('family_request_created_at')
            ->get();

        return response()->json(['requests' => $requests]);
    }

    public function xputApprove(Request $request, $id)
    {
        $user = $request->user();

        $fr = FamilyRequest::where('family_request_id', $id)
            ->where('family_request_id_target', $user->id)
            ->where('family_request_status', 'pending')
            ->first();

        if (! $fr) {
            return response()->json(['message' => 'Permintaan tidak ditemukan'], 404);
        }

        $fr->update([
            'family_request_status' => 'approved',
            'family_request_updated_at' => now(),
        ]);

        $ownerId = $fr->family_request_id_target;
        $memberId = $fr->family_request_id_user;

        $owner = User::find($ownerId);
        $family = $owner->family ?? [];
        if (! in_array($memberId, $family)) {
            $family[] = $memberId;
            $owner->update(['family' => $family]);
        }

        return response()->json([
            'message' => 'Permintaan disetujui',
            'family' => $family,
        ]);
    }

    public function xputReject(Request $request, $id)
    {
        $user = $request->user();

        $fr = FamilyRequest::where('family_request_id', $id)
            ->where('family_request_id_target', $user->id)
            ->where('family_request_status', 'pending')
            ->first();

        if (! $fr) {
            return response()->json(['message' => 'Permintaan tidak ditemukan'], 404);
        }

        $fr->update([
            'family_request_status' => 'rejected',
            'family_request_updated_at' => now(),
        ]);

        return response()->json(['message' => 'Permintaan ditolak']);
    }

    public function xgetMembers(Request $request)
    {
        $user = $request->user();
        $familyIds = $user->family ?? [];

        $members = [];
        if (! empty($familyIds)) {
            $members = User::whereIn('id', $familyIds)
                ->select('id', 'name', 'email', 'phone')
                ->get()
                ->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'phone' => $u->phone,
                ]);
        }

        $sharedWithMe = User::whereJsonContains('family', $user->id)
            ->select('id', 'name', 'email', 'phone')
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'phone' => $u->phone,
            ]);

        return response()->json([
            'members' => $members,
            'shared_with_me' => $sharedWithMe,
        ]);
    }

    public function xdeleteRemove(Request $request, $memberId)
    {
        $user = $request->user();
        $family = $user->family ?? [];

        $key = array_search((int) $memberId, $family);
        if ($key === false) {
            $key = array_search((string) $memberId, $family);
        }
        if ($key === false) {
            return response()->json(['message' => 'Anggota tidak ditemukan'], 404);
        }

        unset($family[$key]);
        $family = array_values($family);
        $user->update(['family' => $family]);

        FamilyRequest::where('family_request_id_user', $memberId)
            ->where('family_request_id_target', $user->id)
            ->where('family_request_status', 'approved')
            ->update(['family_request_status' => 'removed', 'family_request_updated_at' => now()]);

        return response()->json([
            'message' => 'Anggota dihapus dari keluarga',
            'family' => $family,
        ]);
    }
}
