<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportDaySalesCountExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '序号',
            'SKU',
            date('Y-m-d', strtotime('-29 day')),
            date('Y-m-d', strtotime('-28 day')),
            date('Y-m-d', strtotime('-27 day')),
            date('Y-m-d', strtotime('-26 day')),
            date('Y-m-d', strtotime('-25 day')),
            date('Y-m-d', strtotime('-24 day')),
            date('Y-m-d', strtotime('-23 day')),
            date('Y-m-d', strtotime('-22 day')),
            date('Y-m-d', strtotime('-21 day')),
            date('Y-m-d', strtotime('-20 day')),
            date('Y-m-d', strtotime('-19 day')),
            date('Y-m-d', strtotime('-18 day')),
            date('Y-m-d', strtotime('-17 day')),
            date('Y-m-d', strtotime('-16 day')),
            date('Y-m-d', strtotime('-15 day')),
            date('Y-m-d', strtotime('-14 day')),
            date('Y-m-d', strtotime('-13 day')),
            date('Y-m-d', strtotime('-12 day')),
            date('Y-m-d', strtotime('-11 day')),
            date('Y-m-d', strtotime('-10 day')),
            date('Y-m-d', strtotime('-9 day')),
            date('Y-m-d', strtotime('-8 day')),
            date('Y-m-d', strtotime('-7 day')),
            date('Y-m-d', strtotime('-6 day')),
            date('Y-m-d', strtotime('-5 day')),
            date('Y-m-d', strtotime('-4 day')),
            date('Y-m-d', strtotime('-3 day')),
            date('Y-m-d', strtotime('-2 day')),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('-0 day')),
            '统计时间',
            '同步时间'
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->sku,
            $report->old_day_sales_30?:'0',
            $report->old_day_sales_29?:'0',
            $report->old_day_sales_28?:'0',
            $report->old_day_sales_27?:'0',
            $report->old_day_sales_26?:'0',
            $report->old_day_sales_25?:'0',
            $report->old_day_sales_24?:'0',
            $report->old_day_sales_23?:'0',
            $report->old_day_sales_22?:'0',
            $report->old_day_sales_21?:'0',
            $report->old_day_sales_20?:'0',
            $report->old_day_sales_19?:'0',
            $report->old_day_sales_18?:'0',
            $report->old_day_sales_17?:'0',
            $report->old_day_sales_16?:'0',
            $report->old_day_sales_15?:'0',
            $report->old_day_sales_14?:'0',
            $report->old_day_sales_13?:'0',
            $report->old_day_sales_12?:'0',
            $report->old_day_sales_11?:'0',
            $report->old_day_sales_10?:'0',
            $report->old_day_sales_9?:'0',
            $report->old_day_sales_8?:'0',
            $report->old_day_sales_7?:'0',
            $report->old_day_sales_6?:'0',
            $report->old_day_sales_5?:'0',
            $report->old_day_sales_4?:'0',
            $report->old_day_sales_3?:'0',
            $report->old_day_sales_2?:'0',
            $report->old_day_sales_1?:'0',
            $report->compute_batch,
            $report->updated_at,
        ];
    }
}
