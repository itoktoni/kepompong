<?php

namespace App\Models;

use App\Models\BaseModel;

class Idea extends BaseModel
{
    protected $table = 'idea';
    protected $keyType = 'int';
    protected $primaryKey = 'idea_id';

    public $timestamps = false;
    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'idea_id' => 'Id',
        'idea_nama' => 'Nama',
        'idea_keterangan' => 'Keterangan',
        'idea_moral' => 'Moral',
        'idea_type' => 'Type',
        'idea_creator' => 'Creator',
        'idea_implementor' => 'Implementor',
        'idea_tanggal' => 'Tanggal',
        'idea_agama' => 'Agama',
        'idea_ages' => 'Ages',
        'idea_skills' => 'Skills',
        'idea_prompt' => 'Prompt',
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'idea_id',
        'idea_nama',
        'idea_keterangan',
        'idea_moral',
        'idea_type',
        'idea_creator',
        'idea_implementor',
        'idea_tanggal',
        'idea_agama',
        'idea_ages',
        'idea_skills',
        'idea_prompt',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idea_id',
        'idea_nama',
        'idea_keterangan',
        'idea_moral',
        'idea_type',
        'idea_creator',
        'idea_implementor',
        'idea_tanggal',
        'idea_agama',
        'idea_ages',
        'idea_skills',
        'idea_qty',
        'idea_prompt',
    ];

    protected $casts = [
        'idea_agama' => 'array',
        'idea_ages' => 'array',
        'idea_skills' => 'array',
        'idea_qty' => 'integer',
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'idea_id' => 'required',
            'idea_nama' => 'string',
            'idea_keterangan' => 'string',
            'idea_moral' => 'string',
            'idea_type' => 'string',
            'idea_creator' => 'string',
            'idea_implementor' => 'string',
            'idea_agama' => 'nullable|array',
            'idea_ages' => 'nullable|array',
            'idea_skills' => 'nullable|array',
            'idea_prompt' => 'nullable|string',
        ];
    }

    public function toArray()
    {
        return parent::toArray();
    }

    public static function field_name()
    {
        return 'idea_nama';
    }

}
