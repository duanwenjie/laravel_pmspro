<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOosOrdersMResource;
use App\Services\Report\MrpReportOosOrdersMService;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportOosOrdersMController extends Controller
{

    /**
     * 列表
     * @param  MrpReportOosOrdersMService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOosOrdersMService $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOosOrdersMResource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOosOrdersMService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOosOrdersMService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
