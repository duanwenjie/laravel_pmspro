<?php namespace App\ModelFilters\Mrp;

use App\Models\Department;
use App\Tools\Formater;
use EloquentFilter\ModelFilter;

/**
 * Class MrpReportOrdersModAftV3Filter
 * @package App\ModelFilters
 */
class MrpReportOrdersModAftV3Filter extends ModelFilter
{
    public $relations = [];


    /**
     * sku
     * @param $value
     * @return MrpReportOrdersModBefV3Filter
     */
    public function sku($value)
    {
        return $this->where('sku', $value);
    }

    /**
     * 统计时间
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
