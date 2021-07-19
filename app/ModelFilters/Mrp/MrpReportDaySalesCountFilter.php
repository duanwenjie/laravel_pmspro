<?php

namespace App\ModelFilters\Mrp;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class MrpReportDaySalesCountFilter extends ModelFilter
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
     * 统计时间 计算批次(compute_batch)时间范围
     * @param $value
     */
    public function computeBatch($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('compute_batch', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('compute_batch', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('compute_batch', $value);
        }
    }

    /**
     * 查询条件 统计时间(updated_at)时间范围
     * @param $value
     */
    public function updatedAt($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('updated_at', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('updated_at', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('updated_at', $value);
        }
    }
}
