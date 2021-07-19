<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//MRP(国内)-》撤单和缺货订单日统计
class MrpReportOosOrdersDAllV2Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            "撤单订单个数",
            "缺货订单个数",
            "撤单订单金额",
            "缺货订单金额",
            "日期",
            "最后更新时间"
        ];
    }

    public function map($one): array
    {
        return [
            $one->cancel_orders_qty,
            $one->qh_orders_qty,
            $one->cancel_orders_amount,
            $one->qh_orders_amount,
            $one->orders_export_time,
            $one->updated_at,
        ];
    }
}
