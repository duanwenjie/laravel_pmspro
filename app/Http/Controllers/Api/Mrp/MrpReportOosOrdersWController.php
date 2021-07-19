<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersWResource;
use App\Services\Report\MrpReportOosOrdersWService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportOosOrdersWController extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersWService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersWService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersWResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersWService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersWService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
