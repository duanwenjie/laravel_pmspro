<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpReportOrdersSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_MRP_REPORT_ORDERS_SF_LIST:
                return [
                    'sku'             => $this->sku, //sku
                    'stockWay'        => $this->stock_way,//备货方式
                    'stockWayName'    => $this->stock_way_name,
                    'salesStatus'     => $this->sales_status,//销售状态
                    'salesStatusName' => $this->sales_status_name,
                    'orderTimes'      => $this->order_times,//出单次数
                    'nearly1daysQty'  => $this->nearly1days_qty,//倒推第1天销量
                    'nearly2daysQty'  => $this->nearly2days_qty,//倒推第2天销量
                    'nearly3daysQty'  => $this->nearly3days_qty,//倒推第3天销量
                    'salesTrend'      => $this->sales_trend,//销量趋势
                    'salesTrendDes'   => $this->sales_trend_des,//销量趋势描述
                    'nearly7daysQty'  => $this->nearly7days_qty,//近7天销量
                    'nearly14daysQty' => $this->nearly14days_qty,//近14天销量
                    'nearly30daysQty' => $this->nearly30days_qty,//近30天销量
                    'nearly55daysQty' => $this->nearly55days_qty,//近55天销量
                    'daySales'        => $this->day_sales,//日均销量
                    'orderPoint'      => $this->order_point,//订购点
                    'computeBatch'    => $this->compute_batch,//计算批次
                    'updatedAt'       => $this->updated_at,//统计时间
                ];
                break;
            default:
                return [];
        }
    }
}
