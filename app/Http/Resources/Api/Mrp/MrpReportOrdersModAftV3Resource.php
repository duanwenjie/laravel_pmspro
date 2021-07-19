<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOrdersModAftV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::ROUTE_MRP_REPORT_ORDERS_MOD_AFT_V3_LIST:
                return [
                    'sku'                 => $this->sku,
                    'orderDayTimes14'     => $this->order_day_times_14,
                    'nearly2daysQty'      => $this->nearly2days_qty,
                    'daySales14'          => $this->day_sales_14,
                    'nearly3daysQty'      => $this->nearly3days_qty,
                    'nearly7daysQty'      => $this->nearly7days_qty,
                    'nearly13daysQty'     => $this->nearly13days_qty,
                    'nearly14daysQty'     => $this->nearly14days_qty,
                    'salesTrendDesc'      => $this->sales_trend_desc,
                    'stockingCoefficient' => $this->stocking_coefficient,
                    'computeBatch'        => $this->compute_batch,
                    'updatedAt'           => $this->updated_at,
                ];
                break;
        }
    }
}
