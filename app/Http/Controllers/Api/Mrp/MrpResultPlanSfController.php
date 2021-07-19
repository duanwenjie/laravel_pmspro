<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpResultPlanSfResource;
use App\Services\Report\MrpResultPlanSfService;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP SF-》自动补货建议（HS）*/

class MrpResultPlanSfController extends Controller
{
    /**
     * 列表
     * @param  MrpResultPlanSfService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpResultPlanSfService $befDetailV3Service)
    {
        $resource = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpResultPlanSfResource::collection($resource));
    }

    /**
     * 导出
     * @param  MrpResultPlanSfService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpResultPlanSfService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
