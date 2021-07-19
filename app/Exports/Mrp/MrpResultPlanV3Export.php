<?php

namespace App\Exports\Mrp;

use App\Http\ConfigBase\ConfigBase;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $builder;
    protected $stockWays;
    protected $salesStatuses;
    protected $confirmStatuses;

    public function __construct($builder)
    {
        $this->builder = $builder;
        $this->stockWays = ConfigBase::getStockWayMap();
        $this->salesStatuses = ConfigBase::getSalesStatusMap();
        $this->confirmStatuses = ConfigBase::getConfirmStatusMap();
    }

    public function query()
    {
        return $this->builder;
    }

    public function headings(): array
    {
        return [
            "id",
            "SKU",
            "SKU中文名称",
            "备货方式",
            "销售状态",
            "主仓库id",
            "补货天数",
            "库内库存天数",
            "特定备货数量",
            "出单天数",
            "前14天总销量",
            "离散系数",
            "前14天日均销量",
            "销量趋势",
            "备货系数",
            "PR数",
            "采购在途",
            "可用库存",
            "WMS占用库存",
            "实际库存数量",
            "总未发数量",
            "总可用库存",
            "返仓库存",
            "采购单价",
            "订购点",
            "补货数",
            "备注",
            "需求日期",
            "计算批次",
            "统计时间",
            "确认状态",
            "计划员",
            "产品标识"
        ];
    }

    public function map($one): array
    {
        return [
            $one->id,
            $one->sku,
            $one->skuInfo->cn_name ?? '',
            ($this->stockWays[$one->stock_way] ?? $one->stock_way),
            ($this->salesStatuses[$one->sales_status] ?? $one->sales_status),
            $one->warehouseid,
            $one->stock_cycle,
            $one->stock_advance_cycle,
            $one->fixed_stock_num,
            $one->order_day_times_14,
            $one->day_sales_14,
            $one->sdv_day_sales,
            $one->nearly14days_qty,
            ($one->sales_trend==1?'上涨':($one->sales_trend==-1?'下降':(($one->sales_trend==0?'趋势不明':$one->sales_trend)))),
            $one->stocking_coefficient,
            $one->pr_count,
            $one->purchase_on_way_num,
            $one->available_stock_num,
            $one->newwms_use_num,
            $one->actual_stock_num,
            $one->occupy_stock_num,
            $one->total_stock_num,
            $one->leave_num,
            $one->price,
            $one->order_point,
            $one->replenishment_num,
            $one->remark,
            $one->request_date,
            $one->compute_batch,
            $one->updated_at,
            ($this->confirmStatuses[$one->confirm_status] ?? $one->confirm_status),
            $one->planner_nick,
            $one->skuCore->sku_mark ?? '',   //产品标识
        ];
    }
}
