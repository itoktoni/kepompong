<?php

namespace App\Models;

use App\Models\BaseModel;

class Skill extends BaseModel
{
    protected $table = 'skills_anak';
    protected $keyType = 'int';
    protected $primaryKey = 'skill_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'skill_id' => 'Id',
        'skill_id_anak' => 'Anak',
        'skill_key' => 'Key',
        'skill_title' => 'Title',
        'skill_status' => 'Status',
    ];

    public static $sortColumns = [
        'skill_id',
        'skill_id_anak',
        'skill_key',
        'skill_title',
        'skill_status',
    ];

    protected $fillable = [
        'skill_id_anak',
        'skill_key',
        'skill_emoji',
        'skill_title',
        'skill_pilar',
        'skill_progress',
        'skill_color',
        'skill_status',
    ];

    public function toArray()
    {
        return [
            'id' => $this->skill_id,
            'key' => $this->skill_key,
            'emoji' => $this->skill_emoji,
            'title' => $this->skill_title,
            'pilar' => $this->skill_pilar,
            'progress' => $this->skill_progress,
            'color' => $this->skill_color,
            'status' => $this->skill_status,
            'activities' => $this->relationLoaded('has_activities') ? $this->getRelation('has_activities') : [],
        ];
    }

    public static function field_name()
    {
        return 'skill_title';
    }

    public function has_activities()
    {
        return $this->hasMany(SkillActivity::class, 'skill_activity_id_skill');
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'skill_id_anak', 'anak_id');
    }
}
