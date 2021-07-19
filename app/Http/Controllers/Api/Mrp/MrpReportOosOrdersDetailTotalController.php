<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDetailTotalResource;
use App\Services\Report\MrpReportOosOrdersDetailTotalService;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》总缺货订单明细
class MrpReportOosOrdersDetailTotalController extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDetailTotalService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDetailTotalService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDetailTotalResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDetailTotalService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDetailTotalService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
