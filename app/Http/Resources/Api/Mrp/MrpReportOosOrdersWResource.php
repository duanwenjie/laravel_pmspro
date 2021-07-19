<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOosOrdersWResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_OOS_ORDERS_W_LIST:
                return [
                    'cancelOrdersQty'        => $this->cancel_orders_qty,
                    'totalOrdersQty'         => $this->total_orders_qty,
                    'cancelOrdersQtyRate'    => $this->cancel_orders_qty_rate,
                    'cancelOrdersAmount'     => $this->cancel_orders_amount,
                    'totalOrdersAmount'      => $this->total_orders_amount,
                    'cancelOrdersAmountRate' => $this->cancel_orders_amount_rate,
                    'ordersExportWeek'       => $this->orders_export_week,
                    'updatedAt'              => $this->updated_at,

                ];
                break;
        }
    }
}
