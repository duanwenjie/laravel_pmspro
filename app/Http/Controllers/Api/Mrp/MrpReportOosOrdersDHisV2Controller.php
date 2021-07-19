<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDHisV2Resource;
use App\Services\Report\MrpReportOosOrdersDHisV2Service;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》历史每日缺货占比统计表
class MrpReportOosOrdersDHisV2Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDHisV2Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDHisV2Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDHisV2Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDHisV2Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDHisV2Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
