<?php

namespace App\Models;

use App\Models\BaseModel;

class Cashout extends BaseModel
{
    protected $table = 'cashouts';
    protected $keyType = 'int';
    protected $primaryKey = 'cashout_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'cashout_id' => 'Id',
        'cashout_id_user' => 'User',
        'cashout_jumlah' => 'Jumlah',
        'cashout_status' => 'Status',
        'cashout_rekening_bank' => 'Bank',
        'cashout_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'cashout_id',
        'cashout_id_user',
        'cashout_jumlah',
        'cashout_status',
        'cashout_created_at',
    ];

    protected $fillable = [
        'cashout_id_user',
        'cashout_jumlah',
        'cashout_admin_fee',
        'cashout_diterima',
        'cashout_rekening_bank',
        'cashout_rekening_nomor',
        'cashout_rekening_nama',
        'cashout_status',
        'cashout_catatan',
        'cashout_created_at',
        'cashout_updated_at',
    ];

    protected $casts = [
        'cashout_jumlah' => 'integer',
        'cashout_admin_fee' => 'integer',
        'cashout_diterima' => 'integer',
    ];

    public function rules(): array
    {
        return [
            'cashout_id_user' => 'required',
            'cashout_jumlah' => 'required|integer|min:1',
        ];
    }

    public static function field_name()
    {
        return 'cashout_jumlah';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'cashout_id_user');
    }
}
