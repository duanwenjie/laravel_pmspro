<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOosOrdersDResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_D_LIST:
                return [
                    'cancelOrdersQty'       => $this->cancel_orders_qty,
                    'totalOrdersQty'        => $this->total_orders_qty,
                    'cancelOrdersQtyRate'   => $this->cancel_orders_qty_rate,
                    'cancelOrdersAmount'    => $this->cancel_orders_amount,
                    'totalOrdersAmount'     => $this->total_orders_amount,
                    'cancelOdersAmountRate' => $this->cancel_orders_amount_rate,
                    'ordersExportTime'      => $this->orders_export_time,
                    'updatedAt'             => $this->updated_at,

                ];
                break;
        }
    }
}
