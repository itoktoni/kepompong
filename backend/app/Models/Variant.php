<?php

namespace App\Models;

use App\Models\BaseModel;

class Variant extends BaseModel
{
    protected $table = 'variants';
    protected $keyType = 'int';
    protected $primaryKey = 'variant_id';

    public $timestamps = false;
    public $incrementing = true;

    protected $fillable = [
        'product_id',
        'variant_nama',
        'variant_harga',
        'variant_deskripsi',
    ];
}
