<?php

namespace App\Models;

use App\Models\BaseModel;
use App\PaymentStatusEnum;

class Payment extends BaseModel
{
    protected $table = 'payments';
    protected $keyType = 'int';
    protected $primaryKey = 'payment_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'payment_id' => 'Id',
        'payment_id_user' => 'User',
        'payment_order_code' => 'Order Code',
        'payment_jumlah' => 'Jumlah',
        'payment_total' => 'Total',
        'payment_status' => 'Status',
        'payment_metode' => 'Metode',
        'payment_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'payment_id',
        'payment_id_user',
        'payment_order_code',
        'payment_jumlah',
        'payment_total',
        'payment_status',
        'payment_metode',
        'payment_created_at',
    ];

    protected $fillable = [
        'payment_id_user',
        'payment_id_plan',
        'payment_order_code',
        'payment_jumlah',
        'payment_diskon',
        'payment_diskon_code',
        'payment_total',
        'payment_qris_string',
        'payment_status',
        'payment_metode',
        'payment_paid_at',
        'payment_expired_at',
        'payment_created_at',
        'payment_updated_at',
    ];

    public function rules(): array
    {
        return [
            'payment_id_user' => 'required',
            'payment_jumlah' => 'required|integer|min:1',
        ];
    }

    public static function field_name()
    {
        return 'payment_order_code';
    }

    public function has_user()
    {
        return $this->belongsTo(User::class, 'payment_id_user');
    }

    public function has_plan()
    {
        return $this->belongsTo(Plan::class, 'payment_id_plan', 'plan_id');
    }

    public function isPending()
    {
        return $this->payment_status === PaymentStatusEnum::PENDING->value && \Carbon\Carbon::parse($this->payment_expired_at)->isFuture();
    }

    public static function generateCode()
    {
        return 'PAY' . date('Ymd') . strtoupper(substr(md5(uniqid()), 0, 6));
    }
}
