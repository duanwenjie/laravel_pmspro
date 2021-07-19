<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/5
 * Time: 3:57 下午
 */

namespace App\Http\Requests\Api\Mrp;

use App\Http\Requests\Request;
use App\Tools\RouteName;

class MrpRequest extends Request
{
    public function rules()
    {
        $rules = [];
        $routeName = $this->route()->getName();
        switch ($routeName) {
            case RouteName::ROUTE_MRP_WMS_STOCK_SKU_USE_QTY:
                $rules = [
                    'data'                  => 'required|array',
                    'data.*.newwms_use_qty' => 'required|numeric',
                    'data.*.sku'            => 'required|string',
                    'data.*.warehouseid'    => 'required'
                ];
                break;
            case RouteName::ROUTE_MRP_WMS_STOCK_SKU_LEAVE_QTY:
                $rules = [
                    'data'               => 'required|array',
                    'data.*.leave_qty'   => 'required|numeric',
                    'data.*.sku'         => 'required|string',
                    'data.*.warehouseid' => 'required'
                ];
                break;
            case RouteName::ROUTE_MRP_WMS_ACTUAL_STOCK_SKU_QTY:
                $rules = [
                    'data'                 => 'required|array',
                    'data.*.type'          => 'required',
                    'data.*.quantity'      => 'required|numeric',
                    'data.*.sku'           => 'required|string',
                    'data.*.warehouseCode' => 'required'
                ];
                break;
            case RouteName::CANCELPO_OPERATE_ORDER_LISTS:
                $rules = [
                    'data'                           => 'required|array',
                    'data.baseNum'                 => 'required|numeric',
                    'data.cancelBaseNum'                 => 'required|numeric',

                ];
                break;
        }
        return $rules;
    }
}
