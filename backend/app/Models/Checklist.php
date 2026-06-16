<?php

namespace App\Models;

use App\Models\BaseModel;

class Checklist extends BaseModel
{
    protected $table = 'checklists';
    protected $keyType = 'int';
    protected $primaryKey = 'checklist_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'checklist_id' => 'Id',
        'checklist_id_anak' => 'Anak',
        'checklist_title' => 'Title',
        'checklist_date' => 'Date',
        'checklist_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'checklist_id',
        'checklist_id_anak',
        'checklist_title',
        'checklist_date',
        'checklist_created_at',
    ];

    protected $fillable = [
        'checklist_id_anak',
        'checklist_title',
        'checklist_items',
        'checklist_date',
    ];

    protected $casts = [
        'checklist_items' => 'array',
        'checklist_date' => 'date',
    ];

    public function rules(): array
    {
        return [
            'checklist_id_anak' => 'required',
            'checklist_title' => 'required|string|max:255',
        ];
    }

    public static function field_name()
    {
        return 'checklist_title';
    }

    public function toArray()
    {
        return [
            'id' => $this->checklist_id,
            'anak_id' => $this->checklist_id_anak,
            'title' => $this->checklist_title,
            'items' => $this->checklist_items ?? [],
            'date' => $this->checklist_date,
            'created_at' => $this->checklist_created_at,
        ];
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'checklist_id_anak', 'anak_id');
    }
}
