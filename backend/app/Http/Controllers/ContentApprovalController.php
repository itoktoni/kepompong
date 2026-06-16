<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Pilar;
use App\Models\Skill;
use App\Models\Worksheet;
use App\StatusEnum;
use Illuminate\Http\Request;

class ContentApprovalController extends Controller
{
    public function xgetPending(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'developer' && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $type = $request->query('type');

        $result = [];

        if (!$type || $type === 'pilar') {
            $result['pilars'] = Pilar::where('pilar_status', StatusEnum::PENDING->value)
                ->orWhere('pilar_status', StatusEnum::REVIEW->value)
                ->orderBy('pilar_id', 'desc')
                ->get()
                ->map(fn ($p) => $p->toArray());
        }

        if (!$type || $type === 'skill') {
            $result['skills'] = Skill::where('skill_status', StatusEnum::PENDING->value)
                ->orWhere('skill_status', StatusEnum::REVIEW->value)
                ->orderBy('skill_id', 'desc')
                ->get()
                ->map(fn ($s) => $s->toArray());
        }

        if (!$type || $type === 'activity') {
            $result['activities'] = Activity::where('status', StatusEnum::PENDING->value)
                ->orWhere('status', StatusEnum::REVIEW->value)
                ->orderBy('id', 'desc')
                ->get()
                ->map(fn ($a) => [
                    'id' => $a->id,
                    'type' => $a->type,
                    'title' => $a->title,
                    'slug' => $a->slug,
                    'desc' => $a->desc,
                    'image' => $a->image,
                    'status' => $a->status,
                    'created_by' => $a->created_by,
                    'creator' => $a->creator,
                    'views' => $a->views,
                ]);
        }

        if (!$type || $type === 'worksheet') {
            $result['worksheets'] = Worksheet::where('worksheet_status', StatusEnum::PENDING->value)
                ->orWhere('worksheet_status', StatusEnum::REVIEW->value)
                ->orderBy('worksheet_id', 'desc')
                ->get()
                ->toArray();
        }

        return response()->json($result);
    }

    public function xputApprove(Request $request, string $type, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'developer' && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $this->updateStatus($type, $id, StatusEnum::APPROVED->value);
    }

    public function xputReject(Request $request, string $type, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'developer' && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $this->updateStatus($type, $id, StatusEnum::REJECTED->value);
    }

    public function xputReview(Request $request, string $type, int $id)
    {
        $user = $request->user();
        if ($user->role !== 'developer' && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $this->updateStatus($type, $id, StatusEnum::REVIEW->value);
    }

    private function updateStatus(string $type, int $id, string $status): \Illuminate\Http\JsonResponse
    {
        switch ($type) {
            case 'pilar':
                $item = Pilar::find($id);
                if (!$item) return response()->json(['message' => 'Pilar not found'], 404);
                $item->update(['pilar_status' => $status]);
                return response()->json(['message' => 'Status updated', 'status' => $status]);

            case 'skill':
                $item = Skill::find($id);
                if (!$item) return response()->json(['message' => 'Skill not found'], 404);
                $item->update(['skill_status' => $status]);
                return response()->json(['message' => 'Status updated', 'status' => $status]);

            case 'activity':
                $item = Activity::find($id);
                if (!$item) return response()->json(['message' => 'Activity not found'], 404);
                $item->update(['status' => $status]);
                return response()->json(['message' => 'Status updated', 'status' => $status]);

            case 'worksheet':
                $item = Worksheet::find($id);
                if (!$item) return response()->json(['message' => 'Worksheet not found'], 404);
                $item->update(['worksheet_status' => $status]);
                return response()->json(['message' => 'Status updated', 'status' => $status]);

            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    }
}
