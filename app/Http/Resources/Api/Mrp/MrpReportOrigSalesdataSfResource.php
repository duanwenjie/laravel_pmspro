<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOrigSalesdataSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_MRP_REPORT_ORIG_SALESDATA_SF_LIST:
                return [
                    'id'               => $this->id, //id
                    'packageCode'      => $this->package_code,//包裹单号
                    'sku'              => $this->sku,//sku
                    'itemCount'        => $this->item_count,//数量
                    'platform'         => $this->platform,//平台
                    'warehouse'        => $this->warehouse,//仓库名称
                    'warehouseId'      => $this->warehouseid,//仓库ID
                    'ordersExportTime' => $this->orders_export_time,//创建时间（进ERP
                    'paymentDate'      => $this->payment_date,//付款时间
                    'orderCreateTime'  => $this->order_create_time,//创建时间（平台）
                    'outTime'          => $this->out_time,//出库时间
                    'ordersOutTime'    => $this->orders_out_time,//发货时间
                    'computeBatch'     => $this->compute_batch,//计算批次
                    'updatedAt'        => $this->updated_at//统计时间
                ];
                break;
            default:
                return [];
        }
    }
}
