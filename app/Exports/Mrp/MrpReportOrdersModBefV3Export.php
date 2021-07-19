<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOrdersModBefV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '前3天日均销量',
            '前7天日均销量',
            '前14天日均',
            '前14天总销量',
            '是否触发销量调整:1触发,-1不触发',
            '计算批次',
            '统计时间'
        ];
    }

    public function map($user): array
    {
        return [
            $user->sku,
            $user->stock_way_desc,
            $user->sales_status_desc,
            $user->nearly3days_qty,
            $user->nearly7days_qty,
            $user->nearly14days_qty,
            $user->day_sales_14,
            $user->mod_condition_desc,
            $user->compute_batch,
            $user->updated_at,
        ];
    }
}
