<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

//销量源数据（修正后）
class MrpReportOrigSalesdataModV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_ORIG_SALESDATA_MOD_V3_LIST:
                return [
                    'id'           => $this->id,
                    'packageCode'  => $this->package_code,
                    'SKU'          => $this->sku,
                    'itemCount'    => $this->item_count,
                    'platformCode' => $this->platform_code,
                    'warehouseid'  => $this->warehouseid,
                    'paymentDate'  => $this->payment_date,
                    'computeBatch' => $this->compute_batch,
                    'updatedAt'    => $this->updated_at,

                ];
                break;
        }
    }
}
