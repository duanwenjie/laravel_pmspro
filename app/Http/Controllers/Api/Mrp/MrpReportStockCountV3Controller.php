<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportStockCountV3Resource;
use App\ModelFilters\Mrp\MrpReportStockCountV3Filter;
use App\Models\Mrp\MrpReportStockCountV3;
use App\Services\Report\MrpReportStockCountV3Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MrpReportStockCountV3Controller extends Controller
{
    /**
     * MRP(国内)-》MRP V3-》SKU库存统计 列表
     * @param  Request  $request
     * @return JsonResponse
     */
    public function list(Request $request)
    {
        $list = MrpReportStockCountV3::query()->filter($request->input('data', []), MrpReportStockCountV3Filter::class)
            ->paginate($request->input('perPage'));
        return $this->successForResourcePage(MrpReportStockCountV3Resource::collection($list));
    }


    /**
     * MRP(国内)-》MRP V3-》SKU库存统计 导出
     * @param  MrpReportStockCountV3Service  $mrpReportStockCountV3Service
     * @return JsonResponse
     */
    public function export(MrpReportStockCountV3Service $mrpReportStockCountV3Service)
    {
        $url = $mrpReportStockCountV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
