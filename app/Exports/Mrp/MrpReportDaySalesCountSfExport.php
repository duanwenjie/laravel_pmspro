<?php

namespace App\Exports\Mrp;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MrpReportDaySalesCountSfExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
        $column = [
            'id',
            'SKU',
        ];
        for ($i = 0; $i <= 29; $i++) {
            $d = $i;
            $beginDate = date("Y-m-d", strtotime("-$d day"));
            array_push($column, $beginDate);
        }
        array_push($column, '统计批次');
        array_push($column, '同步时间');
        return $column;
    }

    public function map($report): array
    {
        return [
            $report->id,//id
            $report->sku,//SKU
            $report->old_day_sales_1?:'0',//历史1天销量
            $report->old_day_sales_2?:'0',//历史2天销量
            $report->old_day_sales_3?:'0',//历史3天销量
            $report->old_day_sales_4?:'0',//历史4天销量
            $report->old_day_sales_5?:'0',//历史5天销量
            $report->old_day_sales_6?:'0',//历史6天销量
            $report->old_day_sales_7?:'0',//历史7天销量
            $report->old_day_sales_8?:'0',//历史8天销量
            $report->old_day_sales_9?:'0',//历史9天销量
            $report->old_day_sales_10?:'0',//历史10天销量
            $report->old_day_sales_11?:'0',//历史11天销量
            $report->old_day_sales_12?:'0',//历史12天销量
            $report->old_day_sales_13?:'0',//历史13天销量
            $report->old_day_sales_14?:'0',//历史14天销量
            $report->old_day_sales_15?:'0',//历史15天销量
            $report->old_day_sales_16?:'0',//历史16天销量
            $report->old_day_sales_17?:'0',//历史17天销量
            $report->old_day_sales_18?:'0',//历史18天销量
            $report->old_day_sales_19?:'0',//历史19天销量
            $report->old_day_sales_20?:'0',//历史20天销量
            $report->old_day_sales_21?:'0',//历史21天销量
            $report->old_day_sales_22?:'0',//历史22天销量
            $report->old_day_sales_23?:'0',//历史23天销量
            $report->old_day_sales_24?:'0',//历史24天销量
            $report->old_day_sales_25?:'0',//历史25天销量
            $report->old_day_sales_26?:'0',//历史26天销量
            $report->old_day_sales_27?:'0',//历史27天销量
            $report->old_day_sales_28?:'0',//历史28天销量
            $report->old_day_sales_29?:'0',//历史29天销量
            $report->old_day_sales_30?:'0',//历史30天销量
            $report->compute_batch,//统计批次
            $report->updated_at,//同步时间
        ];
    }
}
