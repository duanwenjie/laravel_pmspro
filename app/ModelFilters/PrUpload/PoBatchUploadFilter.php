<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/25
 * Time: 5:54 下午
 */

namespace App\ModelFilters\PrUpload;

use App\Models\PrUpload\PrUploadSkusList;
use App\Tools\Formater;
use EloquentFilter\ModelFilter;

class PoBatchUploadFilter extends ModelFilter
{
    public $relations = [];

    public function userNick($value)
    {
        $this->where('user_nick', $value);
    }

    public function sku($value)
    {
        $this->whereIn('sku', $value);
    }

    public function spu($value)
    {
        $this->whereHas('skuInfo',function ($query) use ($value){
            $query->whereIn('spu',$value);
        });
    }

    public function id($value)
    {
        $this->whereIn('id', $value);
    }

    public function status($value)
    {
        if ($value == 1){ // 已生成采购单查询
            $this->whereIn('status', [
                PrUploadSkusList::handle,
                PrUploadSkusList::waitPurchase,
                PrUploadSkusList::print,
                PrUploadSkusList::poCancel,
            ]);
        }else{
            $this->where('status', $value);
        }
    }

    public function checkStatus($value)
    {
        $this->where('check_status', $value);
    }

    public function createdAt($value)
    {
        //前端返回时间
        $value = Formater::searchTime($value);
        if (!empty($value[0])) {
            $this->where('created_at', '>=', $value[0]);
        }
        if (!empty($value[1])) {
            $this->where('created_at', '<=', $value[1]);
        }
    }
}
