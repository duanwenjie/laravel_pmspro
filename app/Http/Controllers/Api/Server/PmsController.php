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
use App\Services\PrUpload\PmsService;
use App\Tools\ApiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PmsController extends Controller
{

    /**
     * PMS取消PR单
     * @param  Request  $request
     * @return JsonResponse
     * @author dwj
     */
    public function cancelPr(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['pr_id' => 'required'], [], ['pr_id' => 'PR单号']);

            if ($validator->fails()) {
                throw new InvalidRequestException(join('|', $validator->errors()->all()));
            }

            // 添加事务
            $res = [];
            DB::transaction(function () use ($request, &$res) {
                $res = PmsService::cancelPrByPms($request);
            });

            ApiLog::add(CurlApiLog::receivePmsPrType, CurlApiLog::success);
            return response()->json($res);
        } catch (Throwable $e) {
            ApiLog::add(CurlApiLog::receivePmsPrType, CurlApiLog::error, $e->getMessage());
            return response()->json(['code' => '000002', 'msg' => $e->getMessage()]);
        }
    }

    /**
     * PMS下单成功接收PR单数据
     * @param  Request  $request
     * @return JsonResponse
     * @author dwj
     */
    public function receivePrData(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                    'list'         => 'required|array',
                    'list.*.pr_id' => 'required',
                    'list.*.po'    => 'required',
                ],
                [],
                [
                    'list'         => 'PR单数据',
                    'list.*.pr_id' => 'PR单号',
                    'list.*.po'    => '采购单号',
                ]
            );

            if ($validator->fails()) {
                throw new InvalidRequestException(join('|', $validator->errors()->all()));
            }

            $res = PmsService::receivePrDataByPms($request);
            ApiLog::add(CurlApiLog::receivePmsPrType, CurlApiLog::success);
            return response()->json($res);
        } catch (Throwable $e) {
            ApiLog::add(CurlApiLog::receivePmsPrType, CurlApiLog::error, $e->getMessage());
            return response()->json(['code' => '000002', 'msg' => $e->getMessage()]);
        }
    }
}
