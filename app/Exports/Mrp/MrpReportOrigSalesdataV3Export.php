<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

//销量源数据
class MrpReportOrigSalesdataV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '成本价',
            '未做任何修正的日均',
            '近14天修正后日均',
            '出单账号',
            '销售团队',
            '总监账号',
            '经理账号',
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
            $record->platform,
            $record->price,
            $record->avg_day_sales,
            $record->nearly14days_qty,
            $record->sales_account,
            $record->business_type,
            $record->zg_account,//@todo 新系统字段建错，暂时用主管字段存储总监账号
            $record->jl_account,
            $record->warehouseid,
            $record->payment_date,
            $record->compute_batch,
            $record->updated_at,
        ];
    }
}
