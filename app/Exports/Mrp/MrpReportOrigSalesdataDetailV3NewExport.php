<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOrigSalesdataDetailV3NewExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'id',
            'SKU',
            date('Y-m-d', strtotime('-0 day')),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('-2 day')),
            date('Y-m-d', strtotime('-3 day')),
            date('Y-m-d', strtotime('-4 day')),
            date('Y-m-d', strtotime('-5 day')),
            date('Y-m-d', strtotime('-6 day')),
            date('Y-m-d', strtotime('-7 day')),
            date('Y-m-d', strtotime('-8 day')),
            date('Y-m-d', strtotime('-9 day')),
            date('Y-m-d', strtotime('-10 day')),
            date('Y-m-d', strtotime('-11 day')),
            date('Y-m-d', strtotime('-12 day')),
            date('Y-m-d', strtotime('-13 day')),
            '离散系数',
            '日均销量',
            '计算批次',
            '统计时间'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->sku,
            $user->old_day_sales_1?:'0',
            $user->old_day_sales_2?:'0',
            $user->old_day_sales_3?:'0',
            $user->old_day_sales_4?:'0',
            $user->old_day_sales_5?:'0',
            $user->old_day_sales_6?:'0',
            $user->old_day_sales_7?:'0',
            $user->old_day_sales_8?:'0',
            $user->old_day_sales_9?:'0',
            $user->old_day_sales_10?:'0',
            $user->old_day_sales_11?:'0',
            $user->old_day_sales_12?:'0',
            $user->old_day_sales_13?:'0',
            $user->old_day_sales_14?:'0',
            $user->sdv_day_sales,
            $user->avg_day_sales,
            $user->compute_batch,
            $user->updated_at,
        ];
    }
}
