<?php

namespace App\Http\Controllers;

use App\Concerns\AnakUserTrait;
use App\Concerns\NormalizeInputTrait;
use App\Models\Schedule;
use App\Models\ScheduleHistory;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use AnakUserTrait, NormalizeInputTrait;

    public function index(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $schedules = Schedule::where('schedule_id_anak', $anakId)->get();

        return response()->json($schedules->map->toArray());
    }

    public function store(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeScheduleInput($request->all());

        $rules = [
            'schedule_label' => 'required|string|max:255',
            'schedule_time' => 'nullable|string|max:20',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $schedule = Schedule::create([
            'schedule_id_anak' => $anakId,
            'schedule_created_at' => now()->toDateTimeString(),
            ...$data,
        ]);

        return response()->json($schedule->toArray(), 201);
    }

    public function update(Request $request, $anakId, $scheduleId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $data = $this->normalizeScheduleInput($request->all());

        $rules = [
            'schedule_label' => 'nullable|string|max:255',
            'schedule_time' => 'nullable|string|max:20',
        ];
        $validator = \Illuminate\Support\Facades\Validator::make($data, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'code' => 422, 'message' => 'The given data was invalid.', 'data' => $validator->errors()], 422);
        }
        $data = $validator->validated();

        $schedule = Schedule::where('schedule_id', $scheduleId)->where('schedule_id_anak', $anakId)->firstOrFail();
        $schedule->update($data);

        return response()->json($schedule->toArray());
    }

    public function destroy(Request $request, $anakId, $scheduleId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        Schedule::where('schedule_id', $scheduleId)->where('schedule_id_anak', $anakId)->delete();
        ScheduleHistory::where('schedule_history_id_schedule', $scheduleId)->delete();

        return response()->json(null, 204);
    }

    public function toggleDone(Request $request, $anakId, $scheduleId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $schedule = Schedule::where('schedule_id', $scheduleId)->where('schedule_id_anak', $anakId)->firstOrFail();
        $date = $request->input('date', now()->toDateString());
        $time = $request->input('time', now()->format('H:i'));

        $existing = ScheduleHistory::where('schedule_history_id_schedule', $scheduleId)
            ->where('schedule_history_date', $date)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['done' => false, 'date' => $date]);
        }

        $history = ScheduleHistory::create([
            'schedule_history_id_schedule' => $scheduleId,
            'schedule_history_id_anak' => $anakId,
            'schedule_history_date' => $date,
            'schedule_history_time' => $time,
            'schedule_history_created_at' => now()->toDateTimeString(),
        ]);

        return response()->json($history->toArray());
    }

    public function xgetHistories(Request $request, $anakId)
    {
        if (! $this->authorizeAnak($request, (int) $anakId)) {
            return $this->unauthorized();
        }

        $date = $request->input('date', now()->toDateString());

        $histories = ScheduleHistory::where('schedule_history_id_anak', $anakId)
            ->where('schedule_history_date', $date)
            ->get();

        return response()->json(['histories' => $histories->map->toArray(), 'date' => $date]);
    }
}
