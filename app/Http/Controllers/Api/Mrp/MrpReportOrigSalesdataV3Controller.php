<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrigSalesdataV3Resource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpReportOrigSalesdataModV3Service;
use App\Services\Report\MrpReportOrigSalesdataV3Service;
use Illuminate\Http\JsonResponse;

//销量源数据
class MrpReportOrigSalesdataV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOrigSalesdataV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrigSalesdataV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrigSalesdataV3Resource::collection($list));
    }


    /**
     * 导出
     * @param  MrpReportOrigSalesdataV3Service  $befDetailV3Service
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export()
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '销量源数据.csv',
            [new MrpReportOrigSalesdataV3Service(), 'exportAsync']
        );
        return $this->success('导出已加入队列');



    }
}
