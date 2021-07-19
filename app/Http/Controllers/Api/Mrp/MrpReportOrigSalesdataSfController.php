<?php

namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrigSalesdataSfResource;
use App\Services\Report\MrpReportOrigSalesdataSfService;
use Illuminate\Http\JsonResponse;

/*MRP(国内)-》MRP HS-》销量源数据（HS）*/

class MrpReportOrigSalesdataSfController extends Controller
{
    /**
     * 列表
     * @param  MrpReportOrigSalesdataSfService  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrigSalesdataSfService $befDetailV3Service)
    {
        $resource = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrigSalesdataSfResource::collection($resource));
    }

    /**
     * 导出
     * @param  MrpReportOrigSalesdataSfService  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrigSalesdataSfService $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
