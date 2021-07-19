<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》每日缺货订单明细
class MrpReportOosOrdersDetailDailyResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_DETAIL_DAILY_LIST:
                return [
                    "packageCode" => $this->package_code,
                    "sku"         => $this->sku,
                    "totalAmount" => $this->total_amount,
                    "orderStatus" => $this->order_status,
                    "itemCount"   => $this->item_count,
                    "platform"    => $this->platform,
                    "dwDate"      => $this->dw_date,
                    "paymentDate" => $this->payment_date,
                ];
                break;
        }
    }
}
