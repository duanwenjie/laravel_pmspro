<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//MRP(国内)-》每日缺货订单明细
class MrpReportOosOrdersDetailDailyExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            "订单号",
            "SKU",
            "订单金额",
            "订单状态",
            "数量",
            "平台",
            "统计日期",
            "更新时间"
        ];
    }

    public function map($one): array
    {
        return [
            $one->package_code,
            $one->sku,
            $one->total_amount,
            $one->order_status,
            $one->item_count,
            $one->platform,
            $one->dw_date,
            $one->payment_date,
        ];
    }
}
