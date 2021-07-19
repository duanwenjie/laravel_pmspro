<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOosOrdersMExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '库存撤单订单个数',
            '总销售订单个数',
            '库存撤单个数占比',
            '库存撤单金额',
            '总销售订单金额',
            '库存撤单金额占比',
            '年月',
            '最后更新时间',
        ];
    }

    public function map($user): array
    {
        return [
            $user->cancel_orders_qty,
            $user->total_orders_qty,
            $user->cancel_orders_qty_rate,
            $user->cancel_orders_amount,
            $user->total_orders_amount,
            $user->cancel_orders_amount_rate,
            $user->orders_export_month,
            $user->updated_at,
        ];
    }
}
