<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3Resource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::MRP_RESULT_PLAN_V3_LIST:
                return [
                    "id"                  => $this->id,
                    "sku"                 => $this->sku,
                    "skuName"             => $this->skuInfo->cn_name ?? '',   //SKU中文名称
                    'stockWay'            => $this->stock_way,   //备货方式
                    'stockWayName'        => $this->stock_way_name,//备货方式描述
                    'salesStatus'         => $this->sales_status, //销售状态
                    'salesStatusName'     => $this->sales_status_name,//销售状态描述
                    "warehouseid"         => $this->warehouseid,
                    "stockCycle"          => $this->stock_cycle,
                    "stockAdvanceCycle"   => $this->stock_advance_cycle,
                    "fixedStockNum"       => $this->fixed_stock_num,
                    "orderDayTimes14"     => $this->order_day_times_14,
                    "daySales14"          => $this->day_sales_14,
                    "sdvDaySales"         => $this->sdv_day_sales,
                    "nearly14daysQty"     => $this->nearly14days_qty,
                    "salesTrend"          => $this->sales_trend,
                    "stockingCoefficient" => $this->stocking_coefficient,
                    "prCount"             => $this->pr_count,
                    "purchaseOnWayNum"    => $this->purchase_on_way_num,
                    "actualStockNum6"     => $this->actual_stock_num_6,
                    "availableStockNum"   => $this->available_stock_num,
                    "newwmsUseNum"        => $this->newwms_use_num,
                    "actualStockNum"      => $this->actual_stock_num,
                    "occupyStockNum"      => $this->occupy_stock_num,
                    "totalStockNum"       => $this->total_stock_num,
                    "leaveNum"            => $this->leave_num,
                    "price"               => $this->price,
                    "orderPoint"          => $this->order_point,
                    "replenishmentNum"    => $this->replenishment_num,
                    "remark"              => $this->remark,
                    "requestDate"         => $this->request_date,
                    "computeBatch"        => $this->compute_batch,
                    "updatedAt"           => $this->updated_at,
                    'confirmStatus'       => $this->confirm_status,//确认状态
                    'confirmStatusName'   => $this->confirm_status_name,//确认状态描述
                    "plannerNick"         => $this->planner_nick,
                    "skuMark"             => $this->skuCore->sku_mark ?? '',   //产品标识
                ];
                break;
        }
    }
}
