<?php


namespace App\Http\Controllers\Api\Mrp;

use App\Exceptions\InvalidRequestException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Mrp\MrpReportOrigSalesdataModV3Resource;
use App\Models\UserImportExportRecord;
use App\Services\ExportService;
use App\Services\Report\MrpBaseSkuCoreSfService;
use App\Services\Report\MrpReportOrigSalesdataModV3Service;
use Illuminate\Http\JsonResponse;

//销量源数据（修正后）
class MrpReportOrigSalesdataModV3Controller extends Controller
{

    /**
     * 列表
     * @param  MrpReportOrigSalesdataModV3Service  $befDetailV3Service
     * @return JsonResponse
     */
    public function list(MrpReportOrigSalesdataModV3Service $befDetailV3Service)
    {
        $list = $befDetailV3Service->list();
        return $this->successForResourcePage(MrpReportOrigSalesdataModV3Resource::collection($list));
    }


    /**
     * 导出
     * @return JsonResponse
     * @throws InvalidRequestException
     */
    public function export()
    {
        (new ExportService())->asyncExport(
            UserImportExportRecord::MODULE_GN_MRP,
            '销量源数据（修正后）.csv',
            [new MrpReportOrigSalesdataModV3Service(), 'exportAsync']
        );
        return $this->success('导出已加入队列');
    }
}
