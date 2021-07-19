<?php

namespace App\Http\Resources\Api\Mrp;

use App\Tools\RouteName;
use Illuminate\Http\Resources\Json\JsonResource;

class MrpBaseSkuCoreSfResource extends JsonResource
{
    public function toArray($request)
    {
        $routeName = $request->route()->getName();

        switch ($routeName) {
            case RouteName::MRP_MRP_BASE_SKU_CORE_SF_LIST:
                return [
                    'id'               => $this->id,//id
                    'sku'              => $this->sku,//SKU
                    'skuName'          => $this->skuInfo['cn_name']??'',//中文名称
                    'stockWay'         => $this->stock_way,//备货方式
                    'stockWayName'     => $this->stock_way_name,//备货方式描述
                    'salesStatus'      => $this->sales_status,//销售状态
                    'salesStatusName'  => $this->sales_status_name,//销售状态描述
                    'mainWarehouseid'  => $this->skuInfo['main_warehouseid']??'',//主仓库id
                    'fixedStockNum'    => $this->fixed_stock_num,//特定备货数量
                    'bufferStockCycle' => $this->buffer_stock_cycle,//安全库存天数
                    'supplyCycle'      => $this->supply_cycle,//交期
                    'skuMark'          => $this->sku_mark,//产品标识
                    'createdUser'      => $this->created_user,//创建人
                    'plannerNick'      => $this->skuInfo['planner_nick']??'',//计划员
                    'createdAt'        => $this->created_at,//创建时间
                    'updatedUser'      => $this->updated_user,//最后更新人
                    'updatedAt'        => $this->updated_at,//最后更新时间

                ];
                break;
            case RouteName::MRP_MRP_BASE_SKU_CORE_V3_LIST:
                return [
                    'id'                => $this->id,//id
                    'sku'               => $this->sku,//SKU
                    'skuName'           => $this->skuInfo['cn_name']??'',//中文名称
                    'stockWay'          => $this->stock_way,//备货方式
                    'stockWayName'      => $this->stock_way_name,//备货方式描述
                    'salesStatus'       => $this->sales_status,//销售状态
                    'salesStatusName'   => $this->sales_status_name,//销售状态描述
                    'mainWarehouseid'   => $this->skuInfo['main_warehouseid']??'',//主仓库id
                    'skuPrice'          => $this->sku_price,//价格
                    'bufferStockCycle'  => $this->buffer_stock_cycle,//安全库存天数
                    'activeStockCycle'  => $this->active_stock_cycle,//活动库存天数
                    'fixedStockNum'     => $this->fixed_stock_num,//特定备货数量
                    'supplyCycle'       => $this->supply_cycle,//交期
                    'stockAdvanceCycle' => $this->stock_advance_cycle,//库内库存天数
                    'stockCycle'        => $this->stock_cycle,//补货天数
                    'remark'            => $this->remark,//备注
                    'skuMark'           => $this->sku_mark,//产品标识
                    'createdUser'       => $this->created_user,//创建人
                    'createdAt'         => $this->created_at,//创建时间
                    'updatedUser'       => $this->updated_user,//最后更新人
                    'updatedAt'         => $this->updated_at,//最后更新时间
                ];
                break;
            default:
                return [];
        }
    }
}
