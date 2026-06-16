<?php

namespace App\Models;

use App\Concerns\HasUserstamps;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperDiscount
 */
class Discount extends BaseModel
{
    use HasUserstamps, SoftDeletes;

    protected $table = 'discounts';

    protected $primaryKey = 'discount_id';

    public $incrementing = true;
    public $timestamps = true;

    const CREATED_AT = 'discount_created_at';
    const UPDATED_AT = 'discount_updated_at';
    const DELETED_AT = 'discount_deleted_at';
    const CREATED_BY = 'discount_created_by';
    const UPDATED_BY = 'discount_updated_by';
    const DELETED_BY = 'discount_deleted_by';

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [
        'discount_id' => 'Discount Id',
        'discount_code' => 'Discount Code',
        'discount_nama' => 'Discount Nama',
        'discount_type' => 'Discount Type',
    ];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [
        'discount_id',
        'discount_code',
        'discount_nama',
        'discount_value',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'discount_id',
        'discount_code',
        'discount_nama',
        'discount_type',
        'discount_value',
        'discount_min_transaction',
        'discount_max_amount',
        'discount_active',
        'discount_start',
        'discount_end',
        'discount_created_by',
        'discount_updated_by',
        'discount_deleted_by',
    ];

    /**
     * Validation rules.
     */
    public function rules(): array
    {
        return [
            'discount_code' => 'required|string|unique:discounts,discount_code',
            'discount_nama' => 'required|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric',
            'discount_min_transaction' => 'numeric',
            'discount_max_amount' => 'numeric|nullable',
            'discount_active' => 'boolean',
        ];
    }

    public static function field_name()
    {
        return 'discount_code';
    }

    /**
     * Check if discount is valid.
     */
    public function isValid($subtotal = 0): bool
    {
        if (! $this->discount_active) {
            return false;
        }

        if ($this->discount_min_transaction > 0 && $subtotal < $this->discount_min_transaction) {
            return false;
        }

        $now = now();
        if ($this->discount_start && $now < $this->discount_start) {
            return false;
        }

        if ($this->discount_end && $now > $this->discount_end) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount.
     */
    public function calculate($subtotal): float
    {
        if (! $this->isValid($subtotal)) {
            return 0;
        }

        $amount = $this->discount_type === 'percentage'
            ? $subtotal * $this->discount_value / 100
            : $this->discount_value;

        if ($this->discount_max_amount && $amount > $this->discount_max_amount) {
            return $this->discount_max_amount;
        }

        return $amount;
    }
}
