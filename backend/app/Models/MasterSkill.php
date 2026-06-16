<?php

namespace App\Models;

use App\Models\BaseModel;

class MasterSkill extends BaseModel
{
    protected $table = 'skills';
    protected $keyType = 'int';
    protected $primaryKey = 'skill_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'skill_id' => 'Id',
        'skill_key' => 'Key',
        'skill_title' => 'Title',
    ];

    public static $sortColumns = [
        'skill_id',
        'skill_key',
        'skill_title',
        'skill_sort_order',
    ];

    protected $fillable = [
        'skill_key',
        'skill_emoji',
        'skill_title',
        'skill_desc',
        'skill_ages',
        'skill_pilars',
        'skill_agama',
        'skill_plans',
        'skill_evaluasi',
        'skill_color',
        'skill_bg',
        'skill_sort_order',
        'skill_active',
    ];

    protected $casts = [
        'skill_ages' => 'array',
        'skill_pilars' => 'array',
        'skill_agama' => 'array',
        'skill_plans' => 'array',
        'skill_evaluasi' => 'array',
        'skill_active' => 'boolean',
    ];

    public function rules(): array
    {
        return [
            'skill_key' => 'required|string|max:80|unique:skills,skill_key',
            'skill_title' => 'required|string',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->skill_id,
            'key' => $this->skill_key,
            'emoji' => $this->skill_emoji,
            'title' => $this->skill_title,
            'desc' => $this->skill_desc,
            'ages' => $this->skill_ages ?? [],
            'pilars' => $this->skill_pilars ?? [],
            'agama' => $this->skill_agama ?? [],
            'plans' => $this->skill_plans ?? [],
            'evaluasi' => $this->skill_evaluasi ?? [],
            'color' => $this->skill_color,
            'bg' => $this->skill_bg,
            'sort_order' => $this->skill_sort_order,
            'active' => (bool) $this->skill_active,
        ];
    }

    public static function field_name()
    {
        return 'skill_title';
    }
}
