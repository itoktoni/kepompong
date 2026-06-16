<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends BaseModel
{
    protected $table = 'evaluations';
    protected $keyType = 'int';
    protected $primaryKey = 'evaluation_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'evaluation_id' => 'Id',
        'evaluation_id_anak' => 'Anak',
        'evaluation_skill_key' => 'Skill Key',
        'evaluation_skill_title' => 'Skill Title',
        'evaluation_pilar' => 'Pilar',
        'evaluation_points' => 'Points',
        'evaluation_max_points' => 'Max Points',
        'evaluation_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'evaluation_id',
        'evaluation_id_anak',
        'evaluation_skill_key',
        'evaluation_skill_title',
        'evaluation_points',
        'evaluation_max_points',
        'evaluation_created_at',
    ];

    protected $fillable = [
        'evaluation_id_anak',
        'evaluation_skill_key',
        'evaluation_skill_title',
        'evaluation_pilar',
        'evaluation_points',
        'evaluation_max_points',
        'evaluation_notes',
    ];

    public function rules(): array
    {
        return [
            'evaluation_id_anak' => 'required',
            'evaluation_skill_key' => 'required|string',
            'evaluation_points' => 'required|integer|min:0|max:100',
        ];
    }

    public static function field_name()
    {
        return 'evaluation_skill_title';
    }

    public function has_anak(): BelongsTo
    {
        return $this->belongsTo(Anak::class, 'evaluation_id_anak', 'anak_id');
    }
}
