<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//MRP(国内)-》MRP V3-》销量-SKU统计
class MrpReportSalesCountSkuResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_SALES_COUNT_SKU_LIST:
                return [
                    "id"              => $this->id,
                    "skuCount"        => $this->sku_count,
                    "daysSkuCount7"   => $this->days_sku_count7,
                    "daysSkuCount14"  => $this->days_sku_count14,
                    "daysSkuCount30"  => $this->days_sku_count30,
                    "daysSkuCount60"  => $this->days_sku_count60,
                    "daysSkuCount90"  => $this->days_sku_count90,
                    "daysSkuCount180" => $this->days_sku_count180,
                    "updatedAt"       => $this->updated_at,

                ];
                break;
        }
    }
}
