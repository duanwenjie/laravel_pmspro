<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportStockCountV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::ROUTE_MRP_REPORT_STOCK_COUNT_V3_LIST:
                return [
                    'sku'              => $this->sku,
                    'stockWayDesc'     => $this->stock_way_desc,
                    'salesStatusDesc'  => $this->sales_status_desc,
                    'orderDayTimes14'  => $this->order_day_times_14,
                    'fixedStockNum'    => $this->fixed_stock_num,
                    'daySales14'       => $this->day_sales_14,
                    'prCount'          => $this->pr_count,
                    'prNoPo'           => $this->pr_no_po,
                    'prPoNoPrint'      => $this->pr_po_no_print,
                    'purchaseOnWayNum' => $this->purchase_on_way_num,
                    'availableStock'   => $this->available_stock,
                    'actStock'         => $this->act_stock,
                    'newwmsUseQty'     => $this->newwms_use_qty,
                    'leaveQty'         => $this->leave_qty,
                    'occupyStock'      => $this->occupy_stock,
                    'totalStock'       => $this->total_stock,
                    'price'            => $this->price,
                    'orderPoint'       => $this->order_point,
                    'skuWareRecord'    => $this->sku_ware_record,
                    'computeBatch'     => $this->compute_batch,
                    'updatedAt'        => $this->updated_at,
                ];
                break;
        }
    }
}
