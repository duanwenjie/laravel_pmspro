<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpResultPlanSfExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    public function query()
    {
        return $this->builder;
    }

    public function headings(): array
    {
        return [
            'SKU',
            '中文名称',
            '备货方式',
            '销售状态',
            '主仓库id',
            '特定备货数量',
            '安全库存天数',
            '交期',
            '出单次数',
            '日均销量',
            '倒推第1天销量',
            '倒推第2天销量',
            '倒推第3天销量',
            '销量趋势',
            'PR数',
            '采购在途',
            '可用库存',
            '实际库存数量',
            'WMS占用库存',
            '总未发数量',
            '总可用库存',
            '订购点',
            '补货数',
            '需求日期',
            '产品标志',
            '单价',
            '计算批次',
            '统计时间',
            '确认状态',
            '计划员',
        ];
    }

    public function map($report): array
    {
        return [
            $report->sku,//sku
            $report->skuInfo->cn_name, //sku中文名称
            $report->stock_way_name,//备货方式描述
            $report->sales_status_name,//销售状态描述
            $report->warehouseid, //主仓库id
            $report->fixed_stock_num, //特定备货数量
            $report->buffer_stock_cycle, // 安全库存天数
            $report->supply_cycle,// 交期
            $report->order_times,//出单次数
            $report->day_sales,//日均销量
            $report->nearly1days_qty,// 倒推第1天销量
            $report->nearly2days_qty,//倒推第2天销量
            $report->nearly3days_qty,//倒推第3天销量
            $report->sales_trend_des,//销量趋势
            $report->pr_count,//PR数
            $report->purchase_on_way_num,//采购在途
            $report->available_stock_num,//可用库存
            $report->actual_stock_num,//实际库存数量
            $report->newwms_use_num,//WMS占用库存
            $report->occupy_stock_num,//总未发数量
            $report->total_stock_num,//总可用库存
            $report->order_point,//订购点
            $report->replenishment_num,//补货数
            $report->request_date,//需求日期
            $report->sku_mark,//产品标志
            $report->price,//单价
            $report->compute_batch,//计算批次
            $report->updated_at,//统计时间
            $report->confirm_status_name,//确认状态描述
            $report->planner_nick,//计划员
        ];
    }
}
