<?php

namespace App\Models;

use App\Models\BaseModel;

class Anak extends BaseModel
{
    protected $table = 'anak';
    protected $keyType = 'int';
    protected $primaryKey = 'anak_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'anak_id' => 'Id',
        'anak_id_user' => 'User',
        'anak_nama' => 'Nama',
        'anak_gender' => 'Gender',
        'anak_umur' => 'Umur',
        'anak_tahun_lahir' => 'Tahun Lahir',
        'anak_emoji' => 'Emoji',
        'anak_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'anak_id',
        'anak_id_user',
        'anak_nama',
        'anak_gender',
        'anak_umur',
        'anak_tahun_lahir',
        'anak_created_at',
    ];

    protected $fillable = [
        'anak_id_user',
        'anak_nama',
        'anak_gender',
        'anak_agama',
        'anak_umur',
        'anak_tanggal_lahir',
        'anak_bulan_lahir',
        'anak_tahun_lahir',
        'anak_emoji',
        'anak_avatar',
        'anak_settings',
        'anak_created_at',
        'anak_updated_at',
    ];

    protected $casts = [
        'anak_settings' => 'array',
    ];

    public function rules(): array
    {
        return [
            'anak_nama' => 'required|string|max:255',
            'anak_id_user' => 'required',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->anak_id,
            'anak_id_user' => $this->anak_id_user,
            'nama' => $this->anak_nama,
            'gender' => $this->anak_gender,
            'agama' => $this->anak_agama,
            'umur' => $this->anak_umur,
            'tanggal_lahir' => $this->anak_tanggal_lahir,
            'bulan_lahir' => $this->anak_bulan_lahir,
            'tahun_lahir' => $this->anak_tahun_lahir,
            'emoji' => $this->anak_emoji,
            'avatar' => $this->anak_avatar,
            'settings' => $this->anak_settings,
            'skills' => $this->relationLoaded('has_skills') ? $this->getRelation('has_skills') : [],
            'completed_skills' => $this->relationLoaded('has_completed_skills') ? $this->getRelation('has_completed_skills') : [],
            'challenges' => $this->relationLoaded('has_challenges') ? $this->getRelation('has_challenges') : [],
            'challenge_histories' => $this->relationLoaded('has_challenge_histories') ? $this->getRelation('has_challenge_histories') : [],
            'checklists' => $this->relationLoaded('has_checklists') ? $this->getRelation('has_checklists') : [],
            'schedules' => $this->relationLoaded('has_schedules') ? $this->getRelation('has_schedules') : [],
            'worksheets' => $this->relationLoaded('has_worksheets') ? $this->getRelation('has_worksheets') : [],
            'schedule_histories' => $this->relationLoaded('has_schedule_histories') ? $this->getRelation('has_schedule_histories') : [],
        ];
    }

    public static function field_name()
    {
        return 'anak_nama';
    }

    public function has_skills()
    {
        return $this->hasMany(Skill::class, 'skill_id_anak');
    }

    public function has_completed_skills()
    {
        return $this->hasMany(CompletedSkill::class, 'completed_skill_id_anak');
    }

    public function has_challenges()
    {
        return $this->hasMany(Challenge::class, 'challenge_id_anak');
    }

    public function has_challenge_histories()
    {
        return $this->hasMany(ChallengeHistory::class, 'challenge_history_id_anak');
    }

    public function has_checklists()
    {
        return $this->hasMany(Checklist::class, 'checklist_id_anak');
    }

    public function has_schedules()
    {
        return $this->hasMany(Schedule::class, 'schedule_id_anak');
    }

    public function has_worksheets()
    {
        return $this->hasMany(Worksheet::class, 'worksheet_id_anak');
    }

    public function has_evaluations()
    {
        return $this->hasMany(Evaluation::class, 'evaluation_id_anak');
    }

    public function has_schedule_histories()
    {
        return $this->hasMany(ScheduleHistory::class, 'schedule_history_id_anak');
    }
}
