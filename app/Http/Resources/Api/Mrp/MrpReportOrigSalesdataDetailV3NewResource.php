<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOrigSalesdataDetailV3NewResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_ORIG_SALESDATA_DETAIL_V3_NEW_LIST:
                return [
                    'id'                                => $this->id,
                    'SKU'                               => $this->sku,
                    date('Y-m-d', strtotime('-0 day'))  => $this->old_day_sales_1?:0,
                    date('Y-m-d', strtotime('-1 day'))  => $this->old_day_sales_2?:0,
                    date('Y-m-d', strtotime('-2 day'))  => $this->old_day_sales_3?:0,
                    date('Y-m-d', strtotime('-3 day'))  => $this->old_day_sales_4?:0,
                    date('Y-m-d', strtotime('-4 day'))  => $this->old_day_sales_5?:0,
                    date('Y-m-d', strtotime('-5 day'))  => $this->old_day_sales_6?:0,
                    date('Y-m-d', strtotime('-6 day'))  => $this->old_day_sales_7?:0,
                    date('Y-m-d', strtotime('-7 day'))  => $this->old_day_sales_8?:0,
                    date('Y-m-d', strtotime('-8 day'))  => $this->old_day_sales_9?:0,
                    date('Y-m-d', strtotime('-9 day'))  => $this->old_day_sales_10?:0,
                    date('Y-m-d', strtotime('-10 day')) => $this->old_day_sales_11?:0,
                    date('Y-m-d', strtotime('-11 day')) => $this->old_day_sales_12?:0,
                    date('Y-m-d', strtotime('-12 day')) => $this->old_day_sales_13?:0,
                    date('Y-m-d', strtotime('-13 day')) => $this->old_day_sales_14?:0,
                    '离散系数'                              => $this->sdv_day_sales,
                    '日均销量'                              => $this->avg_day_sales,
                    '计算批次'                              => $this->compute_batch,
                    '统计时间'                              => $this->updated_at,

                ];
                break;
        }
    }
}
