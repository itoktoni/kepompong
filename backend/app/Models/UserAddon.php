<?php

namespace App\Models;

use App\Models\BaseModel;

class UserAddon extends BaseModel
{
    protected $table = 'user_addons';
    protected $keyType = 'int';
    protected $primaryKey = 'user_addon_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'user_addon_id' => 'Id',
        'user_addon_id_user' => 'User',
        'user_addon_id_addon' => 'Addon',
        'user_addon_status' => 'Status',
    ];

    public static $sortColumns = [
        'user_addon_id',
        'user_addon_id_user',
        'user_addon_id_addon',
        'user_addon_status',
        'user_addon_created_at',
    ];

    protected $fillable = [
        'user_addon_id_user',
        'user_addon_id_addon',
        'user_addon_harga',
        'user_addon_status',
        'user_addon_created_at',
        'user_addon_expired_at',
    ];

    protected $casts = [
        'user_addon_harga' => 'integer',
    ];

    public function rules(): array
    {
        return [
            'user_addon_id_user' => 'required',
            'user_addon_id_addon' => 'required',
        ];
    }

    public static function field_name()
    {
        return 'user_addon_id';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'user_addon_id_user');
    }

    public function has_addon()
    {
        return $this->belongsTo(Addon::class, 'user_addon_id_addon', 'addon_id');
    }
}
