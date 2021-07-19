<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersDResource;
use App\Services\Report\MrpReportOosOrdersDService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportOosOrdersDController extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersDService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersDService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersDResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersDService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersDService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
