<?php

namespace App\Models;

use App\Models\BaseModel;

class Schedule extends BaseModel
{
    protected $table = 'schedules';
    protected $keyType = 'int';
    protected $primaryKey = 'schedule_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'schedule_id' => 'Id',
        'schedule_id_anak' => 'Anak',
        'schedule_label' => 'Label',
        'schedule_time' => 'Time',
        'schedule_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'schedule_id',
        'schedule_id_anak',
        'schedule_label',
        'schedule_time',
        'schedule_created_at',
    ];

    protected $fillable = [
        'schedule_id_anak',
        'schedule_label',
        'schedule_time',
        'schedule_created_at',
    ];

    protected $casts = [];

    public function rules(): array
    {
        return [
            'schedule_id_anak' => 'required',
            'schedule_label' => 'required|string|max:255',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->schedule_id,
            'anak_id' => $this->schedule_id_anak,
            'label' => $this->schedule_label,
            'time' => $this->schedule_time,
            'created_at' => $this->schedule_created_at,
        ];
    }

    public static function field_name()
    {
        return 'schedule_label';
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'schedule_id_anak', 'anak_id');
    }

    public function has_histories()
    {
        return $this->hasMany(ScheduleHistory::class, 'schedule_history_id_schedule');
    }
}
