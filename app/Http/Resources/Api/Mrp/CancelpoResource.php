<?php
/**
 * notes
 * User: jiangshilin(jiangshilin@youkeshu.com)
 * Date: 2021/7/1
 * Time: 18:37
 */


namespace App\Http\Resources\Api\Mrp;


use App\Http\ConfigBase\ConfigBase;
use App\Services\Report\CancelpoService;
use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class CancelpoResource extends JsonResource
{

    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::CANCELPO_GET_ORDER_LISTS:
                return [
                    'id' => $this->id,
                    'po' => $this->po,
                    'sku' => $this->sku,
                    'quantity' => $this->quantity,
                    'state' => CancelpoService::STATE_ARR[$this->state],
                    'createTime' => $this->create_time,
                    'deliveryDate' => $this->delivery_date,
                    'wareQuantity' => $this->ware_quantity,
                    'lastWareDate' => $this->last_ware_date,
                    'warehouse' => ConfigBase::getWarehouseMap()[$this->warehouse_id],
                    'unQuantity' => $this->un_quantity,
                    'pr' => $this->pr_id,
                    'orderer' => $this->orderer,
                    'merchandiser' => $this->merchandiser,
                ];
                break;
            case RouteName::CANCELPO_GET_DETAIL_RESULT_LISTS:
                return [
                    'sku' => $this->sku,
                    'po' => $this->po,
                    'createTime' => $this->create_time,
                    'state' => CancelpoService::STATE_ARR[$this->state]??'',
                    'quantity' => $this->quantity,
                    'orderer' => $this->orderer,
                    'wareQuantity' => $this->ware_quantity,
                    'unQuantity' => $this->un_quantity,
                    'btNo' => $this->bt_no,
                    'cancelNumTotal' => $this->cancel_num_total,
                    'cancelNumPo' => $this->cancel_num_po,
                    'restCancelNum' => $this->rest_cancel_num,
                    'cancelNum' => $this->cancel_num,
                    'cancelStatus' => CancelpoService::CANCEL_STATUS[$this->cancel_status]??'',
                    'btCreateTime' => $this->bt_create_time,
                    'isShow' => CancelpoService::IS_SHOW[$this->is_show]??'',
                ];
                break;
            case RouteName::CANCELPO_GET_TOTAL_RESULT_LISTS:
                return [
                    'btNo' => $this->bt_no,
                    'sku' => $this->sku,
                    'prNum' => $this->pr_num,
                    'unQuantity' => $this->un_quantity,
                    'avlSkuQty' => $this->avl_sku_qty,
                    'modAftV3Qty' => $this->mod_aft_v3_qty,
                    'baseNum' => $this->base_num,
                    'cancelBaseNum' => $this->cancel_base_num,
                    'countCancelNum' => $this->count_cancel_num,
                    'countCancelDays' => $this->count_cancel_days,
                    'suCancelNum' => $this->su_cancel_num,
                    'btCreateTime' => $this->bt_create_time,
                ];
                break;
        }
    }

}
