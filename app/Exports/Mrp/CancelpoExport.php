<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/2
 * Time: 9:40
 */


namespace App\Exports\Mrp;


use App\Http\ConfigBase\ConfigBase;
use App\Services\Report\CancelpoService;
use App\Tools\RouteName;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CancelpoExport extends BaseExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    protected $builder;
    protected $routeName;

    public function __construct($builder,$request)
    {
        $this->builder = $builder;
        $this->routeName = $request->route()->getName();;
    }

    public function query()
    {
        return $this->builder;
    }

    public function headings(): array
    {
        switch ($this->routeName) {
            case RouteName::CANCELPO_EXPORT_ORDER_LISTS:
                return [
                    '序号',
                    '采购单号',
                    'SKU',
                    '采购数量',
                    '订单SKU状态',
                    '采购时间',
                    '预计到货日期',
                    '入库数量',
                    '最近入库日期',
                    '目的仓',
                    '未交量',
                    'PR单号',
                    '订货员',
                    '跟单员',
                ];
                break;
            case RouteName::CANCELPO_EXPORT_DETAIL_RESULT_LISTS:
                return [
                    'SKU',
                    '采购单号',
                    '采购时间',
                    '订单SKU状态',
                    '采购数量',
                    '订货员',
                    '入库数量',
                    '未交量',
                    '计算批次编号',
                    '撤销总量',
                    '本次应撤销量',
                    '待撤销总量',
                    '取消数量',
                    '是否撤销',
                    '计算时间',
                    '是否同步pms'
                ];
                bareak;
            case RouteName::CANCELPO_EXPORT_TOTAL_RESULT_LISTS:
                return [
                    '计算批次编号',
                    'SKU',
                    'PR数',
                    '未交量',
                    '可用库存',
                    '日均销量',
                    '可销基数',
                    '撤销可销基数',
                    '计算撤销量',
                    '撤销量可销天数',
                    '建议撤销量',
                    '计算时间',
                ];
                bareak;
        }
    }

    public function map($report): array
    {
        switch ($this->routeName) {
            case RouteName::CANCELPO_EXPORT_ORDER_LISTS:
                return [
                    $report->id,
                    $report->po,
                    $report->sku,
                    $report->quantity,
                    CancelpoService::STATE_ARR[$report->state] ?? $report->state,
                    $report->create_time,
                    $report->delivery_date,
                    $report->ware_quantity,
                    $report->last_ware_date,
                    ConfigBase::getWarehouseMap()[$report->warehouse_id]??'',
                    $report->un_quantity,
                    $report->pr_id,
                    $report->orderer,
                    $report->merchandiser,
                ];
                break;
            case RouteName::CANCELPO_EXPORT_DETAIL_RESULT_LISTS:
                return [
                    $report->sku,
                    $report->po,
                    $report->create_time,
                    CancelpoService::STATE_ARR[$report->state] ?? $report->state,
                    $report->quantity,
                    $report->orderer,
                    $report->ware_quantity,
                    $report->un_quantity,
                    $report->bt_no,
                    $report->cancel_num_total,
                    $report->cancel_num_po,
                    $report->rest_cancel_num,
                    $report->cancel_num,
                    CancelpoService::CANCEL_STATUS[$report->cancel_status]??'',
                    $report->bt_create_time,
                    CancelpoService::IS_SHOW[$report->is_show]??'',
                ];
                break;
            case RouteName::CANCELPO_EXPORT_TOTAL_RESULT_LISTS:
                return [
                    $report->bt_no,
                    $report->sku,
                    $report->pr_num,
                    $report->un_quantity,
                    $report->avl_sku_qty,
                    $report->mod_aft_v3_qty,
                    $report->base_num,
                    $report->cancel_base_num,
                    $report->count_cancel_num,
                    $report->count_cancel_days,
                    $report->su_cancel_num,
                    $report->bt_create_time,
                ];
                break;
        }
    }
}
