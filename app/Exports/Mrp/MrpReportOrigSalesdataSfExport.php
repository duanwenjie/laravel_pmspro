<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportOrigSalesdataSfExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '包裹单号',
            'SKU',
            '数量',
            '平台',
            '仓库名称',
            '仓库ID',
            '创建时间（进ERP）',
            '付款时间',
            '创建时间（平台）',
            '出库时间',
            '发货时间',
            '计算批次',
            '统计时间',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,
            $report->package_code,
            $report->sku,
            $report->item_count,
            $report->platform,
            $report->warehouse,
            $report->warehouseid,
            $report->orders_export_time,
            $report->payment_date,
            $report->order_create_time,
            $report->out_time,
            $report->orders_out_time,
            $report->compute_batch,
            $report->updated_at,
        ];
    }
}
