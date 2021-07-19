<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOrdersModBefV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::ROUTE_MRP_SKU_ORDERS_MOD_BEF_V3_LIST:
                return [
                    'sku'              => $this->sku,
                    'salesStatusDesc'  => $this->sales_status_desc,
                    'stockWayDesc'     => $this->stock_way_desc,
                    'nearly3daysQty'   => $this->nearly3days_qty,
                    'nearly7daysQty'   => $this->nearly7days_qty,
                    'nearly14daysQty'  => $this->nearly14days_qty,
                    'daySales14'       => $this->day_sales_14,
                    'modConditionDesc' => $this->mod_condition_desc,
                    'computeBatch'     => $this->compute_batch,
                    'updatedAt'        => $this->updated_at,

                ];
                break;
        }
    }
}
