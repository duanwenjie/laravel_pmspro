<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOrdersSfExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '备货方式',
            '销售状态',
            '出单次数',
            '倒推第1天销量',
            '倒推第2天销量',
            '倒推第3天销量',
            '销量趋势',
            '近7天销量',
            '近14天销量',
            '近30天销量',
            '近55天销量',
            '日均销量',
            '订购点',
            '计算批次',
            '统计时间',
        ];
    }

    public function map($report): array
    {
        return [
            $report->sku,
            $report->stock_way_name,
            $report->sales_status_name,
            $report->order_times,
            $report->nearly1days_qty,
            $report->nearly2days_qty,
            $report->nearly3days_qty,
            $report->sales_trend_des,
            $report->nearly7days_qty,
            $report->nearly14days_qty,
            $report->nearly30days_qty,
            $report->nearly55days_qty,
            $report->day_sales,
            $report->order_point,
            $report->compute_batch,
            $report->updated_at,

        ];
    }
}
