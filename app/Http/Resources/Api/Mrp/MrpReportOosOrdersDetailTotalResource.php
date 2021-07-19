<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》总缺货订单明细
class MrpReportOosOrdersDetailTotalResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_DETAIL_TOTAL_LIST:
                return [
                    "packageCode"  => $this->package_code,
                    "sku"          => $this->sku,
                    "amount"       => $this->total_amount,
                    "orderStatus"  => $this->order_status,
                    "itemCount"    => $this->item_count,
                    "platform"     => $this->platform,
                    "salesAccount" => $this->sales_account,
                    "dwDate"       => $this->dw_date,
                    "paymentDate"  => $this->payment_date,
                ];
                break;
        }
    }
}
