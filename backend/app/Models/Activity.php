<?php

namespace App\Models;

use App\Models\BaseModel;

class Activity extends BaseModel
{
    protected $table = 'activities';
    protected $keyType = 'int';
    protected $primaryKey = 'id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'type' => 'Type',
        'title' => 'Title',
        'slug' => 'Slug',
        'active' => 'Active',
        'status' => 'Status',
        'views' => 'Views',
        'created_by' => 'Created By',
        'creator' => 'Creator',
    ];

    public static $sortColumns = [
        'id',
        'type',
        'title',
        'slug',
        'sort_order',
        'active',
        'views',
        'status',
        'created_by',
        'creator',
    ];

    protected $fillable = [
        'type',
        'title',
        'slug',
        'desc',
        'image',
        'moral',
        'ages',
        'skills',
        'data',
        'sort_order',
        'active',
        'plans',
        'agama',
        'views',
        'status',
        'created_by',
        'prompt',
        'notes',
        'creator',
    ];

    protected function casts(): array
    {
        return [
            'ages' => 'array',
            'skills' => 'array',
            'data' => 'array',
            'plans' => 'array',
            'agama' => 'array',
            'active' => 'boolean',
            'views' => 'integer',
            'created_by' => 'integer',
        ];
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'slug' => 'required|string|max:255',
            'image' => 'string|max:255',
            'status' => 'string|max:255',
            'active' => 'integer|max:255',
            'moral' => 'string|max:255',
        ];
    }

    public static function field_name()
    {
        return 'title';
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function incrementView()
    {
        $this->increment('views');
        return $this->views;
    }
}
