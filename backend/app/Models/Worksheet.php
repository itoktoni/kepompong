<?php

namespace App\Models;

use App\Models\BaseModel;

class Worksheet extends BaseModel
{
    protected $table = 'worksheets_anak';
    protected $keyType = 'int';
    protected $primaryKey = 'worksheet_id';

    public $timestamps = false;
    public $incrementing = true;

    public static $filterColumns = [
        'worksheet_id' => 'Id',
        'worksheet_id_anak' => 'Anak',
        'worksheet_type' => 'Type',
        'worksheet_date' => 'Date',
        'worksheet_status' => 'Status',
        'worksheet_created_at' => 'Created At',
    ];

    public static $sortColumns = [
        'worksheet_id',
        'worksheet_id_anak',
        'worksheet_type',
        'worksheet_date',
        'worksheet_status',
        'worksheet_created_at',
    ];

    protected $fillable = [
        'worksheet_id_anak',
        'worksheet_type',
        'worksheet_data',
        'worksheet_date',
        'worksheet_status',
    ];

    protected $casts = [
        'worksheet_data' => 'array',
        'worksheet_date' => 'date',
    ];

    public function rules(): array
    {
        return [
            'worksheet_id_anak' => 'required',
            'worksheet_type' => 'required|string|max:50',
        ];
    }

    public static function field_name()
    {
        return 'worksheet_type';
    }

    public function has_anak()
    {
        return $this->belongsTo(Anak::class, 'worksheet_id_anak', 'anak_id');
    }
}
