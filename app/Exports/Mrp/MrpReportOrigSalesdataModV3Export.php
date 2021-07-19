<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//销量源数据（修正后）
class MrpReportOrigSalesdataModV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'id	',
            '订单号',
            'SKU',
            '数量',
            '平台',
            '仓库ID',
            '付款时间',
            '计算批次',
            '统计时间',
        ];
    }

    public function map($record): array
    {
        return [
            $record->id,
            $record->package_code,
            $record->sku,
            $record->item_count,
            $record->platform_code,
            $record->warehouseid,
            $record->payment_date,
            $record->compute_batch,
            $record->updated_at,
        ];
    }
}
