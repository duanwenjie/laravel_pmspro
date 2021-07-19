<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportSalesCountPlatformResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_SALES_COUNT_PLATFORM_LIST:
                return [
                    'id'            => $this->id,
                    'sku'           => $this->sku,
                    'platform_code' => $this->platform_code,
                    'daySales7'     => $this->day_sales_7,
                    'daySales14'    => $this->day_sales_14,
                    'daySales28'    => $this->day_sales_28,
                    'totalSales'    => $this->total_sales,
                    'updatedAt'     => $this->updated_at,

                ];
                break;
        }
    }
}
