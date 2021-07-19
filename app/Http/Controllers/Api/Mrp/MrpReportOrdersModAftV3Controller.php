<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrdersModAftV3Resource;
use App\ModelFilters\Mrp\MrpReportOrdersModAftV3Filter;
use App\Models\Mrp\MrpReportOrdersModAftV3;
use App\Services\Report\MrpReportOrdersModAftV3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MrpReportOrdersModAftV3Controller extends Controller
{
    /**
     * MRP(国内)-》MRP V3-》SKU销量统计（修正后） 列表
     * @param  Request  $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $list = MrpReportOrdersModAftV3::query()->filter(
            $request->input('data', []),
            MrpReportOrdersModAftV3Filter ::class
        )
            ->paginate($request->input('perPage'));
        return $this->successForResourcePage(MrpReportOrdersModAftV3Resource::collection($list));
    }

    /**
     * MRP(国内)-》MRP V3-》SKU销量统计（修正后） 导出
     * @param  MrpReportOrdersModAftV3Service  $mrpReportOrdersModAftV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrdersModAftV3Service $mrpReportOrdersModAftV3Service)
    {
        $url = $mrpReportOrdersModAftV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
