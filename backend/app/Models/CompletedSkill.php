<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompletedSkill extends BaseModel
{
    protected $table = 'completed_skills';
    protected $keyType = 'int';
    protected $primaryKey = 'completed_skill_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'completed_skill_id' => 'Id',
        'completed_skill_id_anak' => 'Anak',
        'completed_skill_key' => 'Key',
        'completed_skill_title' => 'Title',
        'completed_skill_pilar' => 'Pilar',
        'completed_skill_completed_at' => 'Completed At',
        'completed_skill_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'completed_skill_id',
        'completed_skill_id_anak',
        'completed_skill_key',
        'completed_skill_title',
        'completed_skill_completed_at',
        'completed_skill_created_at',
    ];

    protected $fillable = [
        'completed_skill_id_anak',
        'completed_skill_key',
        'completed_skill_emoji',
        'completed_skill_title',
        'completed_skill_pilar',
        'completed_skill_color',
        'completed_skill_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed_skill_completed_at' => 'datetime',
        ];
    }

    public function rules(): array
    {
        return [
            'completed_skill_id_anak' => 'required',
            'completed_skill_key' => 'required|string',
            'completed_skill_title' => 'required|string|max:255',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->completed_skill_id,
            'key' => $this->completed_skill_key,
            'emoji' => $this->completed_skill_emoji,
            'title' => $this->completed_skill_title,
            'pilar' => $this->completed_skill_pilar,
            'color' => $this->completed_skill_color,
            'completed_at' => $this->completed_skill_completed_at,
        ];
    }

    public static function field_name()
    {
        return 'completed_skill_title';
    }

    public function has_anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class, 'completed_skill_id_anak', 'anak_id');
    }
}
