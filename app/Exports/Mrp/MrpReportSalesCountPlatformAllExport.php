<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//MRP(国内)-》平台+SKU销量统计(不剔除)
class MrpReportSalesCountPlatformAllExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            "序号",
            "SKU",
            "平台",
            "7天销量",
            "14天销量",
            "28天销量",
            "累计待发销量",
            "统计时间"
        ];
    }

    public function map($one): array
    {
        return [
            $one->id,
            $one->sku,
            $one->platform_code,
            $one->day_sales_7,
            $one->day_sales_14,
            $one->day_sales_28,
            $one->total_sales,
            $one->updated_at,
        ];
    }
}
