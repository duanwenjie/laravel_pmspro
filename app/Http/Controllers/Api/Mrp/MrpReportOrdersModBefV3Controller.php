<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrdersModBefV3Resource;
use App\ModelFilters\Mrp\MrpReportOrdersModBefV3Filter;
use App\Models\Mrp\MrpReportOrdersModBefV3;
use App\Services\Report\MrpReportOrdersModBefV3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MrpReportOrdersModBefV3Controller extends Controller
{
    /**
     * MRP(国内)-》MRP V3-》SKU日销量统计（修正前）报表 列表
     * 入参：{"data":{"sku":""}} 可带条件sku还有统计时间
     * 输出：json 太长了
     */
    public function list(Request $request)
    {
        $list = MrpReportOrdersModBefV3::query()->filter(
            $request->input('data', []),
            MrpReportOrdersModBefV3Filter ::class
        )
            ->paginate($request->input('perPage'));
        return $this->successForResourcePage(MrpReportOrdersModBefV3Resource::collection($list));
    }

    /**
     * MRP(国内)-》MRP V3-》SKU日销量统计（修正前）报表 导出
     * 入参：空
     * 输出：{
     * "state": "000001",
     * "msg": "导出成功",
     * "data": {
     * "url": "https://soter-test.youkeshu.com/yks/file/server/other/5C5683AB814E8498AE8748F21D403CFB_1620293107936.csv"
     * }
     * }
     * @param  MrpReportOrdersModBefV3Service  $mrpReportOrdersModBefV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrdersModBefV3Service $mrpReportOrdersModBefV3Service)
    {
        $url = $mrpReportOrdersModBefV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
