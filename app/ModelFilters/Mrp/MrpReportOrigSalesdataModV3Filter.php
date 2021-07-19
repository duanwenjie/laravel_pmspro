<?php

namespace App\ModelFilters\Mrp;

use App\Services\MrpBaseData\ReportService;
use App\Tools\Formater;
use EloquentFilter\ModelFilter;

//销量源数据（修正后）
class MrpReportOrigSalesdataModV3Filter extends ModelFilter
{
    public $relations = [];

    //自动添加字段值
    public function setup()
    {
        //排序字段不存在，添加默认排序字段
        if (!$this->input('platform_code')) {
            $this->push('platform_code', ReportService::delPtCodeV3);
        }
    }

    //默认查询条件
    public function platformCode($value){
        $this->whereNotIn('platform_code', $value);
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
}
