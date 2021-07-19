<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpResultPlanV3Resource;
use App\Services\Report\MrpResultPlanV3Service;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP V3-》计算SKU自动补货*/

class MrpResultPlanV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpResultPlanV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpResultPlanV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpResultPlanV3Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpResultPlanV3Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpResultPlanV3Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
