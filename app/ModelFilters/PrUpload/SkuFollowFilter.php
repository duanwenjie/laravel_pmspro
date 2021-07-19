<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/25
 * Time: 5:54 下午
 */

namespace App\ModelFilters\PrUpload;

use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class SkuFollowFilter extends ModelFilter
{
    public $relations = [];

    public function prId($value)
    {
        $this->where('pr_id', $value);
    }

    public function po($value)
    {
        $this->where('po', $value);
    }

    public function planner($value)
    {
        $this->where('planner', $value);
    }

    public function sku($value)
    {
        $this->where('sku', $value);
    }

    public function pmsPoDetailStatus($value)
    {
        $this->where('pms_po_detail_status', $value);
    }

    public function prDate($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0])) {
            $this->where('pr_date', '>=', $value[0]);
        }
        if (!empty($value[1])) {
            $this->where('pr_date', '<=', $value[1]);
        }
    }
}
