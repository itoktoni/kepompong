<?php

namespace App\Models;

use App\Models\BaseModel;

class ScheduleHistory extends BaseModel
{
    protected $table = 'schedule_histories';
    protected $keyType = 'int';
    protected $primaryKey = 'schedule_history_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'schedule_history_id' => 'Id',
        'schedule_history_id_schedule' => 'Schedule',
        'schedule_history_id_anak' => 'Anak',
        'schedule_history_date' => 'Date',
        'schedule_history_time' => 'Time',
        'schedule_history_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'schedule_history_id',
        'schedule_history_id_schedule',
        'schedule_history_id_anak',
        'schedule_history_date',
        'schedule_history_time',
        'schedule_history_created_at',
    ];

    protected $fillable = [
        'schedule_history_id_schedule',
        'schedule_history_id_anak',
        'schedule_history_date',
        'schedule_history_time',
        'schedule_history_created_at',
    ];

    protected $casts = [
        'schedule_history_date' => 'date',
    ];

    public function rules(): array
    {
        return [
            'schedule_history_id_schedule' => 'required',
            'schedule_history_id_anak' => 'required',
            'schedule_history_date' => 'required|date',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->schedule_history_id,
            'schedule_id' => $this->schedule_history_id_schedule,
            'anak_id' => $this->schedule_history_id_anak,
            'date' => $this->schedule_history_date,
            'time' => $this->schedule_history_time,
            'created_at' => $this->schedule_history_created_at,
        ];
    }

    public static function field_name()
    {
        return 'schedule_history_date';
    }

    public function has_schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_history_id_schedule', 'schedule_id');
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'schedule_history_id_anak', 'anak_id');
    }
}
