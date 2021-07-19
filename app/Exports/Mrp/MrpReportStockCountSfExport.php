<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportStockCountSfExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'SKU',
            '备货方式',
            '销售状态',
            '出单次数',
            'PR数',
            '未生成PO',
            '已建单且未打印',
            '采购在途',
            '可用库存',
            '实际库存数量',
            'WMS占用库存',
            '总未发数量',
            '总可用库存',
            '入库标识',
            '计算批次',
            '统计时间',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id,//id
            $report->sku,//SKU
            $report->stock_way_name,//备货方式描述
            $report->sales_status_name,//销售状态描述
            $report->order_times,//出单次数
            $report->pr_count,//PR数
            $report->no_order_pr_num,//未生成PO
            $report->no_print_num,//已建单且未打印
            $report->purchase_on_way_num,//采购在途
            $report->available_stock_num,//可用库存
            $report->actual_stock_num,//实际库存数量
            $report->newwms_use_num,//WMS占用库存
            $report->occupy_stock_num,//总未发数量
            $report->total_stock_num,//总可用库存
            $report->sku_ware_record,//入库标识
            $report->compute_batch,//计算批次
            $report->updated_at,//统计时间

        ];
    }
}
