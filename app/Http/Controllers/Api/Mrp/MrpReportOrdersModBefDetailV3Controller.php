<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrdersModBefDetailV3Resource;
use App\Services\Report\MrpReportOrdersModBefDetailV3Service;
use Illuminate\Http\JsonResponse;

/*修正后销售明细统计表*/

class MrpReportOrdersModBefDetailV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOrdersModBefDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrdersModBefDetailV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrdersModBefDetailV3Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOrdersModBefDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrdersModBefDetailV3Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
