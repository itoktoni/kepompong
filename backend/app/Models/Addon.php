<?php

namespace App\Models;

use App\Models\BaseModel;

class Addon extends BaseModel
{
    protected $table = 'addons';
    protected $keyType = 'int';
    protected $primaryKey = 'addon_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'addon_id' => 'Id',
        'addon_id_user' => 'Creator',
        'addon_nama' => 'Nama',
        'addon_harga' => 'Harga',
    ];

    public static $sortColumns = [
        'addon_id',
        'addon_nama',
        'addon_harga',
        'addon_created_at',
    ];

    protected $fillable = [
        'addon_id_user',
        'addon_nama',
        'addon_desc',
        'addon_harga',
        'addon_ages',
        'addon_agama',
        'addon_plans',
        'addon_bg',
        'addon_icon',
        'addon_buyers',
        'addon_active',
        'addon_created_at',
        'addon_updated_at',
    ];

    protected $casts = [
        'addon_ages' => 'array',
        'addon_agama' => 'array',
        'addon_plans' => 'array',
        'addon_buyers' => 'array',
        'addon_active' => 'boolean',
        'addon_harga' => 'integer',
    ];

    public function rules(): array
    {
        return [
            'addon_id_user' => 'required',
            'addon_nama' => 'required|string|max:255',
        ];
    }

    public static function field_name()
    {
        return 'addon_nama';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'addon_id_user');
    }

    public function has_activities()
    {
        return $this->hasMany(Activity::class, 'addon_id');
    }
}
