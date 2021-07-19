<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportStockCountV3Export extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'SKU',
            '备货方式',
            '销售状态',
            '出单天数',
            '特定备货数量',
            '前14天总销量',
            'PR数',
            '未生成PO',
            '已建单且未打印',
            '采购在途',
            '可用库存',
            '实际库存数量',
            'WMS占用库存',
            '返仓离位库存',
            '总未发数量',
            '总可用库存',
            '采购单价',
            '订购点',
            'SKU入库标识',
            '计算批次',
            '统计时间'
        ];
    }

    public function map($report): array
    {
        return [
            $report->sku,
            $report->stock_way_desc,
            $report->sales_status_desc,
            $report->order_day_times_14,
            $report->fixed_stock_num,
            $report->day_sales_14,
            $report->pr_count,
            $report->pr_no_po,
            $report->pr_po_no_print,
            $report->purchase_on_way_num,
            $report->available_stock,
            $report->act_stock,
            $report->newwms_use_qty,
            $report->leave_qty,
            $report->occupy_stock,
            $report->total_stock,
            $report->price,
            $report->order_point,
            $report->sku_ware_record,
            $report->compute_batch,
            $report->updated_at,
        ];
    }
}
