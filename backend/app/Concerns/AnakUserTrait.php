<?php

namespace App\Concerns;

use App\Models\Anak;
use Illuminate\Http\Request;

trait AnakUserTrait
{
    protected function getUserAnakIds(Request $request): array
    {
        $user = $request->user();
        if (! $user) {
            return [];
        }

        return Anak::where('anak_id_user', $user->id)->pluck('anak_id')->toArray();
    }

    protected function authorizeAnak(Request $request, int $anakId): bool
    {
        $ids = $this->getUserAnakIds($request);

        return in_array($anakId, $ids);
    }

    protected function unauthorized()
    {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
}
