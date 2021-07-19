<?php

namespace App\ModelFilters\Mrp;

use App\Models\User;
use App\Tools\Formater;
use EloquentFilter\ModelFilter;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3Filter extends ModelFilter
{
    public $relations = [];

    /**
     * 查询条件 计划员
     * @param $value
     */
    public function plannerNick($value)
    {
        $nickname  =  User::query()->where('username',$value)->orWhere('nickname',$value)->value('nickname');
        $this->where('planner_nick', '=', $nickname);
    }

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
     * 查询条件 计算批次(compute_batch)时间范围
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


    /**
     * 查询条件 备货方式
     * @param $value
     */
    public function stockWay($value)
    {
        $this->where('stock_way', '=', $value);
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
     * 查询条件 确认状态
     * @param $value
     */
    public function confirmStatus($value)
    {
        $this->where('confirm_status', '=', $value);
    }
}
