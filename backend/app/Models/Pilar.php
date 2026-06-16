<?php

namespace App\Models;

use App\Models\BaseModel;

class Pilar extends BaseModel
{
    protected $table = 'pilars';
    protected $keyType = 'int';
    protected $primaryKey = 'pilar_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'pilar_id' => 'Id',
        'pilar_key' => 'Key',
        'pilar_title' => 'Title',
        'pilar_status' => 'Status',
    ];

    public static $sortColumns = [
        'pilar_id',
        'pilar_key',
        'pilar_title',
        'pilar_sort_order',
        'pilar_status',
    ];

    protected $fillable = [
        'pilar_key',
        'pilar_emoji',
        'pilar_title',
        'pilar_subtitle',
        'pilar_color',
        'pilar_bg',
        'pilar_ages',
        'pilar_agama',
        'pilar_plans',
        'pilar_sort_order',
        'pilar_active',
        'pilar_status',
    ];

    protected $casts = [
        'pilar_ages' => 'array',
        'pilar_agama' => 'array',
        'pilar_plans' => 'array',
        'pilar_active' => 'boolean',
    ];

    public function rules(): array
    {
        return [
            'pilar_key' => 'required|string|max:50|unique:pilars,pilar_key',
            'pilar_title' => 'required|string',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->pilar_id,
            'key' => $this->pilar_key,
            'emoji' => $this->pilar_emoji,
            'title' => $this->pilar_title,
            'subtitle' => $this->pilar_subtitle,
            'color' => $this->pilar_color,
            'bg' => $this->pilar_bg,
            'ages' => $this->pilar_ages ?? [],
            'agama' => $this->pilar_agama ?? [],
            'plans' => $this->pilar_plans ?? [],
            'sort_order' => $this->pilar_sort_order,
            'active' => (bool) $this->pilar_active,
            'status' => $this->pilar_status,
        ];
    }

    public static function field_name()
    {
        return 'pilar_title';
    }
}
