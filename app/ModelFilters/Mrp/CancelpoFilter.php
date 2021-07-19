<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/1
 * Time: 18:09
 */


namespace App\ModelFilters\Mrp;


use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class CancelpoFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 查询条件 SKU名称
     * @param $value
     */
    public function sku($value)
    {
        $this->whereIn('pd.sku', $value);
    }

    /**
     * 查询条件 采购时间(createTime)时间范围
     * @param $value
     */
    public function createTime($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('p.create_time', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('p.create_time', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('p.create_time', $value);
        }
    }

    /**
     * 汇总表计算时间
     */
    public function btCreateTime($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('pd.bt_create_time', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('pd.bt_create_time', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('pd.bt_create_time', $value);
        }
    }

    /**
     * 明细-查询条件 SKU名称
     * @param $value
     */
    public function detailSku($value)
    {
        $this->whereIn('sku', $value);
    }

    /**
     * 明细-查询条件 计算批次
     * @param $value
     */
    public function detailBtNo($value)
    {
        $this->whereIn('bt_no', $value);
    }

    /**
     * 明细-查询条件 采购单单号
     * @param $value
     */
    public function detailPo($value)
    {
        $this->whereIn('po_id', $value);
    }

    /**
     * 明细-查询条件 采购时间(createTime)时间范围
     * @param $value
     */
    public function detailCreateTime($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('create_time', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('create_time', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('create_time', $value);
        }
    }

    /**
     * 明细-查询条件 计算时间(detailBtCreateTime)时间范围
     * @param $value
     */
    public function detailBtCreateTime($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0]) && empty($value[1])) {
            $this->where('bt_create_time', '>', $value[0]);
        }
        if (!empty($value[1]) && empty($value[0])) {
            $this->where('bt_create_time', '<', $value[1]);
        }
        if (!empty($value[0]) && !empty($value[1])) {
            $this->whereBetween('bt_create_time', $value);
        }
    }

}
