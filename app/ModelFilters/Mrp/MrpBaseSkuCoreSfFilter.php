<?php

namespace App\ModelFilters\Mrp;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class MrpBaseSkuCoreSfFilter extends ModelFilter
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

    /**
     * 查询条件 销售状态
     * @param $value
     */
    public function salesStatus($value)
    {
        $this->where('sales_status', '=', $value);
    }

    /**
     * 查询条件 备货方式
     * @param $value
     */
    public function stockWay($value)
    {
        $this->where('stock_way', '=', $value);
    }

    /**
     * 查询条件 计算批次(updated_at)更新时间范围
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

    /**
     * 查询条件 新增时间
     * @param $value
     */
    public function createdAt($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('created_at', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('created_at', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('created_at', $value);
        }
    }
}
