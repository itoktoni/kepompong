<?php

namespace App\Models;

use App\Models\BaseModel;

class FamilyRequest extends BaseModel
{
    protected $table = 'family_requests';
    protected $keyType = 'int';
    protected $primaryKey = 'family_request_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'family_request_id' => 'Id',
        'family_request_id_user' => 'User',
        'family_request_id_target' => 'Target',
        'family_request_email' => 'Email',
        'family_request_status' => 'Status',
    ];

    public static $sortColumns = [
        'family_request_id',
        'family_request_id_user',
        'family_request_status',
        'family_request_created_at',
    ];

    protected $fillable = [
        'family_request_id_user',
        'family_request_id_target',
        'family_request_email',
        'family_request_status',
        'family_request_label',
        'family_request_created_at',
        'family_request_updated_at',
    ];

    public function rules(): array
    {
        return [
            'family_request_id_user' => 'required|integer',
            'family_request_id_target' => 'required|integer',
            'family_request_email' => 'required|email',
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->family_request_id,
            'user_id' => $this->family_request_id_user,
            'target_id' => $this->family_request_id_target,
            'email' => $this->family_request_email,
            'status' => $this->family_request_status,
            'label' => $this->family_request_label,
            'created_at' => $this->family_request_created_at,
            'updated_at' => $this->family_request_updated_at,
            'user' => $this->relationLoaded('has_user') ? $this->getRelation('has_user')?->only(['id', 'name', 'email']) : null,
            'target' => $this->relationLoaded('has_target') ? $this->getRelation('has_target')?->only(['id', 'name', 'email']) : null,
        ];
    }

    public static function field_name()
    {
        return 'family_request_email';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'family_request_id_user', 'id');
    }

    public function has_target()
    {
        return $this->belongsTo(User::class, 'family_request_id_target', 'id');
    }
}
