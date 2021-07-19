<?php

namespace App\ModelFilters\Mrp;

use EloquentFilter\ModelFilter;

class MrpReportSalesCountSkuDetailFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 查询条件 SKU名称
     * @param $value
     */
    public function ordersSku($value)
    {
        is_string($value)
            ? $this->where('orders_sku', '=', $value)
            : $this->whereIn('orders_sku', $value);
    }
}
