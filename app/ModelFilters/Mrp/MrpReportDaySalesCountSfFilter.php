<?php

namespace App\ModelFilters\Mrp;

use EloquentFilter\ModelFilter;

class MrpReportDaySalesCountSfFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 查询条件 SKU名称
     * @param $value
     */
    public function sku($value)
    {
        $this->whereIn('sku', $value);
    }
}
