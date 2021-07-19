<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpBaseSkuCoreV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            '自增ID',
            'SKU',
            'sku中文名称',
            '备货方式',
            '销售状态',
            '主仓库id',
            '价格',
            '安全库存天数',
            '活动库存天数',
            '特定备货数量',
            '交期',
            '库内库存天数',
            '补货天数',
            '备注',
            '产品标识',
            '创建人',
            '创建时间',
            '最后更新人',
            '最后更新时间',
        ];
    }

    public function map($report): array
    {
       return [
            $report->id,//id
            $report->sku,//SKU
            $report->skuInfo['cn_name'] ?? '',//中文名称
            $report->stock_way_name,//备货方式描述
            $report->sales_status_name,//销售状态描述
            $report->skuInfo['main_warehouseid'] ?? '',//主仓库id
            $report->sku_price,//价格
            $report->buffer_stock_cycle,//安全库存天数
            $report->active_stock_cycle,//活动库存天数
            $report->fixed_stock_num,//特定备货数量
            $report->supply_cycle,//交期
            $report->stock_advance_cycle,//库内库存天数
            $report->stock_cycle,//补货天数
            $report->remark,//备注
            $report->sku_mark,//产品标识
            $report->created_user,//创建人
            $report->created_at,//创建时间
            $report->updated_user,//最后更新人
            $report->updated_at,//最后更新时间
        ];
    }
}
