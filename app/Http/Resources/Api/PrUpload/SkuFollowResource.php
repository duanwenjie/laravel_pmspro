<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/25
 * Time: 5:30 下午
 */

namespace App\Http\Resources\Api\PrUpload;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class SkuFollowResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::SKU_FOLLOW_LIST:
                return [
                    'prId'                  => $this->pr_id, // PR单号
                    'prDate'                => $this->pr_date,
                    'sku'                   => $this->sku, // SKU
                    'skuName'               => $this->sku_name, // 中文名称
                    'skuSalesStatus'        => $this->sku_sales_status, // 销售状态
                    'skuSalesStatusName'    => $this->sku_sales_status_name, // 中文名称
                    'prQuantity'            => $this->pr_quantity, // PR数量(pcs)
                    'prRequireDate'         => formatDateItem($this->pr_require_date), // 需求日期
                    'planner'               => $this->planner, // 计划员
                    'purchase'              => $this->purchase, // 采购员
                    'po'                    => $this->po, // 采购单
                    'purchaseDate'          => formatDateItem($this->purchase_date), // PO生成日期
                    'quantity'              => $this->quantity, // 采购单数量(pcs)
                    'diffQuantity'          => $this->diff_quantity, // PO差异数量(pcs)
                    'supplyDate'            => formatDateItem($this->supply_date), // (PR+供货周期)日期
                    'arriveDate'            => formatDateItem($this->arrive_date), // 采购回复到货日期
                    'warehouseDate'         => formatDateItem($this->warehouse_date), // PO入库日期
                    'delayDays'             => $this->delay_days, // 入库延迟天数
                    'warehouseQuantity'     => $this->warehouse_quantity, // 入库数量(pcs)
                    'warehouseDiffQuantity' => $this->warehouse_diff_quantity, // 入库量差异
                    'pmsPoDetailStatus'     => $this->pms_po_detail_status, // PMS采购单明细状态
                    'pmsPoDetailStatusName' => $this->pms_po_detail_status_name, // PMS采购单明细状态描述
                    'prStatus'              => $this->pr_status, // PR状态
                    'prStatusName'          => $this->pr_status_name, // PR状态描述
                ];
                break;
            default:
                return [];
        }
    }
}
