<?php
/**
 * Description
 * User: dwj
 * Date: 2021/5/5
 * Time: 3:34 下午
 */

namespace App\Http\Controllers\Api\Server;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use App\Models\CurlApiLog;
use App\Models\MrpBaseData\BaseStockOrderUseQtyList;
use App\Services\MrpBaseData\WmsService;
use App\Tools\ApiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WmsStockController extends Controller
{

    /**
     * 接收WMS数据，更新wms占用库存，sku库存
     * @param  Request  $request
     * @return JsonResponse
     * @author dwj
     */
    public function updateSkuUseQty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data'                  => 'required|array',
            'data.*.newwms_use_qty' => 'required',
            'data.*.quantity'       => 'required',
            'data.*.sku'            => 'required|string',
            'data.*.warehouseid'    => 'required',
        ], [], [
            'data'                  => '参数',
            'data.*.newwms_use_qty' => 'WMS占用库存',
            'data.*.quantity'       => 'sku库存',
            'data.*.sku'            => 'SKU',
            'data.*.warehouseid'    => '仓库ID',
        ]);
        if ($validator->fails()) {
            $errorMsg = join('|', $validator->errors()->all());
            ApiLog::add(CurlApiLog::receiveWmsStockType, CurlApiLog::error, $errorMsg);
            throw new InvalidRequestException($errorMsg);
        }

        $data = $request['data'];
        DB::transaction(function () use ($data) {
            $temp = [];
            foreach ($data as $value) {
                $temp[] = [
                    'sku'            => $value['sku'],
                    'warehouseid'    => $value['warehouseid'],
                    'newwms_use_num' => $value['newwms_use_qty'],
                    'sku_num'        => $value['quantity'],
                ];
            }
            BaseStockOrderUseQtyList::query()->upsert($temp, ['sku', 'warehouseid']);
        });
        ApiLog::add(CurlApiLog::receiveWmsStockType, CurlApiLog::success);
        return $this->success();
    }

    /**
     * 接收WMS数据，更新wms离位库存
     * @param  Request  $request
     * @return JsonResponse
     * @author dwj
     */
    public function updateSkuLeaveQty(Request $request)
    {
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');

        $validator = Validator::make($request->all(), [
            'data'               => 'required|array',
            'data.*.leave_qty'   => 'required',
            'data.*.sku'         => 'required|string',
            'data.*.warehouseid' => 'required',
        ], [], [
            'data.*.leave_qty'   => '离位库存',
            'data.*.sku'         => 'SKU',
            'data.*.warehouseid' => '仓库ID',
        ]);
        if ($validator->fails()) {
            $errorMsg = join('|', $validator->errors()->all());
            ApiLog::add(CurlApiLog::receiveWmsStockType, CurlApiLog::error, $errorMsg);
            throw new InvalidRequestException($errorMsg);
        }

        $data = $request['data'];
        DB::transaction(function () use ($data) {
            $oldUpSku = WmsService::getOldUpSku(); // 离位库存大于0的数据
            $isUpSku = []; // 全量更新的数据
            $temp = [];
            foreach ($data as $value) {
                $warehousId = $value['warehouseid'];
                $isUpSku[] = $value['sku'].'_'.$warehousId;
                $temp[] = [
                    'sku'         => $value['sku'],
                    'warehouseid' => $warehousId,
                    'leave_num'   => $value['leave_qty'],
                ];
            }
            BaseStockOrderUseQtyList::query()->upsert($temp, ['sku', 'warehouseid']);
            // WMS全量推送离位库存数据，需将未在全量范围内的SKU离位库存数据初始化为0
            $noUpSku = array_diff($oldUpSku, $isUpSku);
            WmsService::initSkuLeaveNum($noUpSku);
        });

        ApiLog::add(CurlApiLog::receiveWmsStockType, CurlApiLog::success);
        return $this->success();
    }
}
