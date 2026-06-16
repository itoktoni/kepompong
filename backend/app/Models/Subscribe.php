<?php

namespace App\Models;

use App\Models\BaseModel;

class Subscribe extends BaseModel
{
    protected $table = 'subscribe';
    protected $keyType = 'int';
    protected $primaryKey = 'subscribe_id';

    public $timestamps = false;
    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'subscribe_id' => 'Id',
        'subscribe_id_user' => 'User',
        'subscribe_harga' => 'Harga',
        'subscribe_discount' => 'Discount',
        'subscribe_total' => 'Total',
        'subscribe_id_plan' => 'Plan',
        'subsribe_value' => 'Value',
        'subscribe_trial_at' => 'At',
        'subscribe_start_at' => 'At',
        'subscribe_end_at' => 'At',
        'subscribe_canceled_at' => 'At',
        'subscribe_created_at' => 'At',
        'subscribe_updated_at' => 'At'
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'subscribe_id',
        'subscribe_id_user',
        'subscribe_harga',
        'subscribe_discount',
        'subscribe_total',
        'subscribe_id_plan',
        'subsribe_value',
        'subscribe_trial_at',
        'subscribe_start_at',
        'subscribe_end_at',
        'subscribe_canceled_at',
        'subscribe_created_at',
        'subscribe_updated_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscribe_id',
        'subscribe_id_user',
        'subscribe_harga',
        'subscribe_discount',
        'subscribe_total',
        'subscribe_id_plan',
        'subsribe_value',
        'subscribe_trial_at',
        'subscribe_start_at',
        'subscribe_end_at',
        'subscribe_canceled_at',
        'subscribe_created_at',
        'subscribe_updated_at'
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

			'subscribe_id' => 'required',
			'subscribe_id_user' => 'required',
			'subscribe_id_plan' => 'required',
        ];
    }

    public static function field_name()
    {
        return 'subscribe_nama';
    }

    public function has_plan()
    {
        return $this->belongsTo(Plan::class, 'subscribe_id_plan', 'plan_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class, 'subscribe_id_plan', 'plan_id');
    }

}
