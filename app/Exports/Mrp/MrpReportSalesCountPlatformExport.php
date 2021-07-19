<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportSalesCountPlatformExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '平台',
            '7天销量',
            '14天销量',
            '28天销量',
            '累计待发销量',
            '统计时间',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->sku,
            $user->platform_code,
            $user->day_sales_7,
            $user->day_sales_14,
            $user->day_sales_28,
            $user->total_sales,
            $user->updated_at,
        ];
    }
}
