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
        'idea_title' => 'Title',
        'idea_description' => 'Description',
        'idea_moral' => 'Moral',
        'idea_type' => 'Type',
        'idea_date' => 'Date',
        'idea_ai' => 'Ai'
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'idea_id',
        'idea_title',
        'idea_description',
        'idea_moral',
        'idea_type',
        'idea_date',
        'idea_ai'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idea_id',
        'idea_title',
        'idea_description',
        'idea_moral',
        'idea_type',
        'idea_date',
        'idea_ai'
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            
			'idea_id' => 'required',
			'idea_title' => 'string',
			'idea_description' => 'string',
			'idea_moral' => 'string',
			'idea_type' => 'string',
			'idea_ai' => 'string',
        ];
    }

    public function toArray(){}

    public static function field_name()
    {
        return 'idea_nama';
    }

}
