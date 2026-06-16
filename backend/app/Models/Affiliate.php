<?php

namespace App\Models;

use App\Models\BaseModel;

class Affiliate extends BaseModel
{
    protected $table = 'affiliate';
    protected $keyType = 'int';
    protected $primaryKey = 'affiliate_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'affiliate_id' => 'Id',
        'affiliate_id_user' => 'User',
        'affiliate_tipe' => 'Tipe',
        'affiliate_jumlah' => 'Jumlah',
        'affiliate_status' => 'Status',
        'affiliate_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'affiliate_id',
        'affiliate_id_user',
        'affiliate_tipe',
        'affiliate_jumlah',
        'affiliate_status',
        'affiliate_created_at',
    ];

    protected $fillable = [
        'affiliate_id_user',
        'affiliate_id_from_user',
        'affiliate_id_payment',
        'affiliate_tipe',
        'affiliate_jumlah',
        'affiliate_payment_jumlah',
        'affiliate_commission_rate',
        'affiliate_catatan',
        'affiliate_status',
        'affiliate_created_at',
        'affiliate_updated_at',
    ];

    public function rules(): array
    {
        return [
            'affiliate_id_user' => 'required',
            'affiliate_tipe' => 'required|string',
            'affiliate_jumlah' => 'required|integer|min:0',
        ];
    }

    public static function field_name()
    {
        return 'affiliate_tipe';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'affiliate_id_user');
    }

    public function has_from_user()
    {
        return $this->belongsTo(User::class, 'affiliate_id_from_user');
    }

    public function has_payment()
    {
        return $this->belongsTo(Payment::class, 'affiliate_id_payment', 'payment_id');
    }
}
