<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportDaySalesCountSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_MRP_REPORT_DAY_SALES_COUNT_SF_LIST:
                return [
                    'id'                                => $this->id,//id
                    'SKU'                               => $this->sku,//SKU
                    date('Y-m-d', strtotime('-0 day'))  => $this->old_day_sales_1,//历史1天销量
                    date('Y-m-d', strtotime('-1 day'))  => $this->old_day_sales_2,//历史2天销量
                    date('Y-m-d', strtotime('-2 day'))  => $this->old_day_sales_3,//历史3天销量
                    date('Y-m-d', strtotime('-3 day'))  => $this->old_day_sales_4,//历史4天销量
                    date('Y-m-d', strtotime('-4 day'))  => $this->old_day_sales_5,//历史5天销量
                    date('Y-m-d', strtotime('-5 day'))  => $this->old_day_sales_6,//历史6天销量
                    date('Y-m-d', strtotime('-6 day'))  => $this->old_day_sales_7,//历史7天销量
                    date('Y-m-d', strtotime('-7 day'))  => $this->old_day_sales_8,//历史8天销量
                    date('Y-m-d', strtotime('-8 day'))  => $this->old_day_sales_9,//历史9天销量
                    date('Y-m-d', strtotime('-9 day'))  => $this->old_day_sales_10,//历史10天销量
                    date('Y-m-d', strtotime('-10 day')) => $this->old_day_sales_11,//历史11天销量
                    date('Y-m-d', strtotime('-11 day')) => $this->old_day_sales_12,//历史12天销量
                    date('Y-m-d', strtotime('-12 day')) => $this->old_day_sales_13,//历史13天销量
                    date('Y-m-d', strtotime('-13 day')) => $this->old_day_sales_14,//历史14天销量
                    date('Y-m-d', strtotime('-14 day')) => $this->old_day_sales_15,//历史15天销量
                    date('Y-m-d', strtotime('-15 day')) => $this->old_day_sales_16,//历史16天销量
                    date('Y-m-d', strtotime('-16 day')) => $this->old_day_sales_17,//历史17天销量
                    date('Y-m-d', strtotime('-17 day')) => $this->old_day_sales_18,//历史18天销量
                    date('Y-m-d', strtotime('-18 day')) => $this->old_day_sales_19,//历史19天销量
                    date('Y-m-d', strtotime('-19 day')) => $this->old_day_sales_20,//历史20天销量
                    date('Y-m-d', strtotime('-20 day')) => $this->old_day_sales_21,//历史21天销量
                    date('Y-m-d', strtotime('-21 day')) => $this->old_day_sales_22,//历史22天销量
                    date('Y-m-d', strtotime('-22 day')) => $this->old_day_sales_23,//历史23天销量
                    date('Y-m-d', strtotime('-23 day')) => $this->old_day_sales_24,//历史24天销量
                    date('Y-m-d', strtotime('-24 day')) => $this->old_day_sales_25,//历史25天销量
                    date('Y-m-d', strtotime('-25 day')) => $this->old_day_sales_26,//历史26天销量
                    date('Y-m-d', strtotime('-26 day')) => $this->old_day_sales_27,//历史27天销量
                    date('Y-m-d', strtotime('-27 day')) => $this->old_day_sales_28,//历史28天销量
                    date('Y-m-d', strtotime('-28 day')) => $this->old_day_sales_29,//历史29天销量
                    date('Y-m-d', strtotime('-29 day')) => $this->old_day_sales_30,//历史30天销量
                    '统计批次'                              => $this->compute_batch,//统计批次
                    '同步时间'                              => $this->updated_at,//同步时间
                ];
                break;
            default:
                return [];
        }
    }
}
