<?php namespace App\ModelFilters\Mrp;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

//MRP(国内)-》历史每日缺货占比统计表
class MrpReportOosOrdersDHisV2Filter extends ModelFilter
{
    public $relations = [];


    /**
     * 统计时间
     * @param $value
     * @return Builder
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
