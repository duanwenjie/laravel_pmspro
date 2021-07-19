<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDV2Resource;
use App\Services\Report\MrpReportOosOrdersDV2Service;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》每日最新缺货占比统计报表
class MrpReportOosOrdersDV2Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDV2Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDV2Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDV2Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDV2Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDV2Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
