<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportSalesCountSkuResource;
use App\Services\Report\MrpReportSalesCountSkuService;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》MRP V3-》销量-SKU统计
class MrpReportSalesCountSkuController extends Controller
{

    /**
     * 列表
     * @param  MrpReportSalesCountSkuService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportSalesCountSkuService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportSalesCountSkuResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportSalesCountSkuService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportSalesCountSkuService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
