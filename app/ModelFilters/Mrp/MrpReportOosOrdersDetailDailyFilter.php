<?php

namespace App\ModelFilters\Mrp;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

//MRP(国内)-》每日缺货订单明细
class MrpReportOosOrdersDetailDailyFilter extends ModelFilter
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
     * 最后更新时间 计算批次(updated_at)时间范围
     * @param $value
     */
    public function dwDate($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('dw_date', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('dw_date', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('dw_date', $value);
        }
    }
}
