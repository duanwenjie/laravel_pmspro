<?php

namespace App\ModelFilters\Mrp;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

//销量源数据
class MrpReportOrigSalesdataV3Filter extends ModelFilter
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

    /**
     * 查询条件 付款时间 (payment_date)时间范围
     * @param $value
     */
    public function paymentDate($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('payment_date', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('payment_date', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('payment_date', $value);
        }
    }
}
