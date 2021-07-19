<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//MRP(国内)-》每日最新缺货占比统计报表
class MrpReportOosOrdersDV2Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            "缺货订单个数",
            "总销售订单个数",
            "缺货订单个数占比",
            "缺货订单金额",
            "总销售订单金额",
            "缺货订单金额占比",
            "日期",
            "最后更新时间"
        ];
    }

    public function map($one): array
    {
        return [
            $one->cancel_orders_qty,
            $one->total_orders_qty,
            $one->cancel_orders_qty_rate,
            $one->cancel_orders_amount,
            $one->total_orders_amount,
            $one->cancel_orders_amount_rate,
            $one->payment_date,
            $one->updated_at,
        ];
    }
}
