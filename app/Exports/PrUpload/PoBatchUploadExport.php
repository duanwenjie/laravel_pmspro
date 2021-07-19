<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/26
 * Time: 12:25 下午
 */

namespace App\Exports\PrUpload;

use App\Exports\Mrp\BaseExport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PoBatchUploadExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
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
            'SKU',
            'SPU',
            '下单数',
            '仓别',
            '状态',
            '检测状态',
            '上传人',
            '采购员',
            '上传时间',
            '需求时间',
            '原pr单号',
            '采购单号',
            '备注',
            '原因结果',
            '原因结果',
        ];
    }

    public function map($report): array
    {
        return [
            $report->id, // id
            $report->sku, // SKU
            $report->skuInfo['spu'] ?? '', // SPU
            $report->quantity, // 下单数
            $report->warehouse_name, // 仓别
            $report->statusName, // 状态名称
            $report->check_status_name, // 检测状态描述
            $report->user_nick, // 上传人
            $report->purchaser->nick ?? '', // 采购员
            $report->created_at, // 上传时间
            $report->require_date, // 需求时间
            $report->old_pr_id, // 采购及跟单
            $report->po, // 采购单号
            $report->remark, // 备注
            $report->check_result, // 原因结果
            $report->no_order_reason, // 未下单原因
        ];
    }
}
