<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportSalesCountSkuDetailExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'ID',
            'SKU',
            '销售状态',
            '7天销量',
            '14天销量',
            '30天销量',
            '60天销量',
            '90天销量',
            '180天销量',
            '统计时间',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->orders_sku,
            $user->sales_status,
            $user->days_pcs7,
            $user->days_pcs14,
            $user->days_pcs30,
            $user->days_pcs60,
            $user->days_pcs90,
            $user->days_pcs180,
            $user->updated_at,
        ];
    }
}
