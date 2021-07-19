<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》每日最新缺货占比统计报表
class MrpReportOosOrdersDV2Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_D_V2_LIST:
                return [
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
