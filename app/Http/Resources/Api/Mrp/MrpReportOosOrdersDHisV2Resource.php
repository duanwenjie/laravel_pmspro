<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》历史每日缺货占比统计表
class MrpReportOosOrdersDHisV2Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_D_HIS_V2_LIST:
                return [
                    "platform"               => $this->platform,
                    "cancelOrdersQty"        => $this->cancel_orders_qty,
                    "totalOrdersQty"         => $this->total_orders_qty,
                    "cancelOrdersQtyRate"    => $this->cancel_orders_qty_rate,
                    "cancelOrdersAmount"     => $this->cancel_orders_amount,
                    "totalOrdersAmount"      => $this->total_orders_amount,
                    "cancelOrdersAmountRate" => $this->cancel_orders_amount_rate,
                    "paymentDate"            => $this->payment_date,
                    "updatedAt"              => $this->updated_at,
                ];
                break;
        }
    }
}
