<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDAllV2Resource;
use App\Services\Report\MrpReportOosOrdersDAllV2Service;
use Illuminate\Http\JsonResponse;

//MRP(国内)-》撤单和缺货订单日统计
class MrpReportOosOrdersDAllV2Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDAllV2Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDAllV2Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDAllV2Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDAllV2Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDAllV2Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
