<?php

namespace App\Models;

use App\Models\BaseModel;

class MasterWorksheet extends BaseModel
{
    protected $table = 'worksheets';
    protected $keyType = 'int';
    protected $primaryKey = 'worksheet_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'worksheet_id' => 'Id',
        'worksheet_key' => 'Key',
        'worksheet_title' => 'Title',
    ];

    public static $sortColumns = [
        'worksheet_id',
        'worksheet_key',
        'worksheet_title',
        'worksheet_sort_order',
    ];

    protected $fillable = [
        'worksheet_key',
        'worksheet_icon',
        'worksheet_title',
        'worksheet_desc',
        'worksheet_age',
        'worksheet_age_label',
        'worksheet_ages',
        'worksheet_skills',
        'worksheet_agama',
        'worksheet_plans',
        'worksheet_bg',
        'worksheet_icon_color',
        'worksheet_is_api',
        'worksheet_sort_order',
        'worksheet_active',
        'worksheet_creator_id',
        'worksheet_addon_id',
    ];

    protected $casts = [
        'worksheet_ages' => 'array',
        'worksheet_skills' => 'array',
        'worksheet_agama' => 'array',
        'worksheet_plans' => 'array',
        'worksheet_is_api' => 'boolean',
        'worksheet_active' => 'boolean',
    ];

    public function rules(): array
    {
        return [
            'worksheet_key' => 'required|string|max:80|unique:worksheets,worksheet_key',
            'worksheet_title' => 'required|string',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->worksheet_id,
            'key' => $this->worksheet_key,
            'icon' => $this->worksheet_icon,
            'title' => $this->worksheet_title,
            'desc' => $this->worksheet_desc,
            'age' => $this->worksheet_age,
            'ageLabel' => $this->worksheet_age_label,
            'ages' => $this->worksheet_ages ?? [],
            'skills' => $this->worksheet_skills ?? [],
            'agama' => $this->worksheet_agama ?? [],
            'plans' => $this->worksheet_plans ?? [],
            'bg' => $this->worksheet_bg,
            'iconColor' => $this->worksheet_icon_color,
            'isApi' => (bool) $this->worksheet_is_api,
            'sort_order' => $this->worksheet_sort_order,
            'active' => (bool) $this->worksheet_active,
        ];
    }

    public static function field_name()
    {
        return 'worksheet_title';
    }
}
