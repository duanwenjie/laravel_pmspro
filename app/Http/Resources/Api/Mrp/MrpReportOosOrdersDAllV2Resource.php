<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》撤单和缺货订单日统计
class MrpReportOosOrdersDAllV2Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_D_ALL_V2_LIST:
                return [
                    "cancelOrdersQty"    => $this->cancel_orders_qty,
                    "qhOrdersQty"        => $this->qh_orders_qty,
                    "cancelOrdersAmount" => $this->cancel_orders_amount,
                    "qhOrdersAmount"     => $this->qh_orders_amount,
                    "ordersExportTime"   => $this->orders_export_time,
                    "updatedAt"          => $this->updated_at,

                ];
                break;
        }
    }
}
