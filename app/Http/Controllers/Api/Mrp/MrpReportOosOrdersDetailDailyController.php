<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDetailDailyResource;
use App\Services\Report\MrpReportOosOrdersDetailDailyService;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》每日缺货订单明细
class MrpReportOosOrdersDetailDailyController extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDetailDailyService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDetailDailyService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDetailDailyResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDetailDailyService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDetailDailyService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
