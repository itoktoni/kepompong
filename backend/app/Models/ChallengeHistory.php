<?php

namespace App\Models;

class ChallengeHistory extends BaseModel
{
    protected $table = 'challenge_histories';

    protected $keyType = 'int';

    protected $primaryKey = 'challenge_history_id';

    public $timestamps = false;

    public $incrementing = true;

    public static $filterColumns = [
        'challenge_history_id' => 'Id',
        'challenge_history_id_anak' => 'Anak',
        'challenge_history_category' => 'Category',
        'challenge_history_title' => 'Title',
        'challenge_history_date' => 'Date',
        'challenge_history_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'challenge_history_id',
        'challenge_history_id_anak',
        'challenge_history_category',
        'challenge_history_title',
        'challenge_history_date',
        'challenge_history_created_at',
    ];

    protected $fillable = [
        'challenge_history_id_anak',
        'challenge_history_category',
        'challenge_history_title',
        'challenge_history_date',
        'challenge_history_meta',
    ];

    protected $casts = [
        'challenge_history_meta' => 'array',
        'challenge_history_date' => 'date',
    ];

    public function rules(): array
    {
        return [
            'challenge_history_id_anak' => 'required',
            'challenge_history_category' => 'required|string|max:100',
            'challenge_history_title' => 'required|string|max:255',
        ];
    }

    public static function field_name()
    {
        return 'challenge_history_title';
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'challenge_history_id_anak', 'anak_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->challenge_history_id,
            'anak_id' => $this->challenge_history_id_anak,
            'category' => $this->challenge_history_category,
            'title' => $this->challenge_history_title,
            'emoji' => $this->challenge_history_meta['emoji'] ?? null,
            'maxPoints' => $this->challenge_history_meta['maxPoints'] ?? 0,
            'color' => $this->challenge_history_meta['color'] ?? null,
            'notes' => $this->challenge_history_meta['notes'] ?? null,
            'date' => $this->challenge_history_date,
            'created_at' => $this->challenge_history_created_at,
        ];
    }
}
