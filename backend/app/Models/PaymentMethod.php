<?php

namespace App\Models;

use App\Models\BaseModel;

class PaymentMethod extends BaseModel
{
    protected $table = 'payment_method';
    protected $keyType = 'int';
    protected $primaryKey = 'payment_method_id';

    public $timestamps = false;
    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'payment_method_id' => 'Id',
        'payment_method_nama' => 'Nama',
        'payment_method_person' => 'Person',
        'payment_method_rekening' => 'Rekening',
        'payment_method_transfer' => 'Transfer'
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'payment_method_id',
        'payment_method_nama',
        'payment_method_person',
        'payment_method_rekening',
        'payment_method_transfer'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method_id',
        'payment_method_nama',
        'payment_method_person',
        'payment_method_rekening',
        'payment_method_transfer'
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            
			'payment_method_id' => 'required',
			'payment_method_nama' => 'string',
			'payment_method_person' => 'string',
			'payment_method_rekening' => 'string',
			'payment_method_transfer' => 'string',
        ];
    }

    public function toArray(){}

    public static function field_name()
    {
        return 'payment_method_nama';
    }

}
