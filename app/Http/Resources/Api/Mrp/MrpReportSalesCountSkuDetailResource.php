<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportSalesCountSkuDetailResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_SALES_COUNT_SKU_DETAIL_LIST:
                return [
                    'id'          => $this->id,
                    'ordersSku'   => $this->orders_sku,
                    'salesStatus' => $this->sales_status,
                    'daysPPcs7'   => $this->days_pcs7,
                    'daysPcs14'   => $this->days_pcs14,
                    'daysPcs30'   => $this->days_pcs30,
                    'daysPcs60'   => $this->days_pcs60,
                    'daysPcs90'   => $this->days_pcs90,
                    'daysPcs180'  => $this->days_pcs180,
                    'updatedAt'   => $this->updated_at,

                ];
                break;
        }
    }
}
