<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/26
 * Time: 12:25 下午
 */

namespace App\Exports\PrUpload;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Exports\Mrp\BaseExport;

class SkuFollowExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'PR单号',
            'PR时间',
            'SKU',
            'SKU名称',
            '销售状态',
            'PR数量',
            'PR需求日期',
            '计划员',
            '采购员',
            'PO单号',
            'PO生成日期',
            'PO数量（PCS）',
            'PO差异数量（PCS）',
            '(PR+供货周期)日期',
            '采购回复到货日期',
            'PO入库日期',
            '入库延迟（天）',
            '累计入库量(pcs)',
            '入库量差异',
            '订单SKU状态',
            'PR状态',
        ];
    }

    public function map($report): array
    {
        $report->pr_require_date = formatDateItem($report->pr_require_date);
        $report->purchase_date = formatDateItem($report->purchase_date);
        $report->supply_date = formatDateItem($report->supply_date);
        $report->arrive_date = formatDateItem($report->arrive_date);
        $report->warehouse_date = formatDateItem($report->warehouse_date);
        return [
            $report->pr_id, // PR单号
            $report->pr_date,
            $report->sku, // SKU
            $report->sku_name, // 中文名称
            $report->sku_sales_status_name, // 中文名称
            $report->pr_quantity, // PR数量(pcs)
            $report->pr_require_date, // 需求日期
            $report->planner, // 计划员
            $report->purchase, // 采购员
            $report->po, // 采购单
            $report->purchase_date, // PO生成日期
            $report->quantity, // 采购单数量(pcs)
            $report->diff_quantity, // PO差异数量(pcs)
            $report->supply_date, // (PR+供货周期)日期
            $report->arrive_date, // 采购回复到货日期
            $report->warehouse_date, // PO入库日期
            $report->delay_days, // 入库延迟天数
            $report->warehouse_quantity, // 入库数量(pcs)
            $report->warehouse_diff_quantity, // 入库量差异
            $report->pms_po_detail_status_name, // PMS采购单明细状态描述
            $report->pr_status_name, // PR状态描述
        ];
    }
}
