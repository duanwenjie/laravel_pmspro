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

class PoBatchUploadResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();
        switch ($routeName) {
            case RouteName::PR_BATCH_UPLOAD_LIST:
                return [
                    'id'              => $this->id, // PR单号
                    'sku'             => $this->sku, // SKU
                    'spu'             => $this->spu, // SPU
                    'skuName'         => $this->skuName, // 中文名称
                    'quantity'        => $this->quantity, // 下单数
                    'warehouseName'   => $this->warehouse_name, // 仓别
                    'status'          => $this->status, // 状态
                    'statusName'      => $this->statusName, // 状态名称
                    'checkStatus'     => $this->check_status, // 检测状态
                    'checkStatusName' => $this->check_status_name, // 检测状态描述
                    'userNick'        => $this->user_nick, // 上传人
                    'purchaser'       => $this->purchaser, // 采购员
                    'createdAt'       => $this->created_at, // 上传时间
                    'requireDate'     => formatDateItem($this->require_date), // 需求时间
                    'oldPrId'         => $this->old_pr_id, // 采购及跟单
                    'po'              => $this->po, // 采购单号
                    'remark'          => $this->remark, // 备注
                    'checkResult'     => $this->check_result, // 原因结果
                    'noOrderReason'   => $this->no_order_reason, // 未下单原因
                ];
                break;
            default:
                return [];
        }
    }
}
