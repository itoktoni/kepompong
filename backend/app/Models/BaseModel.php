<?php

namespace App\Models;

use Abbasudo\Purity\Traits\Filterable;
use Abbasudo\Purity\Traits\Sortable;
use App\Concerns\DefaultEntity;
use App\Concerns\OptionTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperBaseModel
 */
class BaseModel extends Model
{
    use DefaultEntity, Filterable, OptionTrait, Sortable;

    protected $table = 'products';

    protected $primaryKey = 'id';

    public $timestamps = true;

    public $incrementing = true;

    /**
     * Columns available for filtering.
     */
    public static $filterColumns = [];

    /**
     * Columns available for sorting.
     */
    public static $sortColumns = [];

    public function rules()
    {
        return [
            $this->field_name() => ['required', 'string', 'max:255'],
        ];
    }
}
