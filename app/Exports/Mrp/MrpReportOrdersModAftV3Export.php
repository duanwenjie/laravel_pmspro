<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOrdersModAftV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '近2天日均销量',
            '出单天数',
            '前14天总销量',
            '前3天日均销量',
            '前7天日均销量',
            '前13天日均销量',
            '前14天日均销量',
            '销量趋势',
            '备货系数',
            '计算批次',
            '统计时间'
        ];
    }

    public function map($report): array
    {
        return [
            $report->sku,
            $report->nearly2days_qty,
            $report->order_day_times_14,
            $report->day_sales_14,
            $report->nearly3days_qty,
            $report->nearly7days_qty,
            $report->nearly13days_qty,
            $report->nearly14days_qty,
            $report->sales_trend_desc,
            $report->stocking_coefficient,
            $report->compute_batch,
            $report->updated_at,
        ];
    }
}
