<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportStockCountSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_MRP_REPORT_STOCK_COUNT_SF_LIST:
                return [
                    'id'                => $this->id,//id
                    'sku'               => $this->sku,//SKU
                    'stockWay'          => $this->stock_way,//备货方式
                    'stockWayName'      => $this->stock_way_name,//备货方式描述
                    'salesStatus'       => $this->sales_status,//销售状态
                    'salesStatusName'   => $this->sales_status_name,//销售状态描述
                    'orderTimes'        => $this->order_times,//出单次数
                    'prCount'           => $this->pr_count,//PR数
                    'noOrderPrNum'      => $this->no_order_pr_num,//未生成PO
                    'noPrintNum'        => $this->no_print_num,//已建单且未打印
                    'purchaseOnWayNum'  => $this->purchase_on_way_num,//采购在途
                    'availableStockNum' => $this->available_stock_num,//可用库存
                    'actualStockNum'    => $this->actual_stock_num,//实际库存数量
                    'newwmsUseNum'      => $this->newwms_use_num,//WMS占用库存
                    'occupyStockNum'    => $this->occupy_stock_num,//总未发数量
                    'totalStockNum'     => $this->total_stock_num,//总可用库存
                    'skuWareRecord'     => $this->sku_ware_record,//入库标识
                    'computeBatch'      => $this->compute_batch,//计算批次
                    'updatedAt'         => $this->updated_at,//统计时间
                ];
                break;
            default:
                return [];
        }
    }
}
