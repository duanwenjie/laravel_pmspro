<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//销量源数据
class MrpReportOrigSalesdataV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_ORIG_SALESDATA_V3_LIST:
                return [
                    'id'              => $this->id,
                    'packageCode'     => $this->package_code,
                    'SKU'             => $this->sku,
                    'itemCount'       => $this->item_count,
                    'platform'        => $this->platform,
                    'price'           => $this->price,
                    'avgDaySales'     => $this->avg_day_sales,
                    'nearly14daysQty' => $this->nearly14days_qty,
                    'salesAccount'    => $this->sales_account,
                    'businessType'    => $this->business_type,
                    'zgAccount'       => $this->zg_account,
                    'jlAccount'       => $this->jl_account,
                    'warehouseid'     => $this->warehouseid,
                    'paymentDate'     => $this->payment_date,
                    'computeBatch'    => $this->compute_batch,
                    'updatedAt'       => $this->updated_at,
                ];
                break;
        }
    }
}
