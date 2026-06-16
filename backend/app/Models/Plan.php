<?php

namespace App\Models;

use App\Models\BaseModel;

class Plan extends BaseModel
{
    protected $table = 'plan';
    protected $keyType = 'int';
    protected $primaryKey = 'plan_id';

    public $timestamps = false;
    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'plan_id' => 'Id',
        'plan_status' => 'Status',
        'plan_nama' => 'Nama',
        'plan_keterangan' => 'Keteranan',
        'plan_harga' => 'Harga',
        'plan_fee' => 'Fee',
        'plan_color' => 'Color',
        'plan_recomended' => 'Rekomended',
        'plan_periode' => 'Periode',
        'plan_interval' => 'Interval',
        'plan_coret' => 'Harga Coret'
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'plan_id',
        'plan_nama',
        'plan_status',
        'plan_keterangan',
        'plan_harga',
        'plan_color',
        'plan_recomended',
        'plan_fee',
        'plan_periode',
        'plan_interval',
        'plan_coret'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'plan_id',
        'plan_status',
        'plan_nama',
        'plan_keterangan',
        'plan_harga',
        'plan_fee',
        'plan_periode',
        'plan_interval',
        'plan_recomended',
        'plan_color',
        'plan_coret'
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [

			'plan_nama' => 'string',
			'plan_keterangan' => 'string',
			'plan_periode' => 'string',
			'plan_interval' => 'string',
        ];
    }

    public static function field_name()
    {
        return 'plan_nama';
    }

}
