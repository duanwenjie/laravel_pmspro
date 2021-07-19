<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpResultPlanSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_MRP_RESULT_PLAN_SF_LIST:
                return [
                    'sku'               => $this->sku,//sku
                    'skuName'           => $this->skuInfo['cn_name'] ?? '', //sku中文名称
                    'stockWay'          => $this->stock_way,   //备货方式
                    'stockWayName'      => $this->stock_way_name,//备货方式描述
                    'salesStatus'       => $this->sales_status, //销售状态
                    'salesStatusName'   => $this->sales_status_name,//销售状态描述
                    'warehouseid'       => $this->warehouseid, //主仓库id
                    'fixedStockNum'     => $this->fixed_stock_num, //特定备货数量
                    'bufferStockCycle'  => $this->buffer_stock_cycle, // 安全库存天数
                    'supplyCycle'       => $this->supply_cycle,// 交期
                    'orderTimes'        => $this->order_times,//出单次数
                    'daySales'          => $this->day_sales,//日均销量
                    'nearly1daysQty'    => $this->nearly1days_qty,// 倒推第1天销量
                    'nearly2daysQty'    => $this->nearly2days_qty,//倒推第2天销量
                    'nearly3daysQty'    => $this->nearly3days_qty,//倒推第3天销量
                    'salesTrend'        => $this->sales_trend,//销量趋势
                    'salesTrendDes'     => $this->sales_trend_des,//销量趋势描述
                    'prCount'           => $this->pr_count,//PR数
                    'purchaseOnWayNum'  => $this->purchase_on_way_num,//采购在途
                    'availableStockNum' => $this->available_stock_num,//可用库存
                    'actualStockNum'    => $this->actual_stock_num,//实际库存数量
                    'newwmsUseNum'      => $this->newwms_use_num,//WMS占用库存
                    'occupyStockNum'    => $this->occupy_stock_num,//总未发数量
                    'totalStockNum'     => $this->total_stock_num,//总可用库存
                    'orderPoint'        => $this->order_point,//订购点
                    'replenishmentNum'  => $this->replenishment_num,//补货数
                    'requestDate'       => $this->request_date,//需求日期
                    'skuMark'           => $this->skuMark,//产品标志
                    'price'             => $this->price,//单价
                    'computeBatch'      => $this->compute_batch,//计算批次
                    'updatedAt'         => $this->updated_at,//统计时间
                    'confirmStatus'     => $this->confirm_status,//确认状态
                    'confirmStatusName' => $this->confirm_status_name,//确认状态描述
                    'plannerNick'       => $this->planner_nick,//计划员
                ];
                break;
            default:
                return [];
        }
    }
}
