<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SkillActivity extends BaseModel
{
    protected $table = 'skill_activities';
    protected $keyType = 'int';
    protected $primaryKey = 'skill_activity_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'skill_activity_id' => 'Id',
        'skill_activity_id_skill' => 'Skill',
        'skill_activity_title' => 'Title',
        'skill_activity_completed' => 'Completed',
        'skill_activity_date' => 'Date',
        'skill_activity_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'skill_activity_id',
        'skill_activity_id_skill',
        'skill_activity_title',
        'skill_activity_completed',
        'skill_activity_date',
        'skill_activity_created_at',
    ];

    protected $fillable = [
        'skill_activity_id_skill',
        'skill_activity_title',
        'skill_activity_emoji',
        'skill_activity_feature',
        'skill_activity_date',
        'skill_activity_completed',
    ];

    protected function casts(): array
    {
        return [
            'skill_activity_completed' => 'boolean',
        ];
    }

    public function rules(): array
    {
        return [
            'skill_activity_id_skill' => 'required',
            'skill_activity_title' => 'required|string|max:255',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->skill_activity_id,
            'title' => $this->skill_activity_title,
            'emoji' => $this->skill_activity_emoji,
            'feature' => $this->skill_activity_feature,
            'date' => $this->skill_activity_date,
            'completed' => $this->skill_activity_completed,
        ];
    }

    public static function field_name()
    {
        return 'skill_activity_title';
    }

    public function has_skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class, 'skill_activity_id_skill', 'skill_id');
    }
}
