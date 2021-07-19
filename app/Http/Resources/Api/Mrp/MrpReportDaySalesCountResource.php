<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportDaySalesCountResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_REPORT_DAY_SALES_COUNT_LIST:
                return [
                    '序号'                                => $this->id,
                    'SKU'                               => $this->sku,
                    date('Y-m-d', strtotime('-29 day')) => $this->old_day_sales_30,
                    date('Y-m-d', strtotime('-28 day')) => $this->old_day_sales_29,
                    date('Y-m-d', strtotime('-27 day')) => $this->old_day_sales_28,
                    date('Y-m-d', strtotime('-26 day')) => $this->old_day_sales_27,
                    date('Y-m-d', strtotime('-25 day')) => $this->old_day_sales_26,
                    date('Y-m-d', strtotime('-24 day')) => $this->old_day_sales_25,
                    date('Y-m-d', strtotime('-23 day')) => $this->old_day_sales_24,
                    date('Y-m-d', strtotime('-22 day')) => $this->old_day_sales_23,
                    date('Y-m-d', strtotime('-21 day')) => $this->old_day_sales_22,
                    date('Y-m-d', strtotime('-20 day')) => $this->old_day_sales_21,
                    date('Y-m-d', strtotime('-19 day')) => $this->old_day_sales_20,
                    date('Y-m-d', strtotime('-18 day')) => $this->old_day_sales_19,
                    date('Y-m-d', strtotime('-17 day')) => $this->old_day_sales_18,
                    date('Y-m-d', strtotime('-16 day')) => $this->old_day_sales_17,
                    date('Y-m-d', strtotime('-15 day')) => $this->old_day_sales_16,
                    date('Y-m-d', strtotime('-14 day')) => $this->old_day_sales_15,
                    date('Y-m-d', strtotime('-13 day')) => $this->old_day_sales_14,
                    date('Y-m-d', strtotime('-12 day')) => $this->old_day_sales_13,
                    date('Y-m-d', strtotime('-11 day')) => $this->old_day_sales_12,
                    date('Y-m-d', strtotime('-10 day')) => $this->old_day_sales_11,
                    date('Y-m-d', strtotime('-9 day'))  => $this->old_day_sales_10,
                    date('Y-m-d', strtotime('-8 day'))  => $this->old_day_sales_9,
                    date('Y-m-d', strtotime('-7 day'))  => $this->old_day_sales_8,
                    date('Y-m-d', strtotime('-6 day'))  => $this->old_day_sales_7,
                    date('Y-m-d', strtotime('-5 day'))  => $this->old_day_sales_6,
                    date('Y-m-d', strtotime('-4 day'))  => $this->old_day_sales_5,
                    date('Y-m-d', strtotime('-3 day'))  => $this->old_day_sales_4,
                    date('Y-m-d', strtotime('-2 day'))  => $this->old_day_sales_3,
                    date('Y-m-d', strtotime('-1 day'))  => $this->old_day_sales_2,
                    date('Y-m-d', strtotime('-0 day'))  => $this->old_day_sales_1,
                    '统计时间'                              => $this->compute_batch,
                    '同步时间'                              => $this->updated_at,
                ];
                break;
        }
    }
}
