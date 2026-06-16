<?php

namespace App\Models;

class Challenge extends BaseModel
{
    protected $table = 'challenges';

    protected $keyType = 'int';

    protected $primaryKey = 'challenge_id';

    public $timestamps = false;

    public $incrementing = true;

    public static $filterColumns = [
        'challenge_id' => 'Id',
        'challenge_id_anak' => 'Anak',
        'challenge_category' => 'Category',
        'challenge_title' => 'Title',
        'challenge_points' => 'Points',
        'challenge_status' => 'Status',
        'challenge_date' => 'Date',
        'challenge_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'challenge_id',
        'challenge_id_anak',
        'challenge_category',
        'challenge_title',
        'challenge_points',
        'challenge_status',
        'challenge_date',
        'challenge_created_at',
    ];

    protected $fillable = [
        'challenge_id_anak',
        'challenge_category',
        'challenge_title',
        'challenge_emoji',
        'challenge_points',
        'challenge_status',
        'challenge_date',
        'challenge_meta',
    ];

    protected $casts = [
        'challenge_meta' => 'array',
        'challenge_date' => 'date',
    ];

    public function rules(): array
    {
        return [
            'challenge_id_anak' => 'required',
            'challenge_category' => 'required|string|max:100',
            'challenge_title' => 'required|string|max:255',
        ];
    }

    public static function field_name()
    {
        return 'challenge_title';
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'challenge_id_anak', 'anak_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->challenge_id,
            'anak_id' => $this->challenge_id_anak,
            'category' => $this->challenge_category,
            'title' => $this->challenge_title,
            'emoji' => $this->challenge_emoji,
            'points' => $this->challenge_points ?? 0,
            'maxPoints' => $this->challenge_meta['maxPoints'] ?? 10,
            'color' => $this->challenge_meta['color'] ?? null,
            'bg' => $this->challenge_meta['bg'] ?? null,
            'notes' => $this->challenge_meta['notes'] ?? null,
            'status' => $this->challenge_status,
            'date' => $this->challenge_date,
            'created_at' => $this->challenge_created_at,
        ];
    }
}
