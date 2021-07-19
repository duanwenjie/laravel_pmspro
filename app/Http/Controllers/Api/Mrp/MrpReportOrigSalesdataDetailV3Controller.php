<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrigSalesdataDetailV3Resource;
use App\Services\Report\MrpReportOrigSalesdataDetailV3Service;
use Illuminate\Http\JsonResponse;

/*销量源数据*/

class MrpReportOrigSalesdataDetailV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOrigSalesdataDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrigSalesdataDetailV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrigSalesdataDetailV3Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOrigSalesdataDetailV3Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export(MrpReportOrigSalesdataDetailV3Service $befDetailV3Service)
    {
        $url = $befDetailV3Service->export();
        return $this->success('导出成功', ['url' => $url]);
    }
}
