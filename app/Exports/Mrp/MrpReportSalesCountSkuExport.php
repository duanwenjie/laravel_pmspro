<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//MRP(国内)-》MRP V3-》销量-SKU统计
class MrpReportSalesCountSkuExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            "ID",
            "SKU总计",
            "7天动销SKU",
            "14天动销SKU",
            "30天动销SKU",
            "60天动销SKU",
            "90天动销SKU",
            "180天动销SKU",
            "统计时间"
        ];
    }

    public function map($one): array
    {
        return [
            $one->id,
            $one->sku_count,
            $one->days_sku_count7,
            $one->days_sku_count14,
            $one->days_sku_count30,
            $one->days_sku_count60,
            $one->days_sku_count90,
            $one->days_sku_count180,
            $one->updated_at,
        ];
    }
}
