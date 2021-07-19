<?php

namespace App\ModelFilters\Mrp;

use EloquentFilter\ModelFilter;

class MrpReportStockCountSfFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 查询条件 SKU名称
     * @param $value
     */
    public function sku($value)
    {
        is_string($value)
        ? $this->where('sku', '=', $value)
        : $this->whereIn('sku', $value);
    }
}
